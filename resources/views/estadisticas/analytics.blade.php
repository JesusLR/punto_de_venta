@extends("maestra")
@section("titulo", "Tráfico Web (GA4)")
@section("contenido")

<style>
    .analytics-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
        border-left: 5px solid #4285F4;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .analytics-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(66, 133, 244, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .analytics-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.5px;
        position: relative;
        z-index: 1;
    }

    .analytics-header i {
        color: #4285F4;
        text-shadow: 0 2px 10px rgba(66, 133, 244, 0.2);
    }

    .iframe-container {
        position: relative;
        width: 100%;
        height: 75vh;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        background-color: #fff;
        overflow: hidden;
    }

    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Estilos Onboarding */
    .setup-container {
        background: white;
        border-radius: 16px;
        padding: 3rem 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        max-width: 850px;
        margin: 0 auto;
    }

    .setup-title {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.8rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .setup-title i {
        color: #4285F4;
    }

    .setup-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }

    .step-card {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .step-card:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .step-number {
        background: #eff6ff;
        color: #3b82f6;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .step-content h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .step-content p {
        color: #475569;
        line-height: 1.5;
        margin-bottom: 0.5rem;
    }

    .code-box {
        background: #0f172a;
        color: #38bdf8;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.9rem;
        display: inline-block;
        border: 1px solid #1e293b;
    }
</style>

<div class="container-fluid">
    <div class="analytics-header">
        <div>
            <h1><i class="fab fa-google mr-2"></i> Tráfico Web (Google Analytics)</h1>
            <p class="mb-0 text-white-50 mt-1">Monitorea el rendimiento del catálogo público y clics de WhatsApp en tiempo real</p>
        </div>
    </div>

    @if(config('services.google.report_url'))
        <!-- Mostrar Reporte de Google Analytics / Looker Studio -->
        <div class="iframe-container">
            <iframe src="{{ config('services.google.report_url') }}" allowfullscreen></iframe>
        </div>
    @else
        <!-- Guía de Configuración (Onboarding) -->
        <div class="setup-container">
            <div class="setup-title">
                <i class="fas fa-tools"></i> Configuración de Panel en Progreso
            </div>
            <p class="setup-subtitle">
                ¡Excelente! Ya estamos rastreando los clics de WhatsApp y las visitas a tus productos. Para poder ver estas estadísticas hermosas directamente aquí, sigue estos sencillos pasos:
            </p>

            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Crea tu reporte en Google Looker Studio</h3>
                    <p>Entra a <a href="https://lookerstudio.google.com" target="_blank" class="font-weight-bold">Google Looker Studio (clic aquí)</a> de forma gratuita usando la misma cuenta de Google con la que creaste tu Google Analytics.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Vincula tu fuente de datos GA4</h3>
                    <p>Crea un reporte en blanco, selecciona **Google Analytics** como origen de datos y elige tu propiedad actual: **"{{ env('APP_NAME') }}" (G-GM55M83HL0)**.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Crea e integra el enlace de inserción (Embed)</h3>
                    <p>Personaliza tu panel con las gráficas de tu preferencia (ej. visitas por ciudad, productos vistos, clics de WhatsApp). Cuando esté listo:</p>
                    <p>1. Haz clic en el botón superior derecho **Compartir > Insertar informe**.</p>
                    <p>2. Activa la opción **"Habilitar inserción"**.</p>
                    <p>3. Selecciona **URL de inserción** y copia ese enlace.</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Pega el enlace en tu archivo .env</h3>
                    <p>Abre el archivo [**.env**](file:///c:/laragon/www/punto_de_venta/.env) de tu proyecto y pega la URL de inserción en la variable `ANALYTICS_REPORT_URL`:</p>
                    <div class="mt-2">
                        <span class="code-box">ANALYTICS_REPORT_URL=https://lookerstudio.google.com/embed/reporting/tu-id-de-reporte</span>
                    </div>
                    <p class="text-muted small mt-2">¡Y listo! Al guardar el archivo, este panel cargará automáticamente tu reporte interactivo en tiempo real.</p>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
