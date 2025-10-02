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

<h2 class="big-title text-center" style="color: var(--primary-dark);">
  {{ __('adminlte::adminlte.our_tours') }}
</h2>

<div class="tour-cards">
@foreach ($typeMeta as $key => $meta)
  @php
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
  @endphp

  {{-- Tarjeta del tipo --}}
  <div class="tour-card" style="cursor:pointer"
       data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug((string)$key) }}">
    <img src="{{ $firstCover }}" class="card-img-top" alt="{{ $translatedTitle }}">
    <div class="card-body d-flex flex-column h-100">
      <h5 class="card-title">{{ $translatedTitle }}</h5>

      @if(!empty($translatedDuration))
        <p class="card-text text-muted">{{ $translatedDuration }}</p>
      @endif

      @if(!empty($translatedDescription))
        <p class="card-text small">{!! nl2br(e($translatedDescription)) !!}</p>
      @endif

      <a href="javascript:void(0)" class="btn btn-success w-100 btn-ver-tour"
         data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug((string)$key) }}">
        {{ __('adminlte::adminlte.see_tours') }}
      </a>
    </div>
  </div>

  {{-- Modal por tipo --}}
  <div class="modal fade" id="modal-{{ Str::slug((string)$key) }}" tabindex="-1"
       aria-labelledby="modalLabel-{{ Str::slug((string)$key) }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header text-white" style="background:#0f2419">
          <h5 class="modal-title text-center w-100 text-white"
              id="modalLabel-{{ Str::slug((string)$key) }}">
            {{ $translatedTitle }}
          </h5>
          <button type="button" class="btn-close bg-light"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              @foreach ($group as $tour)
                @php
                  $tourCover = optional($tour->coverImage)->url
                      ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);
                @endphp

                <div class="col-12 col-md-6 col-xl-4 mb-4">
                  <div class="tour-modal-card h-100">
                    <img src="{{ $tourCover }}" class="card-img-top" alt="{{ $tour->getTranslatedName() }}">
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title card-title-text">{{ $tour->getTranslatedName() }}</h5>

                      @php
                        $rawLength = $tour->length;
                        $unit  = __('adminlte::adminlte.horas');
                        $label = __('adminlte::adminlte.duration');
                      @endphp
                      @if(!empty($rawLength))
                        <p class="text-muted small mb-2"><strong>{{ $label }}:</strong> {{ $rawLength }} {{ $unit }}</p>
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
