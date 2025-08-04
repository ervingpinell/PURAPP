@vite([
  'resources/css/tour-carousel.css',
  'resources/js/tour-carousel.js'
])

@php
    $images = collect($tour->images)
                ->pluck('url')
                ->map(fn($u) => asset($u))
                ->all();
    if (empty($images)) {
      $images = [asset('images/volcano.png')];
    }

    // Aseguramos al menos 3 slides
    while (count($images) < 3) {
        $images[] = $images[0];
    }
@endphp

<div id="tourCarousel"
     class="carousel slide shadow rounded mb-4"
     data-bs-ride="carousel"
     style="height: var(--tour-h);"
>
  <div class="row gx-2 h-100 flex-column-reverse flex-md-row">

    {{-- ■■■ MINIATURAS DESKTOP ■■■ --}}
    @if(count($images) > 1)
      <div class="col-auto d-none d-md-flex flex-column gap-2 pe-2 thumb-box">
        @foreach($images as $i => $src)
          <img src="{{ $src }}"
               class="{{ $i === 0 ? 'active' : '' }}"
               data-bs-target="#tourCarousel"
               data-bs-slide-to="{{ $i }}"
               role="button"
               alt="Miniatura {{ $i + 1 }}">
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
                 style="object-fit: cover;"
                 alt="Slide {{ $i + 1 }}"
                 loading="lazy">
          </div>
        @endforeach
      </div>

      {{-- ← → CONTROLES (visible en todos los tamaños) --}}
      <button class="carousel-control-prev"
              type="button"
              data-bs-target="#tourCarousel"
              data-bs-slide="prev"
              aria-label="Slide anterior">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button class="carousel-control-next"
              type="button"
              data-bs-target="#tourCarousel"
              data-bs-slide="next"
              aria-label="Slide siguiente">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>

      {{-- ••• INDICADORES MOBILE (solo mobile) --}}
      @if(count($images) > 1)
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
