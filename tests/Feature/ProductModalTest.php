<?php

namespace Tests\Feature;

use App\Producto;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        foreach (['2014_10_12_000000_create_users_table.php', '2020_03_04_190205_create_productos_table.php', '2026_06_26_000002_create_notificaciones_leidas_table.php'] as $file) {
            require_once database_path('migrations/'.$file);
        }
        (new \CreateUsersTable)->up();
        (new \CreateProductosTable)->up();
        (new \CreateNotificacionesLeidasTable)->up();
        Schema::table('productos', function (Blueprint $table) {
            $table->string('img')->nullable();
            $table->boolean('lActivo')->default(1);
            foreach (['id_categoria', 'id_material', 'id_proveedor'] as $field) $table->integer($field)->nullable();
        });
        foreach (['categorias', 'materiales', 'proveedores'] as $table) {
            Schema::create($table, function (Blueprint $blueprint) { $blueprint->id(); });
            DB::table($table)->insert(['id' => 1]);
        }
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'test']);
        $this->actingAs($admin);
    }

    private function payload(): array
    {
        return ['codigo_barras' => 'COL-001', 'descripcion' => 'Anillo de oro', 'precio_compra' => '100.50',
            'precio_venta' => '200.00', 'existencia' => '5', 'id_categoria' => 1, 'id_material' => 1, 'id_proveedor' => 1];
    }

    public function test_creates_and_edits_all_fields_without_replacing_existing_image()
    {
        $this->postJson(route('productos.modal.store'), $this->payload())->assertOk()->assertJson(['lSuccess' => true]);
        $producto = Producto::firstOrFail();
        $this->assertSame('Anillo de oro', $producto->descripcion);
        $this->assertEquals(1, $producto->lActivo);
        $producto->img = 'original.jpg';
        $producto->save();
        $data = array_merge($this->payload(), ['codigo_barras' => 'COL-002', 'descripcion' => 'Cadena',
            'precio_compra' => '150', 'precio_venta' => '300', 'existencia' => '2.5',
            'id_categoria' => 0, 'id_material' => 0, 'id_proveedor' => 0, 'lActivo' => 0]);
        $this->postJson(route('productos.modal.update', $producto->id), $data)->assertOk();
        $producto->refresh();
        foreach (['codigo_barras', 'descripcion', 'precio_compra', 'precio_venta', 'existencia', 'id_categoria', 'id_material', 'id_proveedor'] as $field) {
            $this->assertEquals($data[$field], $producto->$field);
        }
        $this->assertSame('original.jpg', $producto->img);
        $this->assertEquals(1, $producto->lActivo);
        $this->assertSame(1, Producto::count());
    }

    public function test_invalid_data_does_not_create_product()
    {
        $this->postJson(route('productos.modal.store'), array_merge($this->payload(), [
            'codigo_barras' => '', 'descripcion' => '', 'precio_compra' => -1, 'precio_venta' => '2.123',
            'existencia' => -5, 'id_categoria' => 999,
        ]))->assertStatus(422)->assertJsonValidationErrors(['codigo_barras', 'descripcion', 'precio_compra', 'precio_venta', 'existencia', 'id_categoria']);
        $this->assertSame(0, Producto::count());
    }

    public function test_existing_management_permission_is_required_for_both_actions()
    {
        $producto = Producto::create($this->payload());
        $user = User::create(['name' => 'Sin permiso', 'email' => 'user@test.local', 'password' => 'test']);
        $this->actingAs($user);
        $this->postJson(route('productos.modal.store'), $this->payload())->assertStatus(403);
        $this->postJson(route('productos.modal.update', $producto->id), $this->payload())->assertStatus(403);
        $this->assertSame(1, Producto::count());
    }

    public function test_missing_product_returns_not_found()
    {
        $this->postJson(route('productos.modal.update', 999), $this->payload())->assertStatus(404);
    }

    public function test_form_contains_full_product_fields()
    {
        $html = view('productos.modals.formulario', ['lstCategorias' => collect(), 'lstMateriales' => collect(), 'lstProveedores' => collect()])->render();
        foreach (array_merge(array_keys($this->payload()), ['img', '_token']) as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html);
        }
    }
}
