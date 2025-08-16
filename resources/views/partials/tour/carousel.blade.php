{{-- resources/views/partials/tour/carousel.blade.php --}}
@php
    use Illuminate\Support\Facades\Storage;

    // Carpeta esperada: storage/app/public/tours/{tour_id}/gallery
    $tourId = $tour->tour_id ?? $tour->id ?? null;
    $folder = $tourId ? "tours/{$tourId}/gallery" : null;

    // Busca archivos válidos en la carpeta
    $images = collect();
    if ($folder && Storage::disk('public')->exists($folder)) {
        $allowed = ['jpg','jpeg','png','webp'];
        $files = collect(Storage::disk('public')->files($folder))
            ->filter(function ($path) use ($allowed) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                return in_array($ext, $allowed, true);
            })
            // orden natural: 01.jpg, 2.jpg, 10.jpg…
            ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
            ->values();

        $images = $files->map(fn ($p) => asset('storage/'.$p));
    }

    // Fallback si no hay imágenes
    if ($images->isEmpty()) {
        $images = collect([asset('images/volcano.png')]);
    }

    // Asegura al menos 4 slides duplicando la primera
    while ($images->count() < 4) {
        $images->push($images->first());
    }

    $images = $images->values(); // array limpio

    // Intervalo lento (ms) para el carrusel principal
    $intervalMs = 8000; // 8 segundos
@endphp

@vite([
  'resources/css/tour-carousel.css',
  'resources/js/tour-carousel.js'
])

<style>
  /* Ajustes mínimos por si tu CSS no lo trae */
  #tourCarousel .thumb-box img {
    width: 92px; height: 92px; object-fit: cover; border-radius: .5rem;
    border: 2px solid transparent; cursor: pointer;
  }
  #tourCarousel .thumb-box img.active { border-color: #198754; }
  #tourCarousel .thumb-box { max-height: 462px; overflow: auto; }
  /* Lightbox modal */
  #tourLightbox .modal-dialog { max-width: min(1100px, 95vw); }
  #tourLightbox .modal-body { padding: 0; background: #000; }
  #tourLightbox .carousel-item img {
    width: 100%; max-height: 82vh; object-fit: contain; background: #000;
  }
</style>

<div id="tourCarousel"
     class="carousel slide shadow rounded mb-4"
     data-bs-ride="carousel"                 {{-- auto con intervalo --}}
     data-bs-interval="{{ $intervalMs }}"    {{-- lento: 2s --}}
     data-bs-touch="false"                   {{-- sin swipe/scroll accidental --}}
     style="height:462px;max-height:462px;min-height:462px;">

  <div class="row gx-2 h-100 flex-column-reverse flex-md-row">

    {{-- ■■■ MINIATURAS DESKTOP (con scroll) ■■■ --}}
    @if($images->count() > 1)
      <div class="col-auto d-none d-md-flex flex-column gap-2 pe-2 thumb-box h-100">
        @foreach($images as $i => $src)
          <img src="{{ $src }}"
               class="{{ $i === 0 ? 'active' : '' }}"
               data-bs-target="#tourCarousel"
               data-bs-slide-to="{{ $i }}"
               role="button"
               alt="Thumbnail {{ $i + 1 }}"
               loading="lazy">
        @endforeach
      </div>
    @endif

    {{-- ■■■ IMAGEN PRINCIPAL ■■■ --}}
    <div class="col position-relative h-100">
      <div class="carousel-inner h-100 rounded shadow-sm overflow-hidden">
        @foreach($images as $i => $src)
          <div class="carousel-item {{ $i === 0 ? 'active' : '' }} h-100">
            <img src="{{ $src }}"
                 class="d-block w-100 h-100"
                 style="object-fit:cover; cursor: zoom-in;"
                 alt="Slide {{ $i + 1 }}"
                 data-open-lightbox="1"
                 data-index="{{ $i }}"
                 loading="lazy">
          </div>
        @endforeach
      </div>

      {{-- Controles ← → --}}
      <button class="carousel-control-prev" type="button"
              data-bs-target="#tourCarousel" data-bs-slide="prev"
              aria-label="Slide anterior">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>

      <button class="carousel-control-next" type="button"
              data-bs-target="#tourCarousel" data-bs-slide="next"
              aria-label="Slide siguiente">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>

      {{-- Indicadores (solo mobile) --}}
      @if($images->count() > 1)
        <div class="carousel-indicators d-md-none">
          @foreach($images as $i => $src)
            <button type="button"
                    data-bs-target="#tourCarousel"
                    data-bs-slide-to="{{ $i }}"
                    class="{{ $i === 0 ? 'active' : '' }}"
                    aria-label="Slide {{ $i + 1 }}"
                    aria-current="{{ $i === 0 ? 'true' : 'false' }}">
            </button>
          @endforeach
        </div>
      @endif
    </div>

  </div>
