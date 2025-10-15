@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

$coverFromFolder = function (?int $tourId): string {
    if (!$tourId) return asset('images/volcano.png');
    $folder = "tours/{$tourId}/gallery";
    if (!Storage::disk('public')->exists($folder)) return asset('images/volcano.png');

    $allowed = ['jpg','jpeg','png','webp'];
    $first = collect(Storage::disk('public')->files($folder))
        ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), $allowed, true))
        ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
        ->first();

    return $first ? asset('storage/'.$first) : asset('images/volcano.png');
};
@endphp

<div class="tour-cards">
@foreach ($typeMeta as $key => $meta)
  @php
      /** @var \Illuminate\Support\Collection $group */
      $group = $toursByType[$key] ?? collect();
      if ($group->isEmpty()) continue;

      $first = $group->first();

      $translatedTitle       = $meta['title']       ?? '';
      $translatedDuration    = $meta['duration']    ?? '';
      $translatedDescription = $meta['description'] ?? '';

      $typeCover  = $meta['cover_url'] ?? null;
      $firstCover = $typeCover
          ?: (optional($first->coverImage)->url
              ?? $coverFromFolder($first->tour_id ?? $first->id ?? null));

      $slugKey = Str::slug((string)$key);
  @endphp

  {{-- Tarjeta del tipo --}}
  <div class="tour-card" style="cursor:pointer"
       data-bs-toggle="modal" data-bs-target="#modal-{{ $slugKey }}">
    <img src="{{ $firstCover }}" class="card-img-top" alt="{{ $translatedTitle }}">
    <div class="card-body d-flex flex-column h-100 p-3">
      <h5 class="card-title mb-2">{{ $translatedTitle }}</h5>

      @if(!empty($translatedDuration))
        <p class="card-text text-muted mb-2">{{ $translatedDuration }}</p>
      @endif

      @if(!empty($translatedDescription))
        <p class="card-text small mb-3 flex-grow-1">{!! nl2br(e($translatedDescription)) !!}</p>
      @endif

      <a href="javascript:void(0)" class="btn btn-success w-100 btn-ver-tour mt-auto"
         data-bs-toggle="modal" data-bs-target="#modal-{{ $slugKey }}">
        {{ __('adminlte::adminlte.see_tours') }}
      </a>
    </div>
  </div>

  {{-- Modal por tipo --}}
  <div class="modal fade modal-fix-top" id="modal-{{ $slugKey }}" tabindex="-1"
       aria-labelledby="modalLabel-{{ $slugKey }}" aria-hidden="true">
    {{-- Fullscreen en sm- y centrado/scrollable en >sm --}}
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered modal-fullscreen-sm-down">
      <div class="modal-content">
        <div class="modal-header text-white" style="background:#0f2419">
          <h5 class="modal-title text-center w-100 text-white"
              id="modalLabel-{{ $slugKey }}">
            {{ $translatedTitle }}
          </h5>
          <button type="button" class="btn-close bg-light"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-2 px-sm-3">
            {{-- Grid centrado y responsivo: 1 col en móvil, 2 en sm, 3 en lg --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 justify-content-center tour-grid">
              @foreach ($group as $tour)
                @php
                  $tourCover = optional($tour->coverImage)->url
                      ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);

                  $unitLabel = __('adminlte::adminlte.horas');
                  $durLabel  = __('adminlte::adminlte.duration');
                @endphp

                <div class="col d-flex">
                  <div class="tour-modal-card h-100 w-100 d-flex flex-column">
                    <img src="{{ $tourCover }}" class="card-img-top" alt="{{ $tour->getTranslatedName() }}">
                    <div class="card-body d-flex flex-column p-3">
                      <h5 class="card-title card-title-text mb-2">{{ $tour->getTranslatedName() }}</h5>

                      @if(!empty($tour->length))
                        <p class="text small mb-2">
                          <strong class="muted">{{ $durLabel }}:</strong> {{ $tour->length }} {{ $unitLabel }}
                        </p>
                      @endif

                      <div class="mb-3 small mt-auto">
                        <div class="d-flex justify-content-between">
                          <div>
                            <strong>{{ __('adminlte::adminlte.adult') }}</strong>
                            <small>({{ __('adminlte::adminlte.age_10_plus') }})</small>
                          </div>
                          <strong style="color:#006633">${{ number_format($tour->adult_price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                          <div>
                            <strong>{{ __('adminlte::adminlte.kid') }}</strong>
                            <small>({{ __('adminlte::adminlte.age_4_to_9') }})</small>
                          </div>
                          <strong style="color:#006633">${{ number_format($tour->kid_price, 2) }}</strong>
                        </div>
                      </div>

                      <a href="{{ localized_route('tours.show', $tour) }}" class="btn btn-success w-100 mt-2">
                        {{ __('adminlte::adminlte.see_tour') }}
                      </a>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            {{ __('adminlte::adminlte.close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endforeach
</div>
