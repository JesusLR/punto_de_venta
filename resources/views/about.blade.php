@extends('maestra_api')
@section('titulo', 'Inicio')
@section('contenido')

<!-- Carga de Fuentes Premium de Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

@php
    $heroTitle = $settings['hero_title'] ?? 'Casa de Joyería Colibri';
    $heroSubtitle = $settings['hero_subtitle'] ?? 'Somos una casa de joyería dedicada a diseñar piezas únicas y atemporales. Combinamos artesanía tradicional con acabados contemporáneos para ofrecer calidad, exclusividad y longevidad.';
    $pills = isset($settings['hero_pills']) ? json_decode($settings['hero_pills'], true) : ['Alta Joyería', 'Diseños Exclusivos', 'Artesanía Tradicional'];
    
    $storyTitle = $settings['story_title'] ?? 'Nuestra Historia';
    $storyText = $settings['story_text'] ?? 'Desde nuestros inicios hemos trabajado cuidadosamente cada pieza: desde la selección de metales y piedras, hasta el acabado final. Nuestro compromiso es crear joyas que acompañen historias.';
    
    $missionTitle = $settings['mission_title'] ?? 'Misión & Visión';
    $missionText = $settings['mission_text'] ?? 'Misión: ofrecer piezas de joyería artesanales con diseño contemporáneo y responsabilidad en los materiales. Visión: ser referente regional en piezas exclusivas y asesoría personalizada.';
    
    $materialsTitle = $settings['materials_title'] ?? 'Metales Nobles & Calidad';
    $materialsText = $settings['materials_text'] ?? 'Trabajamos con oro, plata y aleaciones certificadas; piedras seleccionadas por su belleza y durabilidad. Cada pieza pasa por controles de calidad estrictos.';

    $gallery = isset($settings['gallery_images']) ? json_decode($settings['gallery_images'], true) : [];
    $team = isset($settings['team_members']) ? json_decode($settings['team_members'], true) : [];
    $galleryCount = isset($settings['gallery_count']) ? (int)$settings['gallery_count'] : 6;
    $teamCount = isset($settings['team_count']) ? (int)$settings['team_count'] : 3;

    $storeAddress = $settings['store_address'] ?? 'Av. Ejemplo 123, Ciudad';
    $storeHours = $settings['store_hours'] ?? 'Lun - Vie 10:00 - 18:00';
    $mapsIframe = $settings['maps_iframe'] ?? 'https://www.google.com/maps/embed?pb=!4v1770232199564!6m8!1m7!1sObmAkpfr3RHi2xXMMjq2Pg!2m2!1d21.28547093379538!2d-89.66330729701143!3f88.97710322739826!4f-9.231406347633083!5f1.3700067333042356';
    $contactEmail = $settings['contact_email'] ?? 'info@empresa.com';
    $contactPhone = $settings['contact_phone'] ?? '';
@endphp

