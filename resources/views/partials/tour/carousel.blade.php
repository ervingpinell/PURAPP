@php
use Illuminate\Support\Facades\Storage;
$images = collect();

if (isset($tour)) {
$imgs = $tour->relationLoaded('images')
? $tour->getRelation('images')
: (method_exists($tour, 'images') ? $tour->images()->get() : collect());

if ($imgs && $imgs->count()) {
$imgs = $imgs->sortBy([
['is_cover', 'desc'],
['position', 'asc'],
['id', 'asc'],
])->values();

// getUrlAttribute() of TourImage model
$images = $imgs->map(fn ($img) => $img->url)->values();
}
}
if ($images->isEmpty()) {
$tourId = $tour->tour_id ?? $tour->id ?? null;
$folder = $tourId ? "tours/{$tourId}/gallery" : null;

if ($folder && Storage::disk('public')->exists($folder)) {
$allowed = ['jpg','jpeg','png','webp'];
$files = collect(Storage::disk('public')->files($folder))
->filter(function ($path) use ($allowed) {
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
return in_array($ext, $allowed, true);
})
->sort(fn ($a, $b) => strnatcasecmp($a, $b))
->values();

$images = $files->map(fn ($p) => asset('storage/'.$p));
}
}
if ($images->isEmpty()) {
$images = collect([asset('images/volcano.png')]);
}
while ($images->count() < 5) {
  $images->push($images->first());
  }
  $images = $images->values();
  $intervalMs = 8000; // 8s
  @endphp

  <style>
    /* ===== Lightbox centrado en cualquier dispositivo ===== */
    #tourLightbox .modal-dialog {
      /* ancho controlado, sin empujarlo hacia abajo por márgenes */
      margin: 0 auto;
      width: auto;
      max-width: min(1100px, 96vw);
    }

    #tourLightbox .modal-content {
      background: #000;
      border: 0;
    }

    /* Centrado vertical robusto con viewport dinámico (iOS/Android barras) */
    #tourLightbox.show .modal-dialog {
      display: flex;
      align-items: center;
      min-height: 100dvh;
      /* OK clave para iPad/iOS */
    }

    @supports not (height: 100dvh) {

      /* fallback para navegadores muy viejos */
      #tourLightbox.show .modal-dialog {
        min-height: 100vh;
      }
    }

    /* Cuerpo sin padding, centrado perfecto del carrusel */
    #tourLightbox .modal-body {
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      /* respiración en safe-areas (notch) */
      padding-top: max(0px, env(safe-area-inset-top));
      padding-bottom: max(0px, env(safe-area-inset-bottom));
    }

    /* Imagen grande: contener sin recortes y sin salirse del viewport */
    #tourLightbox .carousel-item img {
      display: block;
      width: 100%;
      height: auto;
      max-width: 100%;
      /* 100dvh menos un pequeño margen visual y safe areas */
      max-height: calc(100dvh - 24px - env(safe-area-inset-top, 0px) - env(safe-area-inset-bottom, 0px));
      object-fit: contain;
      background: #000;
    }

    @supports not (height: 100dvh) {
      #tourLightbox .carousel-item img {
        max-height: calc(100vh - 24px);
      }
    }

    /* Botón de cierre siempre visible en la esquina segura */
    #tourLightbox .modal-body>.btn,
    #tourLightbox .modal-body .btn-close {
      position: absolute;
      top: max(8px, env(safe-area-inset-top));
      right: max(8px, env(safe-area-inset-right));
      z-index: 5;
    }

    /* Opcional: en móviles muy pequeños, usa casi pantalla completa */
    @media (max-width: 576.98px) {
      #tourLightbox .modal-dialog {
        max-width: 100vw;
      }
    }
  </style>

  <div id="tourCarousel"
    class="carousel slide shadow rounded mb-4"
    data-bs-ride="carousel"
    data-bs-interval="{{ $intervalMs }}"
    data-bs-touch="true"
    style="height:462px;max-height:462px;min-height:462px;">

    <div class="row gx-2 h-100 flex-column-reverse flex-md-row">
      {{-- Thumbnails (Desktop) --}}
      @if($images->count() > 1)
      <div class="col-auto d-none d-md-flex flex-column gap-2 pe-2 thumb-box">
        @foreach($images as $i => $src)
        <img src="{{ $src }}"
          class="{{ $i === 0 ? 'active' : '' }}"
          data-bs-target="#tourCarousel"
          data-bs-slide-to="{{ $i }}"
          data-open-lightbox
          data-index="{{ $i }}"
          role="button"
          width="100"
          height="75"
          alt="Miniatura {{ $i + 1 }}">
        @endforeach
      </div>
      @endif

      {{-- Image --}}
      <div class="col position-relative h-100">
        <div class="carousel-inner h-100 rounded shadow-sm overflow-hidden">
          @foreach($images as $i => $src)
          <div class="carousel-item {{ $i === 0 ? 'active' : '' }} h-100">
            <img src="{{ $src }}"
              class="d-block w-100 h-100"
              style="object-fit: cover; cursor: pointer;"
              alt="{{ $tour->getTranslatedName() }} - {{ $i + 1 }}"
              data-open-lightbox
              data-index="{{ $i }}"
              width="800"
              height="600"
              loading="lazy">
            
            {{-- Botón Expandir --}}
            <button type="button" 
                    class="btn btn-dark btn-sm position-absolute top-0 end-0 m-3 bg-opacity-50 border-0 rounded-circle d-flex align-items-center justify-content-center" 
                    style="width: 36px; height: 36px; z-index: 5;"
                    data-open-lightbox
                    data-index="{{ $i }}"
                    title="{{ __('adminlte::adminlte.expand_image') ?? 'Expandir' }}">
                <i class="fas fa-expand fa-sm text-white"></i>
            </button>
          </div>
          @endforeach
        </div>

        {{-- Controls --}}
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

        {{-- Indicators (only mobile) --}}
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

  {{-- Lightbox (Modal) --}}
  <div class="modal fade" id="tourLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content bg-black">
        <div class="modal-body position-relative">
          <button type="button" class="btn btn-light position-absolute top-0 end-0 m-2"
            data-bs-dismiss="modal" aria-label="Close">×</button>

          <div id="tourLightboxCarousel"
            class="carousel slide"
            data-bs-ride="false"
            data-bs-interval="false"
            data-bs-touch="true">
            <div class="carousel-inner">
              @foreach($images as $i => $src)
              <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ $src }}" alt="{{ $tour->getTranslatedName() }} - Large {{ $i + 1 }}" loading="lazy">
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
      const mainEl = document.getElementById('tourCarousel');
      const main = bootstrap.Carousel.getOrCreateInstance(mainEl, {
        interval: {{ $intervalMs }},
        ride: 'carousel',
        touch: true,
        pause: 'hover',
        wrap: true
      });

      const thumbs = mainEl.querySelectorAll('.thumb-box img');
      if (thumbs.length) {
        mainEl.addEventListener('slid.bs.carousel', (ev) => {
          thumbs.forEach(t => t.classList.remove('active'));
          const idx = ev.to ?? ev.relatedTarget?.dataset.bsSlideTo ?? 0;
          if (thumbs[idx]) thumbs[idx].classList.add('active');
        });
      }

      // Lightbox
      const lbEl = document.getElementById('tourLightbox');
      const lbCarouselEl = document.getElementById('tourLightboxCarousel');
      const lb = bootstrap.Carousel.getOrCreateInstance(lbCarouselEl, {
        interval: false,
        ride: false,
        touch: true,
        pause: false,
        wrap: true
      });

      mainEl.querySelectorAll('[data-open-lightbox]').forEach((img) => {
        img.addEventListener('click', (e) => {
          const idx = Number(e.currentTarget.dataset.index || 0);
          const modal = new bootstrap.Modal(lbEl, {
            backdrop: true,
            keyboard: true
          });
          modal.show();
          lb.to(idx);
        });
      });

      // Allow scrolling usually, only blocking if necessary (removed block for now)
      // lbEl.addEventListener('wheel', (e) => e.preventDefault(), {
      //   passive: false
      // });
      // lbEl.addEventListener('touchmove', (e) => e.preventDefault(), {
      //   passive: false
      // });
    });
  </script>