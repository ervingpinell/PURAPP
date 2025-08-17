{{-- resources/views/partials/tour/carousel.blade.php --}}
@php
    use Illuminate\Support\Facades\Storage;

    // 1) Intentar desde BD (tour_images)
    $images = collect();

    if (isset($tour)) {
        // Trae la colección, venga cargada o no
        $imgs = $tour->relationLoaded('images')
            ? $tour->getRelation('images')
            : (method_exists($tour, 'images') ? $tour->images()->get() : collect());

        if ($imgs && $imgs->count()) {
            // Reordenar SIEMPRE: cover primero, luego posición, luego id
            $imgs = $imgs->sortBy([
                ['is_cover', 'desc'],
                ['position', 'asc'],
                ['id', 'asc'],
            ])->values();

            // Accesor getUrlAttribute() del modelo TourImage
            $images = $imgs->map(fn ($img) => $img->url)->values();
        }
    }

    // 2) Fallback: leer carpeta storage/app/public/tours/{tour_id}/gallery
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

    // 3) Último fallback
    if ($images->isEmpty()) {
        $images = collect([asset('images/volcano.png')]);
    }

    // 4) Asegurar mínimo 4 slides (opcional)
    while ($images->count() < 4) {
        $images->push($images->first());
    }
    $images = $images->values();

    // Intervalo del carrusel principal (ms)
    $intervalMs = 8000; // 8s
@endphp

<style>
  #tourCarousel .thumb-box img {
    width: 92px; height: 92px; object-fit: cover; border-radius: .5rem;
    border: 2px solid transparent; cursor: pointer;
  }
  #tourCarousel .thumb-box img.active { border-color: #198754; }
  #tourCarousel .thumb-box { max-height: 462px; overflow: auto; }
  #tourLightbox .modal-dialog { max-width: min(1100px, 95vw); }
  #tourLightbox .modal-body { padding: 0; background: #000; }
  #tourLightbox .carousel-item img {
    width: 100%; max-height: 82vh; object-fit: contain; background: #000;
  }
</style>

<div id="tourCarousel"
     class="carousel slide shadow rounded mb-4"
     data-bs-ride="carousel"
     data-bs-interval="{{ $intervalMs }}"
     data-bs-touch="false"
     style="height:462px;max-height:462px;min-height:462px;">

  <div class="row gx-2 h-100 flex-column-reverse flex-md-row">

    @if($images->count() > 1)
      {{-- Miniaturas (solo desktop) --}}
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

    {{-- Imagen principal --}}
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

      {{-- Controles --}}
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

{{-- Lightbox (Modal) --}}
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
  const mainEl = document.getElementById('tourCarousel');
  const main = bootstrap.Carousel.getOrCreateInstance(mainEl, {
    interval: {{ $intervalMs }},
    ride: 'carousel',
    touch: false,
    pause: 'hover',
    wrap: true
  });

  // Mantener miniatura activa en desktop
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
    interval: false, ride: false, touch: false, pause: false, wrap: true
  });

  mainEl.querySelectorAll('[data-open-lightbox]').forEach((img) => {
    img.addEventListener('click', (e) => {
      const idx = Number(e.currentTarget.dataset.index || 0);
      const modal = new bootstrap.Modal(lbEl, { backdrop: true, keyboard: true });
      modal.show();
      lb.to(idx);
    });
  });

  // Evitar scroll accidental dentro del modal
  lbEl.addEventListener('wheel', (e) => e.preventDefault(), { passive: false });
  lbEl.addEventListener('touchmove', (e) => e.preventDefault(), { passive: false });
});
</script>
