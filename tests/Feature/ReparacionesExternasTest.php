<?php

namespace Tests\Feature;

use App\Cliente;
use App\Egreso;
use App\Ingreso;
use App\Producto;
use App\Reparacion;
use App\ReparacionImagen;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReparacionesExternasTest extends TestCase
{
    private $cliente;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        foreach ([
            '2014_10_12_000000_create_users_table.php' => 'CreateUsersTable',
            '2020_03_04_190205_create_productos_table.php' => 'CreateProductosTable',
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
        ] as $file => $class) { require_once database_path('migrations/'.$file); (new $class)->up(); }
        Storage::fake('local');
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'test']));
        $this->cliente = Cliente::create(['nombre' => 'Cliente del catálogo', 'telefono' => '9991234567']);
        Producto::create(['codigo_barras' => 'STOCK', 'descripcion' => 'Inventario ajeno a reparación externa', 'precio_compra' => 100, 'precio_venta' => 200, 'existencia' => 3]);
    }

    private function datos(): array
    {
        return ['solicitud_id' => (string) Str::uuid(), 'contacto_origen' => 'manual', 'contacto_nombre' => 'María López',
            'contacto_telefono' => '9994459061', 'nombre' => 'Soldadura de cadena', 'observaciones' => "Cadena de oro rota.\nSoldar y pulir."];
    }

    public function test_manual_contact_is_saved_without_creating_accounts_or_moving_stock()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $reparacion = Reparacion::firstOrFail();
        $this->assertSame('EXTERNA', $reparacion->tipo);
        $this->assertSame('María López', $reparacion->contacto_nombre);
        $this->assertNull($reparacion->contacto_cliente_id);
        $this->assertSame(1, User::count()); $this->assertSame(1, Cliente::count());
        $this->assertEquals(3, Producto::first()->existencia);
        $this->assertSame(0, DB::table('reparacion_productos')->count());
        $this->assertSame(0, Egreso::count()); $this->assertSame(0, Ingreso::count());
    }

    public function test_catalog_uses_clients_name_and_phone_instead_of_submitted_name()
    {
        $data = $this->datos(); $data['contacto_origen'] = 'catalogo'; $data['contacto_cliente_id'] = $this->cliente->id;
        unset($data['contacto_telefono']);
        $this->postJson(route('reparaciones.externas.store'), $data)->assertOk();
        $this->assertSame('Cliente del catálogo', Reparacion::first()->contacto_nombre);
        $this->assertSame('9991234567', Reparacion::first()->contacto_telefono);
        $this->assertEquals($this->cliente->id, Reparacion::first()->contacto_cliente_id);
        $this->getJson(route('reparaciones.clientes', ['q' => 'catálogo']))->assertOk()->assertJsonFragment(['nombre' => 'Cliente del catálogo', 'telefono' => '9991234567']);
    }

    public function test_optional_images_are_private_and_retry_does_not_duplicate_files()
    {
        $data = $this->datos(); $data['imagenes'] = [UploadedFile::fake()->image('pieza.png', 30, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $this->assertSame(1, Reparacion::count()); $this->assertSame(1, ReparacionImagen::count());
        $imagen = ReparacionImagen::first(); Storage::disk('local')->assertExists($imagen->ruta);
        $this->assertCount(1, Storage::disk('local')->allFiles('reparaciones'));
        $this->get(route('reparaciones.imagen', [Reparacion::first(), $imagen]))->assertOk();
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $this->get(route('reparaciones.imagen', [Reparacion::orderByDesc('id')->first(), $imagen]))->assertNotFound();
    }

    public function test_delivery_creates_one_expense_and_income_and_rename_updates_both()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk(); $reparacion = Reparacion::first();
        $data = ['precio_compra' => '100.25', 'precio_venta' => '250.75'];
        $this->postJson(route('reparaciones.entregar', $reparacion), $data)->assertOk();
        $this->postJson(route('reparaciones.entregar', $reparacion), $data)->assertOk();
        $this->assertSame(1, Egreso::count()); $this->assertSame(1, Ingreso::count());
        $this->assertEquals('100.25', Egreso::first()->monto); $this->assertEquals('250.75', Ingreso::first()->monto);
        $this->assertSame('ENTREGADO', $reparacion->fresh()->estado);
        $this->assertEquals('100.25', $reparacion->fresh()->precio_compra); $this->assertEquals('250.75', $reparacion->fresh()->precio_venta);
        $this->assertEquals(3, Producto::first()->existencia);
        $this->postJson(route('reparaciones.nombre', $reparacion), ['nombre' => 'Cadena reparada'])->assertOk();
        $this->assertSame('Cadena reparada', Egreso::first()->concepto); $this->assertSame('Cadena reparada', Ingreso::first()->concepto);
        $this->deleteJson(route('finanzas.destroy', Egreso::first()->id))->assertStatus(422);
        $this->deleteJson(route('finanzas.destroyIngreso', Ingreso::first()->id))->assertStatus(422);
    }

    public function test_income_failure_rolls_back_expense_and_delivery()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        Ingreso::creating(function () { throw new \RuntimeException('Income unavailable'); });
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), ['precio_compra' => 100, 'precio_venta' => 250])->assertStatus(500);
        $this->assertSame(0, Egreso::count()); $this->assertSame(0, Ingreso::count());
        $this->assertSame('EN_REPARACION', Reparacion::first()->estado); $this->assertNull(Reparacion::first()->precio_compra);
    }

    public function test_cancellation_does_not_touch_inventory_or_finances()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk(); $reparacion = Reparacion::first();
        $this->postJson(route('reparaciones.cancelar', $reparacion), [])->assertOk();
        $this->postJson(route('reparaciones.cancelar', $reparacion), [])->assertOk();
        $this->postJson(route('reparaciones.entregar', $reparacion), ['precio_compra' => 100, 'precio_venta' => 200])->assertStatus(422);
        $this->assertSame('CANCELADO', $reparacion->fresh()->estado);
        $this->assertEquals(3, Producto::first()->existencia);
        $this->assertSame(0, Egreso::count()); $this->assertSame(0, Ingreso::count());
    }

    public function test_invalid_contacts_files_and_amounts_are_rejected()
    {
        $data = $this->datos(); $data['contacto_telefono'] = ''; $data['observaciones'] = '';
        $this->postJson(route('reparaciones.externas.store'), $data)->assertStatus(422)->assertJsonValidationErrors(['contacto_telefono', 'observaciones']);
        $data = $this->datos(); $data['contacto_origen'] = 'catalogo'; $data['contacto_cliente_id'] = 999;
        $this->postJson(route('reparaciones.externas.store'), $data)->assertStatus(422);
        $data = $this->datos(); $data['imagenes'] = [UploadedFile::fake()->create('script.php', 1)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertStatus(422);
        $this->assertSame(0, Reparacion::count());
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), [])->assertStatus(422);
        $this->postJson(route('reparaciones.entregar', Reparacion::first()), ['precio_compra' => -1, 'precio_venta' => '20.123'])->assertStatus(422);
        $this->assertSame(0, Egreso::count()); $this->assertSame(0, Ingreso::count());
    }

    public function test_failed_image_record_removes_the_stored_file()
    {
        ReparacionImagen::creating(function () { throw new \RuntimeException('Cannot save photo'); });
        $data = $this->datos(); $data['imagenes'] = [UploadedFile::fake()->image('pieza.png', 20, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertStatus(500);
        $this->assertSame(0, Reparacion::count()); $this->assertEmpty(Storage::disk('local')->allFiles('reparaciones'));
    }

    public function test_pdf_generates_with_and_without_images_and_omits_internal_cost()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $response = $this->get(route('reparaciones.pdf', Reparacion::first()))->assertOk();
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $data = $this->datos(); $data['imagenes'] = [UploadedFile::fake()->image('pieza.png', 40, 30)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $conImagen = Reparacion::orderByDesc('id')->first();
        $response = $this->get(route('reparaciones.pdf', $conImagen))->assertOk();
        $this->assertStringContainsString('/Subtype /Image', $response->getContent());
        $sinImagenes = $this->get(route('reparaciones.pdf', $conImagen).'?imagenes=0')->assertOk();
        $this->assertStringStartsWith('inline;', $sinImagenes->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString('/Subtype /Image', $sinImagenes->getContent());
        $this->assertSame(1, $conImagen->imagenes()->count());
        $conImagen->precio_compra = '123.45'; $conImagen->precio_venta = '234.56'; $conImagen->estado = 'ENTREGADO';
        $html = view('reparaciones.pdf', ['reparacion' => $conImagen, 'imagenes' => collect()])->render();
        $this->assertStringContainsString('234.56', $html); $this->assertStringNotContainsString('123.45', $html);
    }

    public function test_permissions_protect_contact_search_images_pdf_and_creation()
    {
        $data = $this->datos(); $data['imagenes'] = [UploadedFile::fake()->image('pieza.png', 20, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $reparacion = Reparacion::first(); $imagen = ReparacionImagen::first();
        $this->actingAs(User::create(['name' => 'Sin permiso', 'email' => 'no@test.local', 'password' => 'test']));
        $this->getJson(route('reparaciones.clientes'))->assertStatus(403);
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertStatus(403);
        $this->getJson(route('reparaciones.imagen', [$reparacion, $imagen]))->assertStatus(403);
        $this->getJson(route('reparaciones.pdf', $reparacion))->assertStatus(403);
        $this->getJson(route('reparaciones.externas.edit', $reparacion))->assertStatus(403);
        $this->postJson(route('reparaciones.externas.update', $reparacion), ['observaciones' => 'Cambiar'])->assertStatus(403);
    }

    public function test_images_have_before_and_after_stages_and_can_be_edited_after_delivery()
    {
        $data = $this->datos();
        $data['imagenes_antes'] = [UploadedFile::fake()->image('antes.png', 30, 20)];
        $data['imagenes_despues'] = [UploadedFile::fake()->image('despues.png', 30, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $reparacion = Reparacion::first(); $antes = $reparacion->imagenes()->where('etapa', 'ANTES')->firstOrFail();
        $this->assertSame(1, $reparacion->imagenes()->where('etapa', 'DESPUES')->count());
        $this->postJson(route('reparaciones.entregar', $reparacion), ['precio_compra' => 100, 'precio_venta' => 250])->assertOk();
        $this->getJson(route('reparaciones.externas.edit', $reparacion))->assertOk()->assertJsonFragment(['etapa' => 'ANTES'])->assertJsonFragment(['etapa' => 'DESPUES']);
        $this->post(route('reparaciones.externas.update', $reparacion), [
            'nombre' => 'Edición con imágenes', 'observaciones' => 'Nueva descripción', 'eliminar_imagenes' => [$antes->id],
            'imagenes_antes' => [UploadedFile::fake()->image('nuevo-antes.png', 30, 20)],
            'imagenes_despues' => [UploadedFile::fake()->image('nuevo-despues.png', 30, 20)],
        ], ['Accept' => 'application/json'])->assertOk();
        Storage::disk('local')->assertMissing($antes->ruta);
        $this->assertSame(3, $reparacion->imagenes()->count());
        $this->assertSame(1, $reparacion->imagenes()->where('etapa', 'ANTES')->count());
        $this->assertSame(2, $reparacion->imagenes()->where('etapa', 'DESPUES')->count());
        $this->assertSame('ENTREGADO', $reparacion->fresh()->estado);
        $this->assertEquals(100, Egreso::first()->monto); $this->assertEquals(250, Ingreso::first()->monto);
        $this->assertSame('Edición con imágenes', Egreso::first()->concepto);
        $this->assertSame('Edición con imágenes', Ingreso::first()->concepto);
        $this->assertEquals(3, Producto::first()->existencia);
        $html = view('reparaciones.modals.piezas', ['reparacion' => $reparacion->fresh()->load('imagenes')])->render();
        $this->assertStringContainsString('Antes de la reparación', $html); $this->assertStringContainsString('Después de la reparación', $html);
        $this->assertStringContainsString('btn-ver-imagen-reparacion', $html); $this->assertStringNotContainsString('target="_blank"', $html);
        $download = $this->get(route('reparaciones.pdf', $reparacion).'?descargar=1')->assertOk();
        $this->assertStringStartsWith('attachment;', $download->headers->get('Content-Disposition'));
    }

    public function test_edit_cannot_remove_images_from_another_repair()
    {
        $data = $this->datos(); $data['imagenes_antes'] = [UploadedFile::fake()->image('antes.png', 20, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $imagen = ReparacionImagen::first();
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $otra = Reparacion::orderByDesc('id')->first();
        $this->postJson(route('reparaciones.externas.update', $otra), ['observaciones' => 'Cambiar', 'eliminar_imagenes' => [$imagen->id]])->assertStatus(422);
        $this->assertSame(1, ReparacionImagen::count()); Storage::disk('local')->assertExists($imagen->ruta);
    }

    public function test_failed_edit_keeps_old_files_and_rolls_back_deletion()
    {
        $data = $this->datos(); $data['imagenes_antes'] = [UploadedFile::fake()->image('antes.png', 20, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $reparacion = Reparacion::first(); $imagen = ReparacionImagen::first();
        ReparacionImagen::creating(function () { throw new \RuntimeException('Cannot save image'); });
        $this->post(route('reparaciones.externas.update', $reparacion), [
            'observaciones' => 'Cambiar', 'eliminar_imagenes' => [$imagen->id],
            'imagenes_despues' => [UploadedFile::fake()->image('despues.png', 20, 20)],
        ], ['Accept' => 'application/json'])->assertStatus(500);
        $this->assertSame(1, ReparacionImagen::count()); Storage::disk('local')->assertExists($imagen->ruta);
        $this->assertCount(1, Storage::disk('local')->allFiles('reparaciones'));
        $this->assertSame($data['observaciones'], $reparacion->fresh()->observaciones);
    }

    private function operador($name, $all = false)
    {
        $role = \App\Role::create(['name' => $name, 'slug' => Str::slug($name)]);
        $slugs = ['ver_reparaciones', 'manage_reparaciones'];
        if ($all) $slugs[] = 'ver_todas_reparaciones';
        $role->permissions()->sync(\App\Permission::whereIn('slug', $slugs)->pluck('id'));
        return User::create(['name' => $name, 'email' => Str::slug($name).'@test.local', 'password' => 'test', 'role_id' => $role->id]);
    }

    public function test_own_only_scope_ignores_forged_user_filter_and_hides_other_records()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos() + ['id_usuario' => 999])->assertOk();
        $ajena = Reparacion::first();
        $operador = $this->operador('Operador propio');
        $this->actingAs($operador);
        $data = $this->datos(); $data['nombre'] = 'REPARACION PROPIA'; $data['id_usuario'] = 1;
        $this->postJson(route('reparaciones.externas.store'), $data)->assertOk();
        $propia = Reparacion::orderByDesc('id')->first(); $this->assertEquals($operador->id, $propia->id_usuario);
        $this->get(route('reparaciones.index'))->assertOk()->assertViewHas('puedeVerTodas', false)
            ->assertViewHas('usuariosFiltro', function ($usuarios) { return $usuarios->isEmpty(); })
            ->assertViewHas('reparaciones', function ($rows) use ($propia) { return $rows->total() === 1 && $rows->first()->id === $propia->id; });
        $this->get(route('reparaciones.index', ['usuario_id' => 1, 'estado' => '']))->assertOk()
            ->assertViewHas('reparaciones', function ($rows) use ($propia) { return $rows->total() === 1 && $rows->first()->id === $propia->id; });
        $this->getJson(route('reparaciones.externas.edit', $ajena))->assertNotFound();
        $this->postJson(route('reparaciones.externas.update', $ajena), ['observaciones' => 'No permitido'])->assertNotFound();
        $this->postJson(route('reparaciones.nombre', $ajena), ['nombre' => 'No permitido'])->assertNotFound();
        $this->postJson(route('reparaciones.cancelar', $ajena), [])->assertNotFound();
        $this->postJson(route('reparaciones.entregar', $ajena), ['precio_compra' => 10, 'precio_venta' => 20])->assertNotFound();
        $this->get(route('reparaciones.pdf', $ajena))->assertNotFound();
        $this->get(route('reparaciones.pdf', $propia))->assertOk();
        $this->assertSame('EN_REPARACION', $ajena->fresh()->estado);
        $this->assertSame(0, Egreso::count()); $this->assertSame(0, Ingreso::count());
    }

    public function test_view_all_permission_enables_user_filter_and_ajax_returns_only_grid()
    {
        $this->postJson(route('reparaciones.externas.store'), $this->datos())->assertOk();
        $ajena = Reparacion::first();
        $operador = $this->operador('Supervisor', true); $this->actingAs($operador);
        $data = $this->datos(); $data['nombre'] = 'DEL SUPERVISOR';
        $this->postJson(route('reparaciones.externas.store'), $data)->assertOk();
        $this->get(route('reparaciones.index'))->assertOk()->assertViewHas('puedeVerTodas', true)
            ->assertViewHas('reparaciones', function ($rows) { return $rows->total() === 2; });
        $this->get(route('reparaciones.index', ['usuario_id' => 1]))->assertOk()
            ->assertViewHas('reparaciones', function ($rows) use ($ajena) { return $rows->total() === 1 && $rows->first()->id === $ajena->id; });
        $response = $this->get(route('reparaciones.index', ['usuario_id' => $operador->id, 'estado' => '']), ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])->assertOk();
        $html = $response->json('html');
        $this->assertStringContainsString('DEL SUPERVISOR', $html);
        $this->assertStringNotContainsString('Soldadura de cadena', $html);
        $this->assertStringNotContainsString('<html', $html);
        $this->assertStringNotContainsString('id="filtrosReparaciones"', $html);
        $this->assertStringContainsString('btn-editar-reparacion-externa', $html);
        $this->getJson(route('reparaciones.externas.edit', $ajena))->assertOk();
        $this->postJson(route('reparaciones.nombre', $ajena), ['nombre' => 'Permitido'])->assertOk();
    }

    public function test_private_images_of_other_users_require_view_all_permission()
    {
        $data = $this->datos(); $data['imagenes_antes'] = [UploadedFile::fake()->image('pieza.png', 20, 20)];
        $this->post(route('reparaciones.externas.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $reparacion = Reparacion::first(); $imagen = ReparacionImagen::first();
        $this->actingAs($this->operador('Propietario limitado'));
        $this->get(route('reparaciones.imagen', [$reparacion, $imagen]))->assertNotFound();
        $this->actingAs($this->operador('Supervisor fotos', true));
        $this->get(route('reparaciones.imagen', [$reparacion, $imagen]))->assertOk();
    }
}
