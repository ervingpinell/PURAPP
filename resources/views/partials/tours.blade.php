@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

function estimate_rows(string $text, int $charsPerLine): int {
    $t = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    $len = mb_strlen($t);
    if ($len <= 0) return 1;
    return max(1, (int)ceil($len / max(1, $charsPerLine)));
}

function max_rows_for_group(iterable $items, callable $getTitle, int $charsPerLine, int $min=1, int $max=4): int {
    $maxRows = 1;
    foreach ($items as $it) {
        $title = (string)$getTitle($it);
        $rows  = estimate_rows($title, $charsPerLine);
        if ($rows > $maxRows) $maxRows = $rows;
    }
    return min($max, max($min, $maxRows));
}

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

$typeTitles = collect($typeMeta)
    ->map(fn($m) => (string)($m['title'] ?? ''))
    ->filter();

$homeRowsXS = min(4, max(1, (int)ceil($typeTitles->map(fn($t)=>estimate_rows($t, 14))->max() ?? 1)));
$homeRowsSM = min(4, max(1, (int)ceil($typeTitles->map(fn($t)=>estimate_rows($t, 18))->max() ?? 1)));
$homeRowsMD = min(4, max(1, (int)ceil($typeTitles->map(fn($t)=>estimate_rows($t, 24))->max() ?? 1)));
$homeRowsLG = min(4, max(1, (int)ceil($typeTitles->map(fn($t)=>estimate_rows($t, 32))->max() ?? 1)));
@endphp

<div class="tour-cards"
     style="--title-rows-xs:{{$homeRowsXS}};--title-rows-sm:{{$homeRowsSM}};--title-rows-md:{{$homeRowsMD}};--title-rows-lg:{{$homeRowsLG}};">

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

      $slugKey = Str::slug((string)$key);

      $rowsXS = max_rows_for_group($group, fn($t)=>$t->getTranslatedName(), 14, 1, 4);
      $rowsSM = max_rows_for_group($group, fn($t)=>$t->getTranslatedName(), 18, 1, 4);
      $rowsMD = max_rows_for_group($group, fn($t)=>$t->getTranslatedName(), 24, 1, 4);
      $rowsLG = max_rows_for_group($group, fn($t)=>$t->getTranslatedName(), 32, 1, 4);
  @endphp

  {{-- Tarjeta del tipo (HOME) --}}
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
       aria-labelledby="modalLabel-{{ $slugKey }}" aria-hidden="true"
       style="--title-rows-xs:{{$rowsXS}};--title-rows-sm:{{$rowsSM}};--title-rows-md:{{$rowsMD}};--title-rows-lg:{{$rowsLG}};">
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
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 justify-content-center tour-grid">
              @foreach ($group as $tour)
                @php
                  $tourCover = optional($tour->coverImage)->url
                      ?? $coverFromFolder($tour->tour_id ?? $tour->id ?? null);

                  $unitLabel = __('adminlte::adminlte.horas');
                  $durLabel  = __('adminlte::adminlte.duration');

                  // ✅ Usar la relación ya cargada (no volver a consultar):
                  // Debes tener prices -> category.translations eager-loaded en el controller.
                  $activeCategories = collect($tour->prices ?? [])
                      ->filter(fn($p) => $p->is_active && $p->category && $p->category->is_active)
                      ->sortBy('category_id')
                      ->values();

                  // Helper local para nombre traducido de categoría
                  $catName = function ($cat) {
                      if (!$cat) return 'N/A';
                      // Si el modelo tiene el helper:
                      if (method_exists($cat, 'getTranslatedName')) {
                          return $cat->getTranslatedName(app()->getLocale());
                      }
                      // Si no, buscamos en translations cargadas:
                      $loc = app()->getLocale();
                      $fb  = config('app.fallback_locale', 'es');
                      $t   = optional($cat->translations);
                      return $t->firstWhere('locale', $loc)->name
                          ?? $t->firstWhere('locale', $fb)->name
                          ?? ($cat->display_name ?? $cat->name ?? 'N/A');
                  };

                  // Helper para rango de edad real
                  $ageRangeText = function ($cat) {
                      if (!$cat) return null;
                      $from = $cat->age_from;
                      $to   = $cat->age_to;
                      if (is_null($from) && is_null($to)) return null;
                      if (!is_null($from) && is_null($to)) return "{$from}+";
                      if (is_null($from) && !is_null($to)) return "0–{$to}";
                      return "{$from}–{$to}";
                  };
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

                      @if($activeCategories->isNotEmpty())
                        <div class="mb-3 small mt-auto">
                          @foreach($activeCategories as $priceRecord)
                            @php
                              $category   = $priceRecord->category;
                              $nameTr     = $catName($category);
                              $price      = (float) $priceRecord->price;
                              $ageText    = $ageRangeText($category);
                            @endphp
                            <div class="d-flex justify-content-between">
                              <div>
                                <strong>{{ $nameTr }}</strong>
                                @if($ageText)
                                  <small>({{ $ageText }})</small>
                                @endif
                              </div>
                              <strong style="color:#006633">${{ number_format($price, 2) }}</strong>
                            </div>
                          @endforeach
                        </div>
                      @else
                        <div class="mb-3 small mt-auto text-muted">
                          {{ __('adminlte::adminlte.no_prices_available') ?? 'Precios no disponibles' }}
                        </div>
                      @endif

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
