<?php

namespace Tests\Feature;

use App\HomepageSetting;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepageImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        require_once database_path('migrations/2014_10_12_000000_create_users_table.php'); (new \CreateUsersTable)->up();
        Schema::create('homepage_settings', function (Blueprint $table) { $table->id(); $table->string('key')->unique(); $table->text('value')->nullable(); $table->timestamps(); });
        Schema::create('permissions', function (Blueprint $table) { $table->id(); $table->string('slug'); });
        Storage::fake('public');
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'test']));
    }

    private function data(): array
    {
        $data = ['gallery_count' => 1, 'team_count' => 1, 'contact_email' => 'contacto@test.local', 'contact_phone' => '9991234567', 'team_name' => ['Colibrí'], 'team_role' => ['Equipo']];
        foreach (['hero_title','hero_subtitle','story_title','story_text','mission_title','mission_text','materials_title','materials_text','store_address','store_hours'] as $key) $data[$key] = 'Texto de prueba';
        return $data;
    }

    public function test_gallery_and_team_use_public_disk_and_are_visible_without_storage_link()
    {
        config(['filesystems.default' => 'public']);
        $data = $this->data();
        $data['gallery_image_0'] = UploadedFile::fake()->image('vitrina.png', 30, 20);
        $data['team_image_0'] = UploadedFile::fake()->image('equipo.png', 20, 20);
        $this->post(route('homepage.settings.update'), $data)->assertRedirect(route('homepage.settings.index'))->assertSessionHas('mensaje')->assertSessionMissing('error');
        $gallery = json_decode(HomepageSetting::getValue('gallery_images'), true);
        $team = json_decode(HomepageSetting::getValue('team_members'), true);
        Storage::disk('public')->assertExists('about_gallery/'.$gallery[0]);
        Storage::disk('public')->assertExists('about_gallery/'.$team[0]['image']);
        Storage::disk('public')->assertMissing('public/about_gallery/'.$gallery[0]);
        $this->get(route('homepage.settings.index'))->assertOk()->assertSee(route('homepage.image', $gallery[0]), false);
        Auth::logout();
        $this->get(route('homepage.image', $gallery[0]))->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get(route('homepage.image', $team[0]['image']))->assertOk();
        $this->get(route('inicio'))->assertOk()->assertSee(route('homepage.image', $gallery[0]), false)->assertSee(route('homepage.image', $team[0]['image']), false);
    }

    public function test_actual_upload_field_names_are_validated()
    {
        $data = $this->data(); $data['gallery_image_0'] = UploadedFile::fake()->create('archivo.php', 1);
        $data['team_image_0'] = UploadedFile::fake()->image('equipo.png', 20, 20)->size(5121);
        $this->postJson(route('homepage.settings.update'), $data)->assertStatus(422)->assertJsonValidationErrors(['gallery_image_0', 'team_image_0']);
        $this->assertSame(0, HomepageSetting::count());
        $this->assertEmpty(Storage::disk('public')->allFiles('about_gallery'));
    }

    public function test_failed_upload_preserves_old_settings_and_does_not_report_success()
    {
        HomepageSetting::setValue('gallery_images', json_encode(['gallery_existente.png']));
        HomepageSetting::setValue('hero_title', 'Título anterior');
        $disk = \Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->andReturn($disk);
        $data = $this->data(); $data['gallery_image_0'] = UploadedFile::fake()->image('vitrina.png', 20, 20);
        $this->post(route('homepage.settings.update'), $data)->assertRedirect(route('homepage.settings.index'))->assertSessionHas('error')->assertSessionMissing('mensaje');
        $this->assertSame('Título anterior', HomepageSetting::getValue('hero_title'));
        $this->assertSame(['gallery_existente.png'], json_decode(HomepageSetting::getValue('gallery_images'), true));
    }

    public function test_upload_replacement_removes_old_gallery_file_only_after_success()
    {
        Storage::disk('public')->put('about_gallery/gallery_anterior.png', 'old');
        HomepageSetting::setValue('gallery_images', json_encode(['gallery_anterior.png']));
        $data = $this->data(); $data['gallery_image_0'] = UploadedFile::fake()->image('nueva.png', 20, 20);
        $this->post(route('homepage.settings.update'), $data)->assertSessionHas('mensaje');
        Storage::disk('public')->assertMissing('about_gallery/gallery_anterior.png');
        $gallery = json_decode(HomepageSetting::getValue('gallery_images'), true);
        Storage::disk('public')->assertExists('about_gallery/'.$gallery[0]);
    }

    public function test_image_route_rejects_missing_or_unrelated_files_and_upload_requires_permission()
    {
        $this->get(route('homepage.image', 'gallery_missing.png'))->assertNotFound();
        Storage::disk('public')->put('about_gallery/secret.txt', 'secret');
        $this->get(route('homepage.image', 'secret.txt'))->assertNotFound();
        $this->actingAs(User::create(['name' => 'Sin permiso', 'email' => 'no@test.local', 'password' => 'test']));
        $this->postJson(route('homepage.settings.update'), $this->data())->assertStatus(403);
    }
}
