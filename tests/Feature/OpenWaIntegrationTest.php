<?php

namespace Tests\Feature;

use App\Exceptions\OpenWaException;
use App\HomepageSetting;
use App\Services\OpenWaService;
use App\Services\WhatsAppBotService;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Mockery;
use Tests\TestCase;

class OpenWaIntegrationTest extends TestCase
{
    private const SESSION = '12345678-1234-4123-8123-123456789abc';
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'services.openwa.url' => 'http://openwa.test:2886/api',
            'services.openwa.system_key' => 'test-integration-secret-for-punto-de-venta',
            'services.openwa.source_system' => 'punto_de_venta', 'services.openwa.automatic_replies' => false,
            'app.name' => 'Joyería Colibrí Progreso',
        ]);
        require_once database_path('migrations/2014_10_12_000000_create_users_table.php');
        (new \CreateUsersTable)->up();
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id(); $table->string('key')->unique(); $table->text('value')->nullable(); $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) { $table->id(); $table->string('slug'); });
        Schema::create('clientes', function (Blueprint $table) { $table->id(); $table->string('nombre'); $table->string('telefono'); });
        Schema::create('ventas', function (Blueprint $table) {
            $table->id(); $table->integer('id_cliente'); $table->integer('id_usuario'); $table->timestamps();
        });
        Schema::create('productos_vendidos', function (Blueprint $table) {
            $table->id(); $table->integer('id_venta'); $table->decimal('precio'); $table->decimal('cantidad');
            $table->string('descripcion')->nullable(); $table->string('codigo_barras')->nullable();
        });
        Schema::create('apartados', function (Blueprint $table) {
            $table->id(); $table->integer('id_cliente'); $table->decimal('total');
        });
        Schema::create('apartado_abonos', function (Blueprint $table) {
            $table->id(); $table->integer('id_apartado'); $table->integer('id_usuario'); $table->decimal('monto');
        });
        Schema::create('apartado_productos', function (Blueprint $table) {
            $table->id(); $table->integer('id_apartado'); $table->string('descripcion'); $table->string('codigo_barras')->nullable();
        });
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'test']);

    }

    private function fixtureSession(array $overrides = []): array
    {
        return array_merge(['id' => self::SESSION, 'sourceSystem' => 'punto_de_venta', 'name' => 'punto-de-venta--centro',
            'status' => 'ready', 'engineLoaded' => true, 'phone' => '529991234567'], $overrides);
    }

    private function fakeApi(array $session = null, array $documentResponse = null): void
    {
        $session = $session ?? $this->fixtureSession();
        Http::fake(function ($request) use ($session, $documentResponse) {
            $path = parse_url($request->url(), PHP_URL_PATH);
            if ($path === '/api/integrations/sessions') return Http::response(['sourceSystem' => 'punto_de_venta', 'sessions' => [$session]]);
            if ($path === '/api/integrations/sessions/'.self::SESSION.'/documents') return Http::response($documentResponse ?? ['status' => 'sent', 'id' => 'message-1']);
            if (in_array($path, ['/api/integrations/sessions/'.self::SESSION, '/api/integrations/sessions/'.self::SESSION.'/start', '/api/integrations/sessions/'.self::SESSION.'/logout'])) return Http::response($session);
            if ($path === '/api/integrations/sessions/'.self::SESSION.'/qr') return Http::response(['status' => 'qr_ready', 'qrCode' => 'data:image/png;base64,QUJD']);
            return Http::response([], 404);
        });
    }

    private function pdf(): void
    {
        $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdf->shouldReceive('setPaper')->once()->with('letter', 'portrait')->andReturnSelf();
        $pdf->shouldReceive('output')->once()->andReturn('%PDF-1.4 test');
        Pdf::shouldReceive('loadView')->once()->andReturn($pdf);
    }

    public function test_lists_only_sessions_from_this_system_using_bearer_authentication()
    {
        $this->fakeApi();
        $this->actingAs($this->admin)->getJson(route('whatsapp.settings.sessions.list'))->assertOk()
            ->assertJsonPath('sessions.0.name', 'centro')->assertJsonPath('active_session_id', self::SESSION);
        Http::assertSent(function ($request) { return $request->hasHeader('Authorization', 'Bearer test-integration-secret-for-punto-de-venta') && strpos($request->url(), '/api/integrations/sessions') !== false; });
    }

    public function test_refuses_sessions_from_another_system()
    {
        $this->fakeApi($this->fixtureSession(['sourceSystem' => 'de_viaje_en_viaje']));
        $this->actingAs($this->admin)->getJson(route('whatsapp.settings.sessions.list'))->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_foreign_saved_session_is_not_used_for_sending()
    {
        HomepageSetting::setValue('wa_session_id', '87654321-1234-4123-8123-123456789abc');
        $this->fakeApi();
        $this->assertSame(self::SESSION, app(OpenWaService::class)->getActiveSessionId());
    }

    public function test_pdf_uses_new_endpoint_international_phone_and_idempotency_header()
    {
        $this->fakeApi();
        app(OpenWaService::class)->sendPdf('5219991234567', '%PDF-1.4 test', 'venta_1.pdf');
        Http::assertSent(function ($request) {
            return $request->url() === 'http://openwa.test:2886/api/integrations/sessions/'.self::SESSION.'/documents'
                && $request['phone'] === '529991234567' && base64_decode($request['base64']) === '%PDF-1.4 test'
                && $request->hasHeader('Idempotency-Key');
        });
    }

    public function test_disconnected_session_does_not_send_pdf()
    {
        $this->fakeApi($this->fixtureSession(['status' => 'disconnected', 'engineLoaded' => false]));
        try { app(OpenWaService::class)->sendPdf('9991234567', '%PDF-1.4 test', 'venta.pdf'); $this->fail('Expected error'); }
        catch (OpenWaException $e) { $this->assertSame(409, $e->getCode()); }
        Http::assertSentCount(2);
    }

    public function test_missing_credential_does_not_make_network_requests()
    {
        config(['services.openwa.system_key' => null]);
        try { app(OpenWaService::class)->getSessions(); $this->fail('Expected configuration error'); }
        catch (OpenWaException $e) { $this->assertSame(503, $e->getCode()); }
        Http::assertNothingSent();
    }

    public function test_upstream_integration_configuration_error_has_diagnostic_status()
    {
        Http::fake(function () { return Http::response(['message' => 'System integrations are not configured correctly'], 503); });
        $this->actingAs($this->admin)->getJson(route('whatsapp.settings.sessions.list'))
            ->assertStatus(503)->assertJsonPath('cMensaje', 'OpenWA no está disponible o su configuración de integraciones no es válida (HTTP 503). Revisa INTEGRATION_SYSTEMS y reinicia el servicio.');
    }

    public function test_cannot_select_session_from_another_system()
    {
        $this->fakeApi($this->fixtureSession(['sourceSystem' => 'de_viaje_en_viaje']));
        $this->actingAs($this->admin)->postJson(route('whatsapp.settings.sessions.active'), ['session_id' => self::SESSION])->assertStatus(403);
        $this->assertNull(HomepageSetting::getValue('wa_session_id'));
    }

    public function test_qr_and_logout_use_integration_routes()
    {
        $this->fakeApi($this->fixtureSession(['status' => 'qr_ready', 'engineLoaded' => true]));
        $this->actingAs($this->admin)->getJson(route('whatsapp.settings.sessions.qr', self::SESSION))->assertOk()->assertJsonPath('qrCode', 'data:image/png;base64,QUJD');
        $this->postJson(route('whatsapp.settings.sessions.stop'), ['session_id' => self::SESSION])->assertOk();
        Http::assertSent(function ($request) { return $request->method() === 'POST' && substr($request->url(), -7) === '/logout'; });
    }

    public function test_automatic_replies_remain_disabled_even_with_old_enabled_setting()
    {
        HomepageSetting::setValue('wa_bot_enabled', '1');
        $this->assertFalse(app(WhatsAppBotService::class)->processIncomingMessage(['chatId' => '529991234567@c.us', 'body' => 'Hola']));
        Http::assertNothingSent();
    }

    public function test_disable_migration_preserves_other_settings_and_is_repeatable()
    {
        HomepageSetting::setValue('wa_bot_enabled', '1');
        HomepageSetting::setValue('wa_welcome_text', 'Conservar');
        require_once database_path('migrations/2026_11_04_000002_disable_whatsapp_automatic_replies.php');
        $migration = new \DisableWhatsappAutomaticReplies;
        $migration->up(); $migration->up();
        $this->assertSame('0', HomepageSetting::getValue('wa_bot_enabled'));
        $this->assertSame('Conservar', HomepageSetting::getValue('wa_welcome_text'));
        $this->assertSame(2, HomepageSetting::count());
    }

    public function test_sales_pdf_sends_directly_through_integration()
    {
        DB::table('clientes')->insert(['id' => 1, 'nombre' => 'Cliente', 'telefono' => '9991234567']);
        DB::table('ventas')->insert(['id' => 1, 'id_cliente' => 1, 'id_usuario' => $this->admin->id]);
        DB::table('productos_vendidos')->insert(['id_venta' => 1, 'descripcion' => 'Anillo de oro', 'precio' => 100, 'cantidad' => 1]);
        $this->fakeApi(); $this->pdf();
        $this->actingAs($this->admin)->postJson(route('ventas.enviarPdfWhatsapp'), ['id_venta' => 1, 'telefono' => '9991234567'])->assertOk()->assertJson(['lSuccess' => true]);
        Http::assertSent(function ($request) {
            return $request->method() === 'POST' && $request['filename'] === 'Comprobante de pago _ Cliente _ Anillo de oro.pdf'
                && $request['phone'] === '529991234567'
                && $request['caption'] === '¡Hola! Aquí tienes tu comprobante de pago. Gracias por confiar en Joyería Colibrí Progreso.';
        });
        Http::assertSentCount(3);
    }

    public function test_layaway_pdf_sends_directly_through_integration()
    {
        DB::table('clientes')->insert(['id' => 1, 'nombre' => 'Cliente', 'telefono' => '9991234567']);
        DB::table('apartados')->insert(['id' => 1, 'id_cliente' => 1, 'total' => 100]);
        DB::table('apartado_productos')->insert(['id_apartado' => 1, 'descripcion' => 'Cadena de plata']);
        $this->fakeApi(); $this->pdf();
        $this->actingAs($this->admin)->postJson(route('apartados.openWA'), ['id_apartado' => 1])->assertOk()->assertJson(['lSuccess' => true]);
        Http::assertSent(function ($request) {
            return $request->method() === 'POST' && $request['filename'] === 'Historial de abonos _ Cliente _ Cadena de plata.pdf'
                && $request['phone'] === '529991234567'
                && $request['caption'] === '¡Hola! Aquí tienes tu historial de abonos. Gracias por confiar en Joyería Colibrí Progreso.';
        });
        Http::assertSentCount(3);
    }

    public function test_login_screen_hides_automatic_responses_and_other_whatsapp_modules()
    {
        $this->actingAs($this->admin)->get(route('whatsapp.settings.index'))->assertOk()
            ->assertSee('Inicio de sesión')->assertDontSee('Parámetros de Respuestas Automáticas')
            ->assertDontSee('Centro de Mensajes')->assertDontSee('WhatsApp Enlaces / QR');
        Http::assertNothingSent();
    }

    public function test_creates_and_starts_session_for_this_system()
    {
        Http::fake(function ($request) {
            if ($request->method() === 'POST' && $request->url() === 'http://openwa.test:2886/api/integrations/sessions') {
                return Http::response($this->fixtureSession(['status' => 'created', 'engineLoaded' => false]), 201);
            }
            return Http::response($this->fixtureSession(['status' => 'initializing', 'engineLoaded' => true]));
        });
        $this->actingAs($this->admin)->postJson(route('whatsapp.settings.sessions.create'), ['name' => 'centro'])->assertOk();
        $this->assertSame(self::SESSION, HomepageSetting::getValue('wa_session_id'));
        Http::assertSent(function ($request) { return $request->method() === 'POST' && isset($request['name']) && $request['name'] === 'centro'; });
        Http::assertSent(function ($request) { return substr($request->url(), -6) === '/start'; });
        Http::assertSentCount(2);
    }

    public function test_ready_status_without_active_engine_cannot_send()
    {
        $this->fakeApi($this->fixtureSession(['engineLoaded' => false]));
        try { app(OpenWaService::class)->sendPdf('9991234567', '%PDF-1.4 test', 'venta.pdf'); $this->fail('Expected error'); }
        catch (OpenWaException $e) { $this->assertSame(409, $e->getCode()); }
        Http::assertSentCount(2);
    }

    public function test_document_without_delivery_confirmation_is_not_reported_as_sent()
    {
        $this->fakeApi(null, ['status' => 'pending']);
        $this->expectException(OpenWaException::class);
        app(OpenWaService::class)->sendPdf('9991234567', '%PDF-1.4 test', 'venta.pdf');
    }

    public function test_downloads_use_the_same_descriptive_names_as_whatsapp()
    {
        DB::table('clientes')->insert(['id' => 1, 'nombre' => 'María López', 'telefono' => '9991234567']);
        DB::table('ventas')->insert(['id' => 1, 'id_cliente' => 1, 'id_usuario' => $this->admin->id]);
        DB::table('productos_vendidos')->insert(['id_venta' => 1, 'descripcion' => 'Anillo', 'precio' => 100, 'cantidad' => 1]);
        DB::table('apartados')->insert(['id' => 1, 'id_cliente' => 1, 'total' => 100]);
        DB::table('apartado_productos')->insert(['id_apartado' => 1, 'descripcion' => 'Cadena']);
        foreach ([
            ['ventas.pdf', 'Comprobante de pago _ María López _ Anillo.pdf'],
            ['apartados.pdf', 'Historial de abonos _ María López _ Cadena.pdf'],
        ] as [$route, $filename]) {
            $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
            $pdf->shouldReceive('setPaper')->once()->with('letter', 'portrait')->andReturnSelf();
            $pdf->shouldReceive('stream')->once()->with($filename)->andReturn(response('%PDF-1.4 test'));
            Pdf::shouldReceive('loadView')->once()->andReturn($pdf);
            $this->actingAs($this->admin)->get(route($route, 1))->assertOk();
        }
    }

    public function test_long_product_names_and_reserved_characters_produce_safe_pdf_filename()
    {
        $venta = new \App\Venta;
        $venta->setRelation('cliente', new \App\Cliente(['nombre' => 'María / López']));
        $venta->setRelation('productos', collect([
            new \App\ProductoVendido(['descripcion' => str_repeat('Anillo de ópalo: "azul" ', 30)]),
        ]));
        $filename = \App\Support\DocumentoPdf::nombre(\App\Support\DocumentoPdf::VENTA, $venta);
        $this->assertLessThanOrEqual(200, strlen($filename));
        $this->assertTrue(mb_check_encoding($filename, 'UTF-8'));
        $this->assertSame(0, preg_match('~[\\\\/:*?"<>|\x00-\x1F\x7F]~u', $filename));
        $this->assertStringContainsString('María - López', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
    }
}