<style>
    /* Reset & Tipografía */
    .about-container {
        font-family: 'Montserrat', sans-serif;
        color: #2c3e50;
        background-color: #fcfbf9;
        margin-top: -20px; /* Acercar al navbar */
        padding-bottom: 3rem;
    }

    /* Fuentes Elegantes */
    .font-serif-luxury {
        font-family: 'Cormorant Garamond', serif;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* Degradado Dorado en Texto */
    .text-gold-gradient {
        background: linear-gradient(135deg, #F5D782 0%, #D4AF37 50%, #B38F24 100%);
        -webkit-background-clip: text;
        -webkit-background-fill-color: transparent;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    /* Héroe de Lujo */
    .about-hero {
        background: radial-gradient(circle at center, #1b1b1d 0%, #0d0d0e 100%);
        color: #f7efe2;
        border-radius: 0 0 24px 24px;
        padding: 6rem 2rem 5rem;
        margin-bottom: 3rem;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden;
        border-bottom: 2px solid #D4AF37;
    }
    
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 80% 20%, rgba(212,175,55,0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .about-hero-content {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
    }

    .about-hero p {
        color: #e5dac9;
        font-size: 1.15rem;
        line-height: 1.8;
        font-weight: 300;
        margin-bottom: 2rem;
    }

    /* Pastillas de Valores */
    .values-container {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    .value-pill {
        background: rgba(212, 175, 55, 0.08);
        border: 1px solid rgba(212, 175, 55, 0.25);
        color: #D4AF37;
        padding: 0.6rem 1.4rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .value-pill:hover {
        background: #D4AF37;
        color: #111;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        transform: translateY(-2px);
    }

    /* Tarjetas de Contenido */
    .luxury-card {
        background: white;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid rgba(212, 175, 55, 0.1);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        height: 100%;
        position: relative;
    }

    .luxury-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #D4AF37, #F5D782, #D4AF37);
        border-radius: 16px 16px 0 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .luxury-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 40px rgba(212, 175, 55, 0.08);
        border-color: rgba(212, 175, 55, 0.25);
    }

    .luxury-card:hover::after {
        opacity: 1;
    }

    .luxury-card h3 {
        color: #1a1a1a;
        font-size: 1.8rem;
        margin-bottom: 1.2rem;
        position: relative;
        padding-bottom: 0.6rem;
    }

    .luxury-card h3::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 40px; height: 2px;
        background-color: #D4AF37;
    }

    .luxury-card p {
        color: #666;
        font-size: 0.98rem;
        line-height: 1.7;
        margin: 0;
    }

    /* Vitrina de Galería */
    .gallery-section {
        background: #fbf9f4;
        padding: 4rem 0;
        margin: 3rem 0;
        border-top: 1px solid rgba(212,175,55,0.08);
        border-bottom: 1px solid rgba(212,175,55,0.08);
    }

    .gallery-title {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .gallery-title h2 {
        font-size: 2.6rem;
        color: #111;
        margin-bottom: 0.5rem;
    }

    .gallery-title p {
        color: #888;
        font-size: 1rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        border: 2px solid white;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .gallery-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(180deg, transparent 50%, rgba(17,17,17,0.85) 100%);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        border-color: #D4AF37;
    }

    .gallery-item:hover img {
        transform: scale(1.08);
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    /* Equipo de Trabajo */
    .team-section {
        padding: 2rem 0 4rem;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 2rem;
    }

    .team-card {
        background: white;
        border-radius: 16px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    }

    .team-img-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 1.5rem;
        border: 3px solid #D4AF37;
        box-shadow: 0 8px 20px rgba(212,175,55,0.15);
        transition: all 0.4s ease;
    }

    .team-card:hover .team-img-wrapper {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(212,175,55,0.25);
    }

    .team-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-card h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111;
        margin-bottom: 0.4rem;
    }

    .team-card p {
        color: #D4AF37;
        font-size: 0.88rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    /* Ubicación y Showroom */
    .map-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(212,175,55,0.08);
        margin-top: 3rem;
    }

    .map-details {
        padding: 2.5rem;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }

    .info-list li {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.2rem;
        color: #555;
        font-size: 0.95rem;
    }

    .info-list li i {
        color: #D4AF37;
        font-size: 1.2rem;
        margin-top: 0.2rem;
    }

    /* Botones Premium */
    .btn-gold-luxury {
        background: linear-gradient(135deg, #1b1b1d 0%, #111 100%);
        color: #D4AF37 !important;
        border: 1px solid #D4AF37;
        padding: 0.9rem 1.8rem;
        border-radius: 8px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-gold-luxury:hover {
        background: #D4AF37;
        color: #111 !important;
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.35);
        transform: translateY(-2px);
    }

    /* Llamada a la Acción (CTA) */
    .about-cta {
        background: linear-gradient(135deg, #121213 0%, #1b1c1e 100%);
        color: white;
        border-radius: 20px;
        padding: 3rem 2.5rem;
        margin-top: 4rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(212, 175, 55, 0.15);
    }

    .about-cta::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; width: 40%;
        background: radial-gradient(circle at right center, rgba(212,175,55,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .about-cta h3 {
        font-size: 2.2rem;
        margin-bottom: 0.8rem;
    }

    .about-cta p {
        color: #d2c8b7;
        font-size: 1.05rem;
        max-width: 650px;
        line-height: 1.6;
        margin-bottom: 1.8rem;
    }

    /* Footer */
    .about-footer {
        text-align: center;
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(0,0,0,0.04);
        color: #888;
        font-size: 0.9rem;
    }

    /* Responsividad */
    @media (max-width: 991px) {
        .about-hero h1 { font-size: 2.8rem; }
        .gallery-grid { grid-template-columns: repeat(2, 1fr); }
        .team-grid { grid-template-columns: 1fr; gap: 1.5rem; }
        .about-cta { text-align: center; }
        .about-cta p { margin: 0 auto 1.8rem; }
        .about-cta .d-flex { justify-content: center; flex-wrap: wrap; }
    }

    @media (max-width: 768px) {
        .about-hero h1 { font-size: 2.2rem; }
        .about-hero p { font-size: 1rem; }
        .gallery-grid { grid-template-columns: 1fr; }
        .map-details { padding: 1.5rem; }
    }
</style>

<div class="about-container">
    
    <!-- Hero Banner Premium -->
    <section class="about-hero">
        <div class="about-hero-content">
            <h1 class="font-serif-luxury text-gold-gradient">{{ $heroTitle }}</h1>
            <p>{{ $heroSubtitle }}</p>
            <div class="values-container">
                @foreach ($pills as $pill)
                    <div class="value-pill">{{ $pill }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="container">
        
        <!-- Grid de Historia, Misión y Visión -->
        <section class="row mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="luxury-card">
                    <h3 class="font-serif-luxury">{{ $storyTitle }}</h3>
                    <p>{{ $storyText }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="luxury-card">
                    <h3 class="font-serif-luxury">{{ $missionTitle }}</h3>
                    <p>{{ $missionText }}</p>
                </div>
            </div>
        </section>

        <!-- Galería de Vitrina -->
        @if ($galleryCount > 0)
        <section class="gallery-section rounded-lg px-3">
            <div class="gallery-title">
                <p class="font-serif-luxury" style="color: #D4AF37;">Colección Exclusiva</p>
                <h2 class="font-serif-luxury">Nuestra Vitrina</h2>
            </div>
            
            <div class="gallery-grid">
                @for ($i = 0; $i < $galleryCount; $i++)
                    @php
                        $img = $gallery[$i] ?? '';
                        $imgUrl = '';
                        if (empty($img)) {
                            $imgUrl = asset('img/logo.jpg');
                        } elseif (strpos($img, 'producto_') === 0) {
                            $imgUrl = asset('img/productos/' . $img);
                        } else {
                            $imgUrl = asset('storage/about_gallery/' . $img);
                        }
                    @endphp
                    <div class="gallery-item">
                        <img src="{{ $imgUrl }}" alt="Vitrina Joyería {{ $i + 1 }}">
                        <div class="gallery-overlay">
                            <span class="text-white font-weight-bold tracking-wider" style="font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">
                                Pieza de Colección
                            </span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
        @endif

        <!-- Sección de Materiales y Calidad -->
        <section class="row mb-5 justify-content-center">
            <div class="col-md-11">
                <div class="luxury-card text-center" style="border: 2px solid rgba(212,175,55,0.18);">
                    <div class="d-flex justify-content-center mb-3">
                        <i class="fas fa-gem" style="color: #D4AF37; font-size: 2.2rem;"></i>
                    </div>
                    <h3 class="font-serif-luxury text-center d-block mb-3" style="width:100%;">
                        <span class="text-gold-gradient">{{ $materialsTitle }}</span>
                    </h3>
                    <p class="mx-auto" style="max-width: 800px; color: #555;">{{ $materialsText }}</p>
                </div>
            </div>
        </section>

        <!-- Equipo de Expertos -->
        @if ($teamCount > 0)
            <section class="team-section">
                <div class="gallery-title">
                    <p class="font-serif-luxury" style="color: #D4AF37;">Manos Maestras</p>
                    <h2 class="font-serif-luxury">Nuestro Equipo</h2>
                </div>
                
                <div class="team-grid">
                    @for ($i = 0; $i < $teamCount; $i++)
                        @php
                            $member = $team[$i] ?? null;
                            if (!$member || empty($member['name'])) continue;
                            
                            $img = $member['image'] ?? '';
                            $imgUrl = '';
                            if (empty($img)) {
                                $imgUrl = asset('img/logo.jpg');
                            } elseif (strpos($img, 'team_') === 0) {
                                $imgUrl = asset('storage/about_gallery/' . $img);
                            } else {
                                $imgUrl = asset($img);
                            }
                        @endphp
                        <div class="team-card shadow-sm">
                            <div class="team-img-wrapper">
                                <img src="{{ $imgUrl }}" alt="{{ $member['name'] }}">
                            </div>
                            <h4>{{ $member['name'] }}</h4>
                            <p>{{ $member['role'] }}</p>
                        </div>
                    @endfor
                </div>
            </section>
        @endif

        <!-- Ubicación & Showroom -->
        <section class="map-card">
            <div class="row no-gutters">
                <div class="col-md-5">
                    <div class="map-details h-100 d-flex flex-column justify-content-center">
                        <p class="font-serif-luxury mb-1" style="color:#D4AF37; text-transform:uppercase; letter-spacing:1px; font-size:0.85rem;">Visítanos</p>
                        <h3 class="font-serif-luxury mb-3" style="font-size:2rem; color:#111;">Nuestro Showroom</h3>
                        <p style="color:#666; font-size:0.95rem; line-height:1.6;">Te invitamos a conocer nuestras instalaciones, donde podrás ver las colecciones en persona y recibir atención personalizada.</p>
                        
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Dirección:</strong><br>
                                    {{ $storeAddress }}
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Horarios:</strong><br>
                                    {{ $storeHours }}
                                </div>
                            </li>
                        </ul>

                        <div class="mt-2" style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                            <a class="btn-gold-luxury" href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($storeAddress) }}" target="_blank" rel="noopener">
                                <i class="fas fa-directions"></i> Cómo llegar
                            </a>
                            <a class="btn-gold-luxury" style="background:#333; border-color:#444; color:#fff !important;" href="{{ route('catalogoProductos.index') }}">
                                <i class="fas fa-th-large"></i> Ver catálogo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="map-wrap h-100" style="min-height: 380px;">
                        @if (!empty($mapsIframe))
                            <iframe src="{{ $mapsIframe }}" width="100%" height="100%" style="border:0; min-height: 380px; display:block;" allowfullscreen="" loading="lazy"></iframe>
                        @else
                            <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted" style="min-height: 380px;">
                                <i class="fas fa-map-marked-alt mr-2"></i> Mapa no disponible
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Agendar Cita o Contacto -->
        <section class="about-cta">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="font-serif-luxury">¿Deseas una pieza personalizada?</h3>
                    <p>Nuestros diseñadores y maestros joyeros pueden hacer realidad tu idea. Agenda una cita exclusiva o contáctanos por WhatsApp para una asesoría a tu medida.</p>
                </div>
                <div class="col-lg-4 text-lg-right">
                    <div class="d-flex flex-column flex-sm-row justify-content-lg-end gap-2" style="gap:0.6rem; flex-wrap:wrap;">
                        <a class="btn-gold-luxury" style="background:#25D366; border-color:#25D366; color:#fff !important;" href="https://wa.me/{{ $contactPhone }}" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp" style="font-size:1.2rem;"></i> WhatsApp
                        </a>
                        <a class="btn-gold-luxury" style="background:#B38F24; border-color:#B38F24; color:#111 !important;" href="{{ route('catalogoProductos.index') }}">
                            <i class="fas fa-gem"></i> Catálogo
                        </a>
                        <a class="btn-gold-luxury" href="mailto:{{ $contactEmail }}">
                            <i class="fas fa-envelope"></i> Correo
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Público -->
        <footer class="about-footer">
            <div class="font-serif-luxury font-weight-bold" style="color: #D4AF37; font-size:1.1rem; letter-spacing:1px;">{{ env('APP_NAME') }}</div>
            <div class="mt-2">© {{ date('Y') }} · Todos los derechos reservados · Alta Joyería Fina</div>
        </footer>

    </div>
</div>

@endsection