</div>

{{-- ■■■ LIGHTBOX (Modal Bootstrap) – sin auto-slide ■■■ --}}
<div class="modal fade" id="tourLightbox" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-black">
      <div class="modal-body position-relative">
        <button type="button" class="btn btn-light position-absolute top-0 end-0 m-2"
                data-bs-dismiss="modal" aria-label="Close">×</button>

        <div id="tourLightboxCarousel"
             class="carousel slide"
             data-bs-ride="false"
             data-bs-interval="false"
             data-bs-touch="false">
          <div class="carousel-inner">
            @foreach($images as $i => $src)
              <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ $src }}" alt="Large {{ $i + 1 }}">
              </div>
            @endforeach
          </div>

          <button class="carousel-control-prev" type="button"
                  data-bs-target="#tourLightboxCarousel" data-bs-slide="prev" aria-label="Prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button class="carousel-control-next" type="button"
                  data-bs-target="#tourLightboxCarousel" data-bs-slide="next" aria-label="Next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Carrusel principal – lento y sin swipe
  const mainEl = document.getElementById('tourCarousel');
  const main = bootstrap.Carousel.getOrCreateInstance(mainEl, {
    interval: {{ $intervalMs }}, // 8s
    ride: 'carousel',
    touch: false,
    pause: 'hover',
    wrap: true
  });

  // Marcos de miniaturas: mantén "active" en la que toca
  const thumbs = mainEl.querySelectorAll('.thumb-box img');
  if (thumbs.length) {
    mainEl.addEventListener('slid.bs.carousel', (ev) => {
      thumbs.forEach(t => t.classList.remove('active'));
      const idx = ev.to ?? ev.relatedTarget?.dataset.bsSlideTo ?? 0;
      if (thumbs[idx]) thumbs[idx].classList.add('active');
    });
  }

  // Lightbox (modal) sin auto-slide
  const lbEl = document.getElementById('tourLightbox');
  const lbCarouselEl = document.getElementById('tourLightboxCarousel');
  const lb = bootstrap.Carousel.getOrCreateInstance(lbCarouselEl, {
    interval: false, ride: false, touch: false, pause: false, wrap: true
  });

  // Abrir modal desde imagen principal
  mainEl.querySelectorAll('[data-open-lightbox]').forEach((img) => {
    img.addEventListener('click', (e) => {
      const idx = Number(e.currentTarget.dataset.index || 0);
      const modal = new bootstrap.Modal(lbEl, { backdrop: true, keyboard: true });
      modal.show();
      lb.to(idx);
    });
  });

  // También permite abrir desde miniaturas (opcional)
  thumbs.forEach((t, i) => {
    t.addEventListener('dblclick', () => {
      const modal = new bootstrap.Modal(lbEl, { backdrop: true, keyboard: true });
      modal.show();
      lb.to(i);
    });
  });

  // Bloquea el scroll / rueda dentro del modal para evitar cambios involuntarios
  lbEl.addEventListener('wheel', (e) => e.preventDefault(), { passive: false });
  lbEl.addEventListener('touchmove', (e) => e.preventDefault(), { passive: false });
});
</script>
