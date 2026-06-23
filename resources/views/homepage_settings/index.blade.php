@extends("maestra")
@section("titulo", "Configuración de Portada")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<style>
    .nav-tabs {
        border-bottom: 2px solid rgba(212, 175, 55, 0.2);
    }
    .nav-tabs .nav-link {
        color: #2a2a2a;
        font-weight: 700;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        padding: 1rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nav-tabs .nav-link:hover {
        color: #D4AF37;
        background: rgba(212, 175, 55, 0.04);
        border-bottom-color: rgba(212, 175, 55, 0.3);
    }
    .nav-tabs .nav-link.active {
        color: #D4AF37 !important;
        background: transparent;
        border-bottom: 3px solid #D4AF37;
    }
    .setting-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
    }
    .image-preview-box {
        position: relative;
        width: 100%;
        height: 160px;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
        overflow: hidden;
        background-color: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .image-preview-box:hover {
        border-color: #D4AF37;
    }
    .image-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .image-preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .image-preview-box:hover .image-preview-overlay {
        opacity: 1;
    }
    .hidden-file-input {
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="productos-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1><i class="fas fa-sliders-h"></i> Configuración de Portada</h1>
                <p style="margin-top: 5px; color: rgba(255,255,255,0.8);">Modifica dinámicamente los textos, imágenes de vitrina, miembros del equipo y datos de contacto de la página principal.</p>
            </div>
            <div class="mt-2 mt-md-0">
                <button type="button" onclick="document.getElementById('formSettings').submit();" class="btn btn-action" style="background:#D4AF37; color:#111; border:none;">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="formSettings" action="{{ route('homepage.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-justified mb-4" id="settingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="hero-tab" data-toggle="tab" href="#hero" role="tab" aria-controls="hero" aria-selected="true">
                        <i class="fas fa-gem mr-2"></i> Héroe & General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sections-tab" data-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="false">
                        <i class="fas fa-book-open mr-2"></i> Secciones / Historia
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab" aria-controls="gallery" aria-selected="false">
                        <i class="fas fa-images mr-2"></i> Vitrina / Galería
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="team-tab" data-toggle="tab" href="#team" role="tab" aria-controls="team" aria-selected="false">
                        <i class="fas fa-users mr-2"></i> Equipo de Trabajo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                        <i class="fas fa-map-marked-alt mr-2"></i> Contacto & Ubicación
                    </a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="settingTabsContent">
                
                <!-- Tab 1: Hero -->
                <div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                    <div class="setting-card">
                        <h4 class="section-title"><i class="fas fa-gem"></i> Sección Héroe (Banner de Entrada)</h4>
                        
                        <div class="form-group-modern mb-3">
                            <label for="hero_title"><i class="fas fa-heading"></i> Título del Héroe</label>
                            <input type="text" name="hero_title" id="hero_title" class="form-control form-control-modern" value="{{ $settings['hero_title'] ?? '' }}" required>
                            <small class="form-text text-muted">Es el título principal que se muestra en letras grandes doradas en la portada.</small>
                        </div>

                        <div class="form-group-modern mb-3">
                            <label for="hero_subtitle"><i class="fas fa-align-left"></i> Subtítulo / Descripción</label>
                            <textarea name="hero_subtitle" id="hero_subtitle" rows="3" class="form-control" required>{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                            <small class="form-text text-muted">Introduce una breve presentación o propuesta de valor de la joyería.</small>
                        </div>

                        <div class="form-group-modern mb-3">
                            <label for="hero_pills"><i class="fas fa-tags"></i> Pastillas de Valores (Separados por Coma)</label>
                            <input type="text" name="hero_pills" id="hero_pills" class="form-control form-control-modern" value="{{ implode(', ', $pills) }}">
                            <small class="form-text text-muted">Valores destacados que aparecerán debajo del subtítulo (ej: Alta Joyería, Diseños Exclusivos, Plata Ley .925).</small>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Sections -->
                <div class="tab-pane fade" id="sections" role="tabpanel" aria-labelledby="sections-tab">
                    <div class="setting-card">
                        <h4 class="section-title"><i class="fas fa-book-open"></i> Secciones de la Página</h4>
                        
                        <div class="row">
                            <!-- Card 1: Historia -->
                            <div class="col-md-6 mb-4">
                                <div class="p-3 border rounded" style="background-color: #fafafa; border-left: 3px solid #D4AF37 !important;">
                                    <h5 class="font-weight-bold text-dark mb-3">Tarjeta 1: Historia de la Casa</h5>
                                    <div class="form-group-modern mb-2">
                                        <label for="story_title"><i class="fas fa-tag"></i> Título de la Tarjeta</label>
                                        <input type="text" name="story_title" id="story_title" class="form-control form-control-modern" value="{{ $settings['story_title'] ?? '' }}" required>
                                    </div>
                                    <div class="form-group-modern">
                                        <label for="story_text"><i class="fas fa-align-left"></i> Contenido de la Tarjeta</label>
                                        <textarea name="story_text" id="story_text" rows="5" class="form-control" required>{{ $settings['story_text'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Misión y Visión -->
                            <div class="col-md-6 mb-4">
                                <div class="p-3 border rounded" style="background-color: #fafafa; border-left: 3px solid #D4AF37 !important;">
                                    <h5 class="font-weight-bold text-dark mb-3">Tarjeta 2: Misión & Visión</h5>
                                    <div class="form-group-modern mb-2">
                                        <label for="mission_title"><i class="fas fa-tag"></i> Título de la Tarjeta</label>
                                        <input type="text" name="mission_title" id="mission_title" class="form-control form-control-modern" value="{{ $settings['mission_title'] ?? '' }}" required>
                                    </div>
                                    <div class="form-group-modern">
                                        <label for="mission_text"><i class="fas fa-align-left"></i> Contenido de la Tarjeta</label>
                                        <textarea name="mission_text" id="mission_text" rows="5" class="form-control" required>{{ $settings['mission_text'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Materiales -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="p-3 border rounded" style="background-color: #fafafa; border-left: 3px solid #D4AF37 !important;">
                                    <h5 class="font-weight-bold text-dark mb-3">Sección 3: Calidad y Materiales</h5>
                                    <div class="form-group-modern mb-2">
                                        <label for="materials_title"><i class="fas fa-tag"></i> Título de la Sección</label>
                                        <input type="text" name="materials_title" id="materials_title" class="form-control form-control-modern" value="{{ $settings['materials_title'] ?? '' }}" required>
                                    </div>
                                    <div class="form-group-modern">
                                        <label for="materials_text"><i class="fas fa-align-left"></i> Contenido de la Sección</label>
                                        <textarea name="materials_text" id="materials_text" rows="4" class="form-control" required>{{ $settings['materials_text'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tab 3: Showcase Gallery -->
                <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                    <div class="setting-card">
                        <h4 class="section-title"><i class="fas fa-images"></i> Vitrina de Imágenes Destacadas</h4>
                        <p class="text-muted small mb-4"><i class="fas fa-info-circle"></i> Sube y administra las imágenes principales que componen el catálogo de vitrina de tu joyería. Se recomienda usar imágenes cuadradas.</p>
                        
                        <div class="form-group-modern mb-4" style="max-width: 300px;">
                            <label for="gallery_count"><i class="fas fa-list-ol"></i> Cantidad de imágenes a mostrar</label>
                            <select name="gallery_count" id="gallery_count" class="form-control form-control-modern">
                                @for ($c = 0; $c <= 12; $c++)
                                    <option value="{{ $c }}" {{ ($settings['gallery_count'] ?? 6) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endfor
                            </select>
                            <small class="form-text text-muted">Selecciona 0 para ocultar la sección de vitrina por completo en la portada.</small>
                        </div>

                        <div class="row">
                            @for ($i = 0; $i < 12; $i++)
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
                                <div class="col-md-4 mb-4" id="gallery_item_container_{{ $i }}">
                                    <div class="card h-100 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e2e2;">
                                        <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-3">
                                            <h6 class="font-weight-bold text-muted mb-2">Imagen Vitrina #{{ $i + 1 }}</h6>
                                            <div class="image-preview-box mb-3" onclick="triggerFileInput('gallery_image_{{ $i }}')">
                                                <img id="preview_gallery_image_{{ $i }}" src="{{ $imgUrl }}" alt="Vitrina {{ $i + 1 }}">
                                                <div class="image-preview-overlay">
                                                    <i class="fas fa-camera mr-2"></i> Cambiar Imagen
                                                </div>
                                            </div>
                                            <input type="file" name="gallery_image_{{ $i }}" id="gallery_image_{{ $i }}" class="hidden-file-input" accept="image/*" onchange="previewImage(this, 'preview_gallery_image_{{ $i }}')">
                                            <small class="text-muted"><i class="fas fa-hand-pointer mr-1"></i> Haz clic para seleccionar archivo</small>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Team -->
                <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                    <div class="setting-card">
                        <h4 class="section-title"><i class="fas fa-users"></i> Equipo de la Joyería</h4>
                        <p class="text-muted small mb-4"><i class="fas fa-info-circle"></i> Personaliza los nombres, cargos y fotos de los miembros del equipo que se mostrarán en la página principal.</p>
                        
                        <div class="form-group-modern mb-4" style="max-width: 300px;">
                            <label for="team_count"><i class="fas fa-list-ol"></i> Cantidad de miembros a mostrar</label>
                            <select name="team_count" id="team_count" class="form-control form-control-modern">
                                @for ($c = 0; $c <= 6; $c++)
                                    <option value="{{ $c }}" {{ ($settings['team_count'] ?? 3) == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endfor
                            </select>
                            <small class="form-text text-muted">Selecciona 0 para ocultar la sección del equipo por completo en la portada.</small>
                        </div>

                        <div class="row">
                            @for ($i = 0; $i < 6; $i++)
                                @php
                                    $member = $team[$i] ?? ['name' => '', 'role' => '', 'image' => ''];
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
                                <div class="col-md-4 mb-4" id="team_item_container_{{ $i }}">
                                    <div class="card h-100 shadow-sm" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #D4AF37;">
                                        <div class="card-body p-3">
                                            <h6 class="font-weight-bold text-center text-muted mb-3">Miembro #{{ $i + 1 }}</h6>
                                            <div class="d-flex justify-content-center mb-3">
                                                <div class="image-preview-box" style="width: 120px; height: 120px; border-radius: 50%; border: 2px solid #dee2e6;" onclick="triggerFileInput('team_image_{{ $i }}')">
                                                    <img id="preview_team_image_{{ $i }}" src="{{ $imgUrl }}" alt="Miembro {{ $i + 1 }}" style="border-radius: 50%;">
                                                    <div class="image-preview-overlay" style="border-radius: 50%;">
                                                        <i class="fas fa-camera"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="file" name="team_image_{{ $i }}" id="team_image_{{ $i }}" class="hidden-file-input" accept="image/*" onchange="previewImage(this, 'preview_team_image_{{ $i }}')">
                                            
                                            <div class="form-group-modern mb-2">
                                                <label for="team_name_{{ $i }}"><i class="fas fa-user"></i> Nombre Completo</label>
                                                <input type="text" name="team_name[]" id="team_name_{{ $i }}" class="form-control form-control-modern" value="{{ $member['name'] ?? '' }}">
                                            </div>
                                            <div class="form-group-modern">
                                                <label for="team_role_{{ $i }}"><i class="fas fa-briefcase"></i> Puesto / Rol</label>
                                                <input type="text" name="team_role[]" id="team_role_{{ $i }}" class="form-control form-control-modern" value="{{ $member['role'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Contact -->
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="setting-card">
                        <h4 class="section-title"><i class="fas fa-map-marked-alt"></i> Datos de Ubicación, Showroom & Contacto</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern mb-3">
                                    <label for="store_address"><i class="fas fa-map-pin"></i> Dirección del Showroom</label>
                                    <textarea name="store_address" id="store_address" rows="3" class="form-control" required>{{ $settings['store_address'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Calle, número, colonia, código postal y ciudad de tu establecimiento.</small>
                                </div>

                                <div class="form-group-modern mb-3">
                                    <label for="store_hours"><i class="fas fa-clock"></i> Horario de Atención</label>
                                    <input type="text" name="store_hours" id="store_hours" class="form-control form-control-modern" value="{{ $settings['store_hours'] ?? '' }}" required>
                                    <small class="form-text text-muted">Ejemplo: Lunes a Viernes 10:00 - 18:00 | Sábados 10:00 - 14:00.</small>
                                </div>

                                <div class="form-group-modern mb-3">
                                    <label for="contact_email"><i class="fas fa-envelope"></i> Correo Electrónico de Contacto</label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control form-control-modern" value="{{ $settings['contact_email'] ?? '' }}" required>
                                </div>

                                <div class="form-group-modern mb-3">
                                    <label for="contact_phone"><i class="fas fa-phone-alt"></i> Teléfono (WhatsApp - Solo números, ej: 529991234567)</label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control form-control-modern" value="{{ $settings['contact_phone'] ?? '' }}" required>
                                    <small class="form-text text-muted">Usa el formato internacional (código de país + número) para que el enlace directo de WhatsApp funcione correctamente sin problemas.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-modern mb-3">
                                    <label for="maps_iframe"><i class="fas fa-map"></i> Enlace de Mapa de Google Maps (URL de iframe src)</label>
                                    <textarea name="maps_iframe" id="maps_iframe" rows="5" class="form-control">{{ $settings['maps_iframe'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Inserta el enlace del mapa para embeber. Puedes obtenerlo en Google Maps -> Compartir -> Insertar un mapa -> Copia el valor de <b>src="..."</b>.</small>
                                </div>
                                @if(!empty($settings['maps_iframe']))
                                    <div class="mt-3">
                                        <h6 class="font-weight-bold text-muted mb-2">Vista Previa del Mapa:</h6>
                                        <div style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                            <iframe src="{{ $settings['maps_iframe'] }}" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Floating Save Bar -->
            <div class="card shadow-lg mt-4" style="border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <span class="text-muted"><i class="fas fa-info-circle mr-1"></i> Asegúrate de revisar todos los campos antes de guardar.</span>
                    <button type="submit" class="btn btn-action" style="background:#D4AF37; color:#111; border:none; padding: 0.8rem 2.5rem;">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function triggerFileInput(id) {
        document.getElementById(id).click();
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateGalleryVisibility() {
        var count = parseInt(document.getElementById('gallery_count').value);
        for (var i = 0; i < 12; i++) {
            var container = document.getElementById('gallery_item_container_' + i);
            if (container) {
                if (i < count) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            }
        }
    }

    function updateTeamVisibility() {
        var count = parseInt(document.getElementById('team_count').value);
        for (var i = 0; i < 6; i++) {
            var container = document.getElementById('team_item_container_' + i);
            if (container) {
                if (i < count) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initial setup for visibility
        var galleryCountEl = document.getElementById('gallery_count');
        if (galleryCountEl) {
            galleryCountEl.addEventListener('change', updateGalleryVisibility);
            updateGalleryVisibility();
        }
        var teamCountEl = document.getElementById('team_count');
        if (teamCountEl) {
            teamCountEl.addEventListener('change', updateTeamVisibility);
            updateTeamVisibility();
        }

        @if(session('mensaje'))
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: "{{ session('mensaje') }}",
                confirmButtonColor: '#D4AF37',
                timer: 3000
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#D4AF37'
            });
        @endif
    });
</script>
@endsection
