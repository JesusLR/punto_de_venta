<?php

namespace Tests\Feature;

use App\Egreso;
use App\Producto;
use App\Reparacion;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReparacionesTest extends TestCase
{
    private $uno;
    private $dos;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        foreach ([
            '2014_10_12_000000_create_users_table.php' => 'CreateUsersTable',
            '2020_03_04_190205_create_productos_table.php' => 'CreateProductosTable',
            '2026_11_10_000005_create_producto_imagenes_table.php' => 'CreateProductoImagenesTable',
            '2020_03_10_014423_create_clientes_table.php' => 'CreateClientesTable',
            '2026_06_26_000002_create_notificaciones_leidas_table.php' => 'CreateNotificacionesLeidasTable',
            '2026_04_09_000001_create_egresos_table.php' => 'CreateEgresosTable',
            '2026_06_30_143853_create_ingresos_table.php' => 'CreateIngresosTable',
            '2026_06_22_000001_create_permissions_system_tables.php' => 'CreatePermissionsSystemTables',
            '2026_11_09_000001_create_reparaciones_internas.php' => 'CreateReparacionesInternas',
            '2026_11_09_000002_add_nombre_to_reparaciones.php' => 'AddNombreToReparaciones',
            '2026_11_09_000004_add_cancelacion_to_reparaciones.php' => 'AddCancelacionToReparaciones',
            '2026_11_10_000001_add_reparaciones_externas.php' => 'AddReparacionesExternas',
            '2026_11_10_000002_link_external_repairs_to_clientes.php' => 'LinkExternalRepairsToClientes',
            '2026_11_10_000003_add_etapa_to_reparacion_imagenes.php' => 'AddEtapaToReparacionImagenes',
            '2026_11_10_000004_add_view_all_reparaciones_permission.php' => 'AddViewAllReparacionesPermission',
        ] as $file => $class) {
            require_once database_path('migrations/'.$file);
            (new $class)->up();
        }
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('lActivo')->default(1);
            $table->string('img')->nullable();
            foreach (['id_categoria', 'id_material', 'id_proveedor'] as $field) $table->integer($field)->default(0);
        });
        foreach (['categorias' => 'cNombreCategoria', 'materiales' => 'cNombreMaterial', 'proveedores' => 'cNombreProveedor'] as $name => $label) {
            Schema::create($name, function (Blueprint $table) use ($label) { $table->id(); $table->string($label); $table->boolean('lActivo')->default(1); });
        }
        Schema::create('homepage_settings', function (Blueprint $table) { $table->id(); $table->string('key'); $table->text('value')->nullable(); });
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'test']));
        $this->uno = Producto::create(['codigo_barras' => 'UNO', 'descripcion' => 'Anillo', 'precio_compra' => 100, 'precio_venta' => 200, 'existencia' => 1]);
        $this->dos = Producto::create(['codigo_barras' => 'DOS', 'descripcion' => 'Cadena', 'precio_compra' => 200, 'precio_venta' => 400, 'existencia' => 4]);
    }

    private function data(): array
    {
        return ['solicitud_id' => (string) Str::uuid(), 'total' => '150.75', 'observaciones' => 'Soldar y pulir', 'productos' => [
            ['producto_id' => $this->uno->id, 'cantidad' => 1], ['producto_id' => $this->dos->id, 'cantidad' => 2],
        ]];
    }

    public function test_full_internal_flow_removes_stock_hides_catalog_and_returns_stock_with_one_expense()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $reparacion = Reparacion::firstOrFail();
        $this->assertEquals(0, $this->uno->fresh()->existencia);
        $this->assertEquals(2, $this->dos->fresh()->existencia);
        $this->assertSame(2, $reparacion->piezas()->count());
        $this->assertSame(0, Egreso::count());
        $catalogo = $this->getJson('/api/verCategoria/0/0')->assertOk()->json('lstProducto');
        $this->assertSame([$this->dos->id], array_column($catalogo, 'id'));
        $this->get('/api/catalogoProductos')->assertOk()->assertViewHas('lstProducto', function ($productos) {
            return count($productos) === 1 && (int) $productos[0]->id === $this->dos->id;
        });
        $this->get(route('reparaciones.index'))->assertOk()->assertSee('Soldar y pulir')->assertSee('Marcar entregado');
        $this->postJson(route('reparaciones.entregar', $reparacion), [])->assertOk();
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $this->assertEquals(4, $this->dos->fresh()->existencia);
        $this->assertSame('ENTREGADO', $reparacion->fresh()->estado);
        $this->assertNotNull($reparacion->fresh()->entregado_at);
        $this->assertEquals('150.75', Egreso::first()->monto);
        $this->assertSame('Reparación interna #'.$reparacion->id, Egreso::first()->concepto);
        $this->assertEquals(Egreso::first()->id, $reparacion->fresh()->id_egreso);
        $this->postJson(route('reparaciones.entregar', $reparacion), [])->assertOk();
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $this->assertSame(1, Egreso::count());
    }

    public function test_repeated_submission_does_not_withdraw_twice()
    {
        $data = $this->data();
        $this->postJson(route('reparaciones.store'), $data)->assertOk();
        $this->postJson(route('reparaciones.store'), $data)->assertOk();
        $this->assertSame(1, Reparacion::count());
        $this->assertEquals(0, $this->uno->fresh()->existencia);
        $this->assertEquals(2, $this->dos->fresh()->existencia);
    }

    public function test_cart_created_before_repair_cannot_sell_reserved_stock()
    {
        $pieza = clone $this->uno; $pieza->cantidad = 1;
        $this->withSession(['productos' => [$pieza]]);
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $this->postJson(route('terminarOCancelarVenta'), ['accion' => 'terminar', 'tipo_pago' => 'EFECTIVO', 'userID' => 1])
            ->assertStatus(422)->assertJsonValidationErrors('productos');
        $this->assertEquals(0, $this->uno->fresh()->existencia);
    }

    public function test_received_pieces_can_be_sold_normally()
    {
        foreach (['2020_03_05_003009_create_ventas_table.php' => 'CreateVentasTable', '2020_03_05_003110_create_producto_vendidos_table.php' => 'CreateProductoVendidosTable'] as $file => $class) {
            require_once database_path('migrations/'.$file); (new $class)->up();
        }
        Schema::table('ventas', function (Blueprint $table) {
            $table->integer('id_cliente')->nullable(); $table->integer('id_usuario')->nullable();
            $table->string('tipo_pago')->nullable(); $table->string('cNombreVenta')->nullable();
        });
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertOk();
        $pieza = $this->uno->fresh(); $pieza->cantidad = 1;
        $this->withSession(['productos' => [$pieza]])->post(route('terminarOCancelarVenta'), [
            'accion' => 'terminar', 'tipo_pago' => 'EFECTIVO', 'userID' => 1,
        ])->assertRedirect(route('vender.index'))->assertSessionHas('mensaje', 'Venta terminada');
        $this->assertSame(1, DB::table('ventas')->count());
        $this->assertSame(1, DB::table('productos_vendidos')->count());
        $this->assertEquals(0, $this->uno->fresh()->existencia);
        $this->assertSame(1, Egreso::count());
    }

    public function test_inactive_missing_products_and_empty_repairs_are_rejected()
    {
        $this->uno->lActivo = 0; $this->uno->save();
        $this->postJson(route('reparaciones.store'), $this->data())->assertStatus(422);
        $data = $this->data(); $data['productos'][0]['producto_id'] = 999;
        $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        $data['productos'] = [];
        $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        $this->assertSame(0, Reparacion::count());
        $this->assertEquals(4, $this->dos->fresh()->existencia);
    }

    public function test_insufficient_stock_rolls_back_every_piece()
    {
        $data = $this->data(); $data['productos'][1]['cantidad'] = 5;
        $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        $this->assertSame(0, Reparacion::count());
        $this->assertSame(0, DB::table('reparacion_productos')->count());
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $this->assertEquals(4, $this->dos->fresh()->existencia);
    }

    public function test_multiple_pending_repairs_are_counted_in_product_grid()
    {
        $data = $this->data(); $data['productos'] = [['producto_id' => $this->dos->id, 'cantidad' => 1]];
        $this->postJson(route('reparaciones.store'), $data)->assertOk();
        $data['solicitud_id'] = (string) Str::uuid();
        $this->postJson(route('reparaciones.store'), $data)->assertOk();
        $params = ['cTipoBusqueda' => 'T', 'cTipoBusquedaProveedor' => 'T', 'cTipoBusquedaMaterial' => 'T', 'cTipoBusquedaCategoria' => 'T'];
        $rows = $this->postJson(route('gridProductos'), $params)->assertOk()->json('rows');
        $this->assertNotNull($rows);
        $this->assertEquals(2, collect($rows)->firstWhere('id', $this->dos->id)['unidades_reparacion']);
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertOk();
        $rows = $this->postJson(route('gridProductos'), $params)->assertOk()->json('rows');
        $this->assertEquals(1, collect($rows)->firstWhere('id', $this->dos->id)['unidades_reparacion']);
        $this->assertEquals(3, $this->dos->fresh()->existencia);
    }

    public function test_duplicate_products_zero_negative_or_fractional_units_and_invalid_total_are_rejected()
    {
        $data = $this->data(); $data['productos'][1]['producto_id'] = $this->uno->id;
        $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        foreach ([0, -1, 1.5] as $cantidad) {
            $data = $this->data(); $data['productos'][0]['cantidad'] = $cantidad;
            $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        }
        $data = $this->data(); $data['total'] = '-1';
        $this->postJson(route('reparaciones.store'), $data)->assertStatus(422);
        $this->assertSame(0, Reparacion::count());
    }

    public function test_failed_expense_creation_rolls_back_returned_inventory_and_status()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        Egreso::creating(function () { throw new \RuntimeException('Expense unavailable'); });
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertStatus(500);
        $this->assertSame('EN_REPARACION', Reparacion::first()->estado);
        $this->assertEquals(0, $this->uno->fresh()->existencia);
        $this->assertEquals(2, $this->dos->fresh()->existencia);
        $this->assertSame(0, Egreso::count());
    }

    public function test_repairs_expense_cannot_be_deleted()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertOk();
        $this->deleteJson(route('finanzas.destroy', Egreso::first()->id))->assertStatus(422);
        $this->assertSame(1, Egreso::count());
    }

    public function test_permissions_protect_module_search_registration_and_delivery()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $user = User::create(['name' => 'Lectura', 'email' => 'reader@test.local', 'password' => 'test']);
        $this->actingAs($user);
        $this->getJson(route('reparaciones.index'))->assertStatus(403);
        $this->getJson(route('reparaciones.productos'))->assertStatus(403);
        $this->postJson(route('reparaciones.store'), $this->data())->assertStatus(403);
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertStatus(403);
        $this->postJson(route('reparaciones.nombre', Reparacion::first()), ['nombre' => 'No permitido'])->assertStatus(403);
        $this->postJson(route('reparaciones.cancelar', Reparacion::first()), [])->assertStatus(403);
        $roleId = DB::table('roles')->where('slug', 'cajero')->value('id');
        DB::table('permission_role')->insert(['role_id' => $roleId, 'permission_id' => DB::table('permissions')->where('slug', 'ver_reparaciones')->value('id')]);
        $user->role_id = $roleId; $user->save(); $user->unsetRelation('role');
        $this->get(route('reparaciones.index'))->assertOk()->assertDontSee('id="formReparacion"', false)->assertDontSee('btn-entregar-reparacion');
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertStatus(403);
        $this->assertEquals(0, $this->uno->fresh()->existencia);
    }

    public function test_name_can_be_set_renamed_after_delivery_or_cleared_without_stock_or_expense_amount_changes()
    {
        $this->postJson(route('reparaciones.store'), $this->data() + ['nombre' => 'Soldadura de anillos'])->assertOk();
        $reparacion = Reparacion::firstOrFail();
        $this->assertSame('Soldadura de anillos', $reparacion->nombre);
        $this->postJson(route('reparaciones.nombre', $reparacion), ['nombre' => str_repeat('a', 151)])->assertStatus(422);
        $this->postJson(route('reparaciones.entregar', $reparacion), [])->assertOk();
        $egreso = Egreso::first()->toArray();
        $this->assertSame('Soldadura de anillos', $egreso['concepto']);
        $this->postJson(route('reparaciones.nombre', $reparacion), ['nombre' => 'Reparación recibida'])->assertOk();
        $this->assertSame('Reparación recibida', $reparacion->fresh()->nombre);
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $actual = Egreso::first()->toArray();
        $this->assertSame('Reparación recibida', $actual['concepto']);
        unset($egreso['concepto'], $egreso['updated_at'], $actual['concepto'], $actual['updated_at']);
        $this->assertSame($egreso, $actual);
        $this->postJson(route('reparaciones.nombre', $reparacion), ['nombre' => ''])->assertOk();
        $this->assertNull($reparacion->fresh()->nombre);
        $this->assertSame('Reparación interna #'.$reparacion->id, Egreso::first()->concepto);
    }

    public function test_pieces_are_displayed_in_a_modal_instead_of_an_expanded_row()
    {
        $this->postJson(route('reparaciones.store'), $this->data() + ['nombre' => '<b>Mi reparación</b>'])->assertOk();
        $this->get(route('reparaciones.index'))->assertOk()
            ->assertSee('data-toggle="modal" data-target="#detalleReparacion1"', false)
            ->assertSee('class="modal fade apartado-modal reparacion-detalle-modal"', false)
            ->assertSee('&lt;b&gt;Mi reparación&lt;/b&gt;', false)
            ->assertDontSee('<tr class="collapse"', false)
            ->assertSee('Anillo')->assertSee('Cadena');
    }

    public function test_cancel_returns_units_once_without_expense_and_cannot_be_delivered()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $reparacion = Reparacion::first();
        $this->postJson(route('reparaciones.cancelar', $reparacion), [])->assertOk();
        $this->assertSame('CANCELADO', $reparacion->fresh()->estado);
        $this->assertNotNull($reparacion->fresh()->cancelado_at);
        $this->assertEquals(1, $reparacion->fresh()->id_usuario_cancelacion);
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $this->assertEquals(4, $this->dos->fresh()->existencia);
        $this->assertSame(0, Egreso::count());
        $this->postJson(route('reparaciones.cancelar', $reparacion), [])->assertOk();
        $this->assertEquals(1, $this->uno->fresh()->existencia);
        $this->postJson(route('reparaciones.entregar', $reparacion), [])->assertStatus(422);
        $this->assertSame(0, Egreso::count());
    }

    public function test_delivered_repair_cannot_be_cancelled()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $reparacion = Reparacion::first();
        $this->postJson(route('reparaciones.entregar', $reparacion), [])->assertOk();
        $this->postJson(route('reparaciones.cancelar', $reparacion), [])->assertStatus(422);
        $this->assertSame('ENTREGADO', $reparacion->fresh()->estado);
        $this->assertSame(1, Egreso::count());
        $this->assertEquals(1, $this->uno->fresh()->existencia);
    }

    public function test_filter_defaults_to_pending_but_all_and_cancelled_are_available()
    {
        $this->postJson(route('reparaciones.store'), $this->data())->assertOk();
        $this->postJson(route('reparaciones.cancelar', Reparacion::first()), [])->assertOk();
        $this->get(route('reparaciones.index'))->assertOk()->assertViewHas('estadoFiltro', 'EN_REPARACION')
            ->assertViewHas('reparaciones', function ($rows) { return $rows->total() === 0; });
        $this->get(route('reparaciones.index', ['estado' => 'CANCELADO']))->assertOk()
            ->assertViewHas('reparaciones', function ($rows) { return $rows->total() === 1; });
        $this->get(route('reparaciones.index').'?estado=')->assertOk()
            ->assertViewHas('reparaciones', function ($rows) { return $rows->total() === 1; });
    }

    public function test_existing_expense_names_are_reconciled_without_changing_amounts()
    {
        $this->postJson(route('reparaciones.store'), $this->data() + ['nombre' => 'Mi reparación'])->assertOk();
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertOk();
        DB::table('egresos')->update(['concepto' => 'Reparación interna #1']);
        require_once database_path('migrations/2026_11_09_000003_sync_repair_expense_names.php');
        $migration = new \SyncRepairExpenseNames; $migration->up(); $migration->up();
        $this->assertSame('Mi reparación', Egreso::first()->concepto);
        $this->assertEquals('150.75', Egreso::first()->monto);
        $this->assertSame(1, Egreso::count());
    }
}
