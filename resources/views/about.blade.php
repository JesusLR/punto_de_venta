@extends('maestra_api')
@section('titulo', 'Inicio')
@section('contenido')

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
    $pills = is_array($pills) ? $pills : [];
    $gallery = is_array($gallery) ? $gallery : [];
    $team = is_array($team) ? $team : [];
@endphp

<div class="about-container">
    
    <!-- Hero Banner Premium -->
    <section class="about-hero">
        <div class="about-hero-content">
            <span class="boutique-eyebrow">El encanto está en los detalles</span>
            <h1 class="font-serif-luxury text-gold-gradient">{{ $heroTitle }}</h1>
            <p>{{ $heroSubtitle }}</p>
            <div class="hero-actions">
                <a href="{{ route('catalogoProductos.index') }}" class="boutique-button">Descubrir la colección <span aria-hidden="true">↗</span></a>
                <a href="#historia" class="boutique-text-link">Nuestra historia <span aria-hidden="true">↓</span></a>
            </div>
            <div class="values-container">
                @foreach ($pills as $pill)
                    <div class="value-pill">{{ $pill }}</div>
                @endforeach
            </div>
        </div>
        <figure class="hero-jewel">
            <img src="{{ asset('img/colibri-dorado.svg') }}" alt="Colibrí de alas desplegadas en tonos dorados" fetchpriority="high">
            <figcaption><span>La belleza de lo especial</span><span>Colibrí</span></figcaption>
        </figure>
    </section>

    <div class="boutique-manifesto"><span>Para celebrar.</span><span>Para regalar.</span><span>Para ti.</span><i class="far fa-gem" aria-hidden="true"></i></div>

    <div class="container">
        
        <!-- Grid de Historia, Misión y Visión -->
        <section class="row mb-5 boutique-story" id="historia">
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
                        <img src="{{ $imgUrl }}" alt="Vitrina Joyería {{ $i + 1 }}" loading="lazy">
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
                <div class="col-lg-7">
                    <span class="boutique-eyebrow">Una joya, tu historia</span>
                    <h3 class="font-serif-luxury">¿Deseas una pieza personalizada?</h3>
                    <p>Nuestros diseñadores y maestros joyeros pueden hacer realidad tu idea. Agenda una cita exclusiva o contáctanos por WhatsApp para una asesoría a tu medida.</p>
                </div>
                <div class="col-lg-5">
                    <div class="personalized-actions">
                        <a class="btn-gold-luxury boutique-whatsapp" href="https://wa.me/{{ $contactPhone }}" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i> Hablemos por WhatsApp
                        </a>
                        <a class="btn-gold-luxury" href="{{ route('catalogoProductos.index') }}">
                            Ver colección <span aria-hidden="true">↗</span>
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
            <div class="mt-2">© {{ date('Y') }} · Todos los derechos reservados · {{env("APP_NAME")}}</div>
        </footer>

    </div>
</div>

@endsection
