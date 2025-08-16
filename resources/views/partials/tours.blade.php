{{-- resources/views/partials/tour/tours.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    /**
     * Devuelve la URL de portada a partir de una carpeta de tour:
     *   storage/app/public/tours/{tourId}/gallery
     */
    $coverFromFolder = function (?int $tourId): string {
        if (!$tourId) return asset('images/volcano.png');

        $folder = "tours/{$tourId}/gallery";
        if (!Storage::disk('public')->exists($folder)) {
            return asset('images/volcano.png');
        }

        $allowed = ['jpg','jpeg','png','webp'];
        $first = collect(Storage::disk('public')->files($folder))
            ->filter(function ($p) use ($allowed) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                return in_array($ext, $allowed, true);
            })
            ->sort(function ($a, $b) { return strnatcasecmp($a, $b); })
            ->first();

        return $first ? asset('storage/'.$first) : asset('images/volcano.png');
    };
@endphp

<h2 class="big-title text-center" style="color: var(--primary-dark);">
    {{ __('adminlte::adminlte.our_tours') }}
</h2>

<div class="tour-cards">
@foreach ($typeMeta as $slug => $meta)
    @php
        /** @var \Illuminate\Support\Collection $group */
        $group = $toursByType[$slug] ?? collect();
        if ($group->isEmpty()) continue;

        $first = $group->first();

        // Títulos/descr traducidos por tipo
        $translatedTitle       = $meta['title']       ?? '';
        $translatedDuration    = $meta['duration']    ?? '';
        $translatedDescription = $meta['description'] ?? '';

        // Portada para la tarjeta del tipo (primera del grupo)
        $firstCover = $coverFromFolder($first->tour_id ?? $first->id ?? null);
    @endphp

    {{-- Card del tipo (abre modal) --}}
    <div class="tour-card" style="cursor:pointer"
         data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug($slug) }}">
        <img src="{{ $firstCover }}"
             class="card-img-top" alt="{{ $first->getTranslatedName() }}">
        <div class="card-body d-flex flex-column h-100">
            <h5 class="card-title">{{ $translatedTitle }}</h5>

            @if(!empty($translatedDuration))
                <p class="card-text text-muted">{{ $translatedDuration }}</p>
            @endif

            @if(!empty($translatedDescription))
                <p class="card-text small">{!! nl2br(e($translatedDescription)) !!}</p>
            @endif

            <a href="javascript:void(0)" class="btn btn-success w-100 btn-ver-tour"
               data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug($slug) }}">
                {{ __('adminlte::adminlte.see_tours') }}
            </a>
        </div>
    </div>

    {{-- Modal por tipo --}}
    <div class="modal fade" id="modal-{{ Str::slug($slug) }}" tabindex="-1"
         aria-labelledby="modalLabel-{{ Str::slug($slug) }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#0f2419">
                    <h5 class="modal-title text-center w-100 text-white"
                        id="modalLabel-{{ Str::slug($slug) }}">
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
                                    // Por defecto: portada desde su propio ID
                                    $coverTourId = $tour->tour_id ?? $tour->id ?? null;

                                    // OVERRIDE para "Half Day": forzar carpetas 1, 2, 3
                                    // - Caminata -> 1
                                    // - Safari   -> 2
                                    // - Puentes  -> 3
                                    $slugNormalized = Str::slug($slug); // ej. "half-day"
                                    if (in_array($slugNormalized, ['half-day','half_day','medio-dia','medio-dia'])) {
                                        $name = Str::lower($tour->getTranslatedName());
                                        if (Str::contains($name, ['caminata'])) {
                                            $coverTourId = 1;
                                        } elseif (Str::contains($name, ['safari'])) {
                                            $coverTourId = 2;
                                        } elseif (Str::contains($name, ['puentes','colgantes'])) {
                                            $coverTourId = 3;
                                        }
                                    }

                                    $tourCover = $coverFromFolder($coverTourId);
                                @endphp

                                <div class="col-6 col-sm-6 col-md-6 col-xl-4 mb-4">
                                    <div class="tour-modal-card h-100">
                                        <img src="{{ $tourCover }}"
                                             class="card-img-top" alt="{{ $tour->getTranslatedName() }}">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title card-title-text">{{ $tour->getTranslatedName() }}</h5>

                                            @php
                                                $rawLength = $tour->length;
                                                $unit  = __('adminlte::adminlte.horas');
                                                $label = __('adminlte::adminlte.duration');
                                            @endphp
                                            @if(!empty($rawLength))
                                                <p class="text-muted small mb-2">
                                                    <strong>{{ $label }}:</strong> {{ $rawLength }} {{ $unit }}
                                                </p>
                                            @endif

                                            <div class="mb-3 small mt-auto">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ __('adminlte::adminlte.adult') }}</strong>
                                                        <small>({{ __('adminlte::adminlte.age_10_plus') }})</small>
                                                    </div>
                                                    <strong style="color:#006633">
                                                        ${{ number_format($tour->adult_price, 2) }}
                                                    </strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ __('adminlte::adminlte.kid') }}</strong>
                                                        <small>({{ __('adminlte::adminlte.age_4_to_9') }})</small>
                                                    </div>
                                                    <strong style="color:#006633">
                                                        ${{ number_format($tour->kid_price, 2) }}
                                                    </strong>
                                                </div>
                                            </div>

                                            <a href="{{ route('tours.show', $tour->tour_id) }}"
                                               class="btn btn-success w-100 mt-2">
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
                    <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('adminlte::adminlte.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
