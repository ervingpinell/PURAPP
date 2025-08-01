<div id="tourCarousel" class="carousel slide shadow-sm rounded mb-3" data-bs-ride="carousel">
  <div class="carousel-inner rounded">
    @for ($i = 0; $i < 3; $i++)
      <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
        <img src="{{ asset('images/volcano.png') }}"
             class="d-block w-100"
             style="max-height: 350px; object-fit: cover;"
             alt="{{ $tour->translated_name ?? $tour->name }}">
      </div>
    @endfor
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#tourCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#tourCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
