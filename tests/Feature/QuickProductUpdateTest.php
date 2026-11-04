<?php

namespace Tests\Feature;

use App\Producto;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuickProductUpdateTest extends TestCase
{
    private $producto;
    private $admin;
    private $imageDirectory;

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
            $table->integer('id_categoria')->nullable();
        });
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'test']);
        $this->producto = Producto::create([
            'codigo_barras' => 'ORIGINAL', 'descripcion' => 'Producto original',
            'precio_compra' => 10, 'precio_venta' => 20, 'existencia' => 2, 'id_categoria' => 7,
        ]);
        $root = storage_path('framework/testing/quick-products-'.uniqid());
        $this->app->instance('path.public', $root);
        $this->imageDirectory = $root.'/img/productos';
        mkdir($this->imageDirectory, 0777, true);
    }

    protected function tearDown(): void
    {
        // Liberar los handles de UploadedFile antes de limpiar en Windows.
        parent::tearDown();
        gc_collect_cycles();
        foreach (glob($this->imageDirectory.'/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->imageDirectory);
        rmdir(dirname($this->imageDirectory));
        rmdir(dirname(dirname($this->imageDirectory)));
    }

    private function payload(): array
    {
        return ['codigo_barras' => 'NUEVO', 'precio_compra' => '12.50', 'precio_venta' => '25.00', 'existencia' => '5.25'];
    }

    private function endpoint(): string
    {
        return route('productos.actualizarRapido', $this->producto);
    }

    public function test_updates_only_quick_fields_and_preserves_existing_image()
    {
        $this->producto->img = 'existente.jpg';
        $this->producto->save();
        $this->actingAs($this->admin)->postJson($this->endpoint(), array_merge($this->payload(), [
            'descripcion' => 'No cambiar', 'id_categoria' => 99,
        ]))->assertOk()->assertJson(['lSuccess' => true]);
        $this->producto->refresh();
        $this->assertSame('NUEVO', $this->producto->codigo_barras);
        $this->assertEquals(12.50, $this->producto->precio_compra);
        $this->assertEquals(25, $this->producto->precio_venta);
        $this->assertEquals(5.25, $this->producto->existencia);
        $this->assertSame('Producto original', $this->producto->descripcion);
        $this->assertEquals(7, $this->producto->id_categoria);
        $this->assertSame('existente.jpg', $this->producto->img);
    }

    public function test_replaces_image_with_safe_filename_independent_of_product_code()
    {
        $path = tempnam(sys_get_temp_dir(), 'quick-product-');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a9XcAAAAASUVORK5CYII='));
        $image = new UploadedFile($path, 'foto.png', 'image/png', null, true);
        $this->actingAs($this->admin)->post($this->endpoint(), array_merge($this->payload(), [
            'img' => $image, 'codigo_barras' => '../CODIGO',
        ]), ['Accept' => 'application/json'])->assertOk();
        $filename = $this->producto->fresh()->img;
        $this->assertStringStartsWith('producto_', $filename);
        $this->assertSame(basename($filename), $filename);
        $this->assertFileExists($this->imageDirectory.'/'.$filename);
    }

    public function test_invalid_values_and_files_do_not_modify_product()
    {
        $this->actingAs($this->admin)->postJson($this->endpoint(), array_merge($this->payload(), [
            'codigo_barras' => '', 'precio_compra' => -1, 'precio_venta' => '1.123', 'existencia' => 10000000,
            'img' => UploadedFile::fake()->create('archivo.php', 1),
        ]))->assertStatus(422)->assertJsonValidationErrors(['codigo_barras', 'precio_compra', 'precio_venta', 'existencia', 'img']);
        $this->assertSame('ORIGINAL', $this->producto->fresh()->codigo_barras);
        $this->assertEmpty(glob($this->imageDirectory.'/*'));
    }

    public function test_requires_product_management_permission()
    {
        $user = User::create(['name' => 'Sin permiso', 'email' => 'user@example.test', 'password' => 'test']);
        $this->actingAs($user)->postJson($this->endpoint(), $this->payload())->assertStatus(403);
        $this->assertSame('ORIGINAL', $this->producto->fresh()->codigo_barras);
    }

    public function test_missing_product_returns_not_found()
    {
        $this->actingAs($this->admin)->postJson(route('productos.actualizarRapido', 999), $this->payload())->assertStatus(404);
    }

    public function test_modal_renders_requested_fields()
    {
        $html = view('productos.modals.edicion_rapida')->render();
        foreach (['img', 'codigo_barras', 'precio_compra', 'precio_venta', 'existencia'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html);
        }
    }
}
