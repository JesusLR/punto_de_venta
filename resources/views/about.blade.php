@extends('maestra_api')
@section('titulo', 'Sobre Nosotros')
@section('contenido')

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<style>
  :root{
    --gold:#D4AF37;
    --dark:#111213;
    --muted:#6b6b6b;
    --card:#ffffff;
  }

  .about-hero{
    background: linear-gradient(180deg, #0f0f10 0%, #171718 60%);
    color: #f6efe6;
    border-radius: 12px;
    padding: 3rem 1.25rem;
    margin-bottom: 1.75rem;
    display:flex;
    gap:2rem;
    align-items:center;
    justify-content:space-between;
  }
  .about-hero .left{max-width:66%;}
  .about-hero h1{color:var(--gold);font-size:2.1rem;margin:0 0 .5rem;font-weight:800}
  .about-hero p{color:#e8dfcf;margin:0;font-size:1.05rem}

  .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem}
  .about-card{background:var(--card);border-radius:12px;padding:1.4rem;box-shadow:0 8px 30px rgba(0,0,0,0.06)}
  .about-card h3{margin:0 0 .6rem;color:var(--dark);font-weight:800}
  .about-card p{margin:0;color:var(--muted);line-height:1.5}

  .values{display:flex;gap:1rem;flex-wrap:wrap;margin-top:.75rem}
  .value-pill{background:linear-gradient(90deg, rgba(212,175,55,0.12), rgba(212,175,55,0.04));padding:.6rem .9rem;border-radius:999px;font-weight:700;color:#2a2a2a}

  .gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-top:1rem}
  .gallery img{width:100%;height:140px;object-fit:cover;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,0.06)}

  .team{display:flex;gap:1rem;flex-wrap:wrap;margin-top:.75rem}
  .team-member{flex:1 1 200px;background:var(--card);padding:1rem;border-radius:10px;text-align:center}
  .team-member img{width:92px;height:92px;border-radius:999px;object-fit:cover;margin-bottom:.5rem}

  .contact-cta{background:linear-gradient(90deg,#111,#1b1b1b);color:#fff;padding:1.25rem;border-radius:12px;display:flex;gap:1rem;align-items:center;justify-content:space-between}
  .contact-cta .left{max-width:70%}
  .btn-contact{background:var(--gold);color:#111;padding:.8rem 1.1rem;border-radius:10px;font-weight:800;border:none}

  @media (max-width:900px){
    .about-hero{flex-direction:column;align-items:flex-start}
    .about-hero .left{max-width:100%}
    .about-grid{grid-template-columns:1fr}
    .gallery img{height:120px}
  }
</style>

<div class="container">

  <section class="about-hero">
    <div class="left">
      <h1><i class="fas fa-gem" style="margin-right:.6rem;color:var(--gold)"></i> {{ env('APP_NAME') }}</h1>
      <p>Somos una casa de joyería dedicada a diseñar piezas únicas y atemporales. Combinámos artesanía tradicional con acabados contemporáneos para ofrecer calidad, exclusividad y longevidad.</p>
      <div class="values" style="margin-top:.9rem">
        <div class="value-pill">Artesanía</div>
        <div class="value-pill">Materiales nobles</div>
        <div class="value-pill">Diseño exclusivo</div>
      </div>
    </div>
    <div class="right" style="text-align:right">
      {{-- <p style="color:#e8dfcf;font-weight:700;margin-bottom:.6rem">Visítanos</p> --}}
      {{-- <p style="color:#e8dfcf;opacity:.9">Showroom · Atención personalizada · Envíos a todo el país</p> --}}
    </div>
  </section>

  <section class="about-grid">
    <div class="about-card">
      <h3>Nuestra historia</h3>
      <p>Desde nuestros inicios hemos trabajado cuidadosamente cada pieza: desde la selección de metales y piedras, hasta el acabado final. Nuestro compromiso es crear joyas que acompañen historias.</p>
    </div>

    <div class="about-card">
      <h3>Misión & Visión</h3>
      <p>Misión: ofrecer piezas de joyería artesanales con diseño contemporáneo y responsabilidad en los materiales. Visión: ser referente regional en piezas exclusivas y asesoría personalizada.</p>
    </div>
  </section>

  <section class="about-card" style="margin-bottom:1rem">
    <h3 style="margin-bottom:.6rem">Materiales y calidad</h3>
    <p style="margin-bottom:.8rem">Trabajamos con oro, plata y aleaciones certificadas; piedras seleccionadas por su belleza y durabilidad. Cada pieza pasa por controles de calidad estrictos.</p>

    <div class="gallery">
      @isset($lstProducto)
        @foreach($lstProducto->take(6) as $p)
          <img class="about-gallery-item" data-name="{{ $p->img ?? '' }}" src="{{ $p->img ? asset('img/productos/'.$p->img) : asset('img/logo.jpg') }}" alt="{{ $p->descripcion }}">
        @endforeach
      @else
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
        <img class="about-gallery-item" src="{{ asset('img/logo.jpg') }}" alt="sample">
      @endisset
    </div>

    @auth
    <div style="margin-top:.75rem;">
        <button id="btnEditGallery" class="btn btn-dark"><i class="fas fa-edit"></i> Editar imágenes del catálogo</button>
    </div>

    <!-- Modal editar imágenes -->
    <div class="modal fade" id="editGalleryModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <h5>Reemplazar imagen</h5>
            <p class="small text-muted">Selecciona una imagen del grid para reemplazarla.</p>

            <div id="gallerySelector" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:.75rem;">
              <!-- thumbnails cloned by JS -->
            </div>

            <div class="form-group">
              <input type="file" id="aboutImageFile" accept="image/*" class="form-control-file">
            </div>

            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button id="btnUploadAboutImage" type="button" class="btn btn-success">Subir</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endauth

  </section>

  <section style="margin-top:1rem">
    <h3 style="margin-bottom:.6rem">Nuestro equipo</h3>
    <div class="team">
      <div class="team-member">
        <img src="{{ asset('img/logo.jpg') }}" alt="miembro">
        <div style="font-weight:700">Director creativo</div>
        <div style="color:var(--muted)">Diseño y desarrollo de colecciones</div>
      </div>
      <div class="team-member">
        <img src="{{ asset('img/logo.jpg') }}" alt="miembro">
        <div style="font-weight:700">Maestro joyero</div>
        <div style="color:var(--muted)">Acabados y control de calidad</div>
      </div>
      <div class="team-member">
        <img src="{{ asset('img/logo.jpg') }}" alt="miembro">
        <div style="font-weight:700">Atención al cliente</div>
        <div style="color:var(--muted)">Asesoría y pedidos personalizados</div>
      </div>
    </div>
  </section>

  <!-- Ubicación (Google Maps embed) -->
  <section style="margin-top:1rem" class="about-card">
    <h3 style="margin-bottom:.6rem">Nuestra ubicación</h3>
    <p style="margin-bottom:.8rem">Visítanos en nuestro showroom. Usa el mapa para obtener indicaciones.</p>

    <div class="map-wrap" style="border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.06)">
      <iframe
        src="https://www.google.com/maps/embed?pb=!4v1770232199564!6m8!1m7!1sObmAkpfr3RHi2xXMMjq2Pg!2m2!1d21.28547093379538!2d-89.66330729701143!3f88.97710322739826!4f-9.231406347633083!5f1.3700067333042356"
        width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <div style="display:flex;gap:0.6rem;margin-top:0.9rem;flex-wrap:wrap;align-items:center;justify-content:space-between">
      <div style="color:var(--muted)">
        <strong>Dirección:</strong> {{ env('STORE_ADDRESS', 'Av. Ejemplo 123, Ciudad') }}<br>
        <strong>Horario:</strong> {{ env('STORE_HOURS', 'Lun - Vie 10:00 - 18:00') }}
      </div>

      <div style="display:flex;gap:0.6rem">
        <a class="btn-contact" href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode(env('STORE_ADDRESS','Av. Ejemplo 123, Ciudad')) }}" target="_blank" rel="noopener">Cómo llegar</a>

        <a class="btn-contact" style="background:#333;color:#fff" href="{{ route('catalogoProductos.index') }}">
          <i class="fas fa-th-large"></i>&nbsp; Ver catálogo
        </a>
      </div>
    </div>
  </section>

  <!-- Contact CTA (mantener) -->
  <section style="margin-top:1.25rem">
    <div class="contact-cta">
      <div class="left">
        <div style="font-weight:800;font-size:1.05rem">¿Deseas asesoría personalizada?</div>
        <div style="color:#dcd1b8;margin-top:.25rem">Agendamos una cita o te atendemos por WhatsApp y correo.</div>
      </div>
      <div class="right" style="display:flex;gap:.6rem;align-items:center">
        <a class="btn-contact" href="mailto:info@empresa.com">info@empresa.com</a>
        <a class="btn-contact" style="background:#25D366" href="https://wa.me/{{ env('CONTACT_PHONE', '') }}" target="_blank" rel="noopener">
          <i class="fab fa-whatsapp"></i>&nbsp; WhatsApp
        </a>
        <a class="btn-contact" style="background:transparent;border:1px solid rgba(212,175,55,.16);color:var(--gold)" href="{{ route('catalogoProductos.index') }}">
          <i class="fas fa-gem"></i>&nbsp; Catálogo
        </a>
      </div>
    </div>
  </section>

  <footer style="margin-top:1.5rem;text-align:center;color:var(--muted)">
    <div style="font-weight:700;color:var(--gold)">{{ env('APP_NAME') }}</div>
    <div style="font-size:.95rem;margin-top:.25rem">© {{ date('Y') }} · Todos los derechos reservados</div>
  </footer>

</div>

@endsection

@auth
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btnEdit = document.getElementById('btnEditGallery');
  const modal = $('#editGalleryModal');
  const gallerySelector = document.getElementById('gallerySelector');
  let selectedIndex = null;

  btnEdit && btnEdit.addEventListener('click', function(){
    // build selector from current gallery thumbnails
    gallerySelector.innerHTML = '';
    document.querySelectorAll('.about-gallery-item').forEach((img, idx) => {
      const thumb = document.createElement('img');
      thumb.src = img.src;
      thumb.style.width = '100%';
      thumb.style.height = '80px';
      thumb.style.objectFit = 'cover';
      thumb.style.cursor = 'pointer';
      thumb.style.border = '2px solid transparent';
      thumb.dataset.index = idx;
      thumb.addEventListener('click', function(){
        // mark selected
        gallerySelector.querySelectorAll('img').forEach(i=> i.style.border='2px solid transparent');
        this.style.border = '2px solid #28a745';
        selectedIndex = parseInt(this.dataset.index);
      });
      gallerySelector.appendChild(thumb);
    });
    modal.modal('show');
  });

  document.getElementById('btnUploadAboutImage').addEventListener('click', function(){
    if (selectedIndex === null) { alert('Selecciona una miniatura para reemplazar.'); return; }
    const fileInput = document.getElementById('aboutImageFile');
    if (!fileInput.files.length) { alert('Selecciona una imagen.'); return; }
    const file = fileInput.files[0];
    const fd = new FormData();
    fd.append('image', file);

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    this.disabled = true;
    this.textContent = 'Subiendo...';

    fetch("{{ route('about.upload.image') }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token },
      body: fd
    }).then(r=> r.json())
      .then(json => {
        if (json.url) {
          // update gallery thumbnail on page
          const imgs = document.querySelectorAll('.about-gallery-item');
          if (imgs[selectedIndex]) {
            imgs[selectedIndex].src = json.url;
            imgs[selectedIndex].dataset.name = json.name || '';
          }
          modal.modal('hide');
        } else {
          alert('Error subiendo imagen');
        }
      })
      .catch(()=> alert('Error subiendo imagen'))
      .finally(()=> { this.disabled = false; this.textContent = 'Subir'; });
  });
});
</script>
@endauth