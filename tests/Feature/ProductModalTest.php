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
    private $imagenesRoot;
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        foreach (['2014_10_12_000000_create_users_table.php', '2020_03_04_190205_create_productos_table.php', '2026_06_26_000002_create_notificaciones_leidas_table.php'] as $file) {
            require_once database_path('migrations/'.$file);
        }
        (new \CreateUsersTable)->up();
        (new \CreateProductosTable)->up();
        require_once database_path('migrations/2026_11_10_000005_create_producto_imagenes_table.php');
        (new \CreateProductoImagenesTable)->up();
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

    private function prepararImagenes()
    {
        $this->imagenesRoot = storage_path('framework/testing/product-gallery-'.uniqid());
        mkdir($this->imagenesRoot.'/img/productos', 0777, true);
        $this->app->instance('path.public', $this->imagenesRoot);
    }

    private function foto($nombre)
    {
        $path = tempnam($this->imagenesRoot, 'source_');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aKS8AAAAASUVORK5CYII='));
        return new \Illuminate\Http\UploadedFile($path, $nombre, 'image/png', null, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown(); gc_collect_cycles();
        if ($this->imagenesRoot) {
            foreach (glob($this->imagenesRoot.'/img/productos/*') ?: [] as $file) unlink($file);
            foreach (glob($this->imagenesRoot.'/source_*') ?: [] as $file) unlink($file);
            rmdir($this->imagenesRoot.'/img/productos'); rmdir($this->imagenesRoot.'/img'); rmdir($this->imagenesRoot);
        }
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

    public function test_additional_images_are_saved_and_preserved_on_edit()
    {
        $this->prepararImagenes();
        $data = $this->payload();
        $data['img'] = $this->foto('principal.png');
        $data['imagenes_adicionales'] = [$this->foto('lateral.png'), $this->foto('detalle.png')];
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $producto = Producto::first(); $principal = $producto->img;
        $this->assertSame(2, $producto->imagenesAdicionales()->count());
        $this->assertFileExists($this->imagenesRoot.'/img/productos/'.$principal);
        foreach ($producto->imagenesAdicionales as $imagen) {
            $this->assertSame(basename($imagen->archivo), $imagen->archivo);
            $this->assertFileExists($this->imagenesRoot.'/img/productos/'.$imagen->archivo);
        }
        $this->postJson(route('productos.modal.update', $producto), $this->payload())->assertOk();
        $this->assertSame($principal, $producto->fresh()->img);
        $this->assertSame(2, $producto->imagenesAdicionales()->count());
        $data = $this->payload(); $data['imagenes_adicionales'] = [$this->foto('otra.png')];
        $this->post(route('productos.modal.update', $producto), $data, ['Accept' => 'application/json'])->assertOk();
        $this->assertSame(3, $producto->imagenesAdicionales()->count());
    }

    public function test_gallery_failure_rolls_back_product_and_removes_new_files()
    {
        $this->prepararImagenes();
        \App\ProductoImagen::creating(function () { throw new \RuntimeException('Cannot save gallery'); });
        $data = $this->payload(); $data['img'] = $this->foto('principal.png');
        $data['imagenes_adicionales'] = [$this->foto('detalle.png')];
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertStatus(500);
        $this->assertSame(0, Producto::count()); $this->assertSame(0, \App\ProductoImagen::count());
        $this->assertEmpty(glob($this->imagenesRoot.'/img/productos/*'));
    }

    public function test_gallery_rejects_invalid_files_and_more_than_ten_images()
    {
        $data = $this->payload(); $data['imagenes_adicionales'] = [\Illuminate\Http\UploadedFile::fake()->create('archivo.php', 1)];
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertStatus(422);
        $data['imagenes_adicionales'] = array_fill(0, 11, \Illuminate\Http\UploadedFile::fake()->image('foto.png', 20, 20));
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertStatus(422);
        $this->assertSame(0, Producto::count());
    }

    public function test_catalog_includes_gallery_and_uses_it_without_main_image()
    {
        foreach (['categorias' => 'cNombreCategoria', 'materiales' => 'cNombreMaterial'] as $table => $label) {
            Schema::table($table, function (Blueprint $schema) use ($label) { $schema->string($label)->nullable(); $schema->boolean('lActivo')->default(1); });
        }
        Schema::create('homepage_settings', function (Blueprint $table) { $table->id(); $table->string('key'); $table->text('value')->nullable(); });
        $producto = Producto::create($this->payload());
        $producto->imagenesAdicionales()->create(['archivo' => 'detalle-uno.png']);
        $producto->imagenesAdicionales()->create(['archivo' => 'detalle-dos.png']);
        $this->get('/api/catalogoProductos')->assertOk()->assertSee('2 fotos')->assertSee('detalle-uno.png')->assertSee('detalle-dos.png')
            ->assertViewHas('lstProducto', function ($productos) { return count($productos) === 1 && count($productos[0]->imagenes_adicionales) === 2; });
    }

    public function test_edit_can_remove_main_and_additional_images_on_save()
    {
        $this->prepararImagenes();
        $data = $this->payload(); $data['img'] = $this->foto('principal.png');
        $data['imagenes_adicionales'] = [$this->foto('uno.png'), $this->foto('dos.png')];
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $producto = Producto::first(); $principal = $producto->img;
        $imagenes = $producto->imagenesAdicionales()->pluck('archivo')->all();
        $this->postJson(route('productos.modal.update', $producto), $this->payload() + [
            'quitar_imagen_principal' => 1, 'eliminar_imagenes' => [$imagenes[0]],
        ])->assertOk();
        $this->assertNull($producto->fresh()->img);
        $this->assertSame([$imagenes[1]], $producto->imagenesAdicionales()->pluck('archivo')->all());
        $this->assertFileNotExists($this->imagenesRoot.'/img/productos/'.$principal);
        $this->assertFileNotExists($this->imagenesRoot.'/img/productos/'.$imagenes[0]);
        $this->assertFileExists($this->imagenesRoot.'/img/productos/'.$imagenes[1]);
    }

    public function test_image_removal_rejects_files_owned_by_another_product()
    {
        $producto = Producto::create($this->payload());
        $otro = Producto::create(array_merge($this->payload(), ['codigo_barras' => 'OTRO']));
        $otro->imagenesAdicionales()->create(['archivo' => 'ajena.png']);
        $this->postJson(route('productos.modal.update', $producto), $this->payload() + ['eliminar_imagenes' => ['ajena.png']])->assertStatus(422);
        $this->assertSame(1, $otro->imagenesAdicionales()->count());
    }

    public function test_failed_edit_preserves_images_marked_for_removal()
    {
        $this->prepararImagenes();
        $data = $this->payload(); $data['img'] = $this->foto('principal.png'); $data['imagenes_adicionales'] = [$this->foto('vieja.png')];
        $this->post(route('productos.modal.store'), $data, ['Accept' => 'application/json'])->assertOk();
        $producto = Producto::first(); $principal = $producto->img; $vieja = $producto->imagenesAdicionales()->first()->archivo;
        \App\ProductoImagen::creating(function () { throw new \RuntimeException('Cannot save new image'); });
        $data = $this->payload() + ['quitar_imagen_principal' => 1, 'eliminar_imagenes' => [$vieja], 'imagenes_adicionales' => [$this->foto('nueva.png')]];
        $this->post(route('productos.modal.update', $producto), $data, ['Accept' => 'application/json'])->assertStatus(500);
        $this->assertSame($principal, $producto->fresh()->img);
        $this->assertSame([$vieja], $producto->imagenesAdicionales()->pluck('archivo')->all());
        $this->assertFileExists($this->imagenesRoot.'/img/productos/'.$principal);
        $this->assertFileExists($this->imagenesRoot.'/img/productos/'.$vieja);
        $this->assertCount(2, glob($this->imagenesRoot.'/img/productos/*'));
    }

    public function test_removing_a_shared_main_image_does_not_delete_another_products_file()
    {
        $this->prepararImagenes();
        file_put_contents($this->imagenesRoot.'/img/productos/compartida.png', 'image');
        $producto = Producto::create($this->payload()); $producto->img = 'compartida.png'; $producto->save();
        $otro = Producto::create(array_merge($this->payload(), ['codigo_barras' => 'OTRO'])); $otro->img = 'compartida.png'; $otro->save();
        $this->postJson(route('productos.modal.update', $producto), $this->payload() + ['quitar_imagen_principal' => 1])->assertOk();
        $this->assertNull($producto->fresh()->img);
        $this->assertSame('compartida.png', $otro->fresh()->img);
        $this->assertFileExists($this->imagenesRoot.'/img/productos/compartida.png');
    }
}
