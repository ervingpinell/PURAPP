@php use Illuminate\Support\Str; @endphp

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
            $translatedTitle       = $meta['title'];       // p.ej. "Full Day" traducido
            $translatedDuration    = $meta['duration'];    // p.ej. "6 – 9 horas" traducido
            $translatedDescription = $meta['description']; // texto traducido del tipo
        @endphp

        {{-- Card fuera del modal --}}
        <div class="tour-card" style="cursor: pointer;" data-bs-toggle="modal"
             data-bs-target="#modal-{{ Str::slug($slug) }}">
            <img src="{{ $first->image_path ? asset('storage/' . $first->image_path) : asset('images/volcano.png') }}"
                 class="card-img-top" alt="{{ $first->translated_name }}">
            <div class="card-body d-flex flex-column h-100">
                <h5 class="card-title">{{ $translatedTitle }}</h5>
                @if(!empty($translatedDuration))
                    <p class="card-text text-muted">{{ $translatedDuration }}</p>
                @endif
                @if(!empty($translatedDescription))
                    <p class="card-text small card-text">{!! nl2br(e($translatedDescription)) !!}</p>
                @endif
                <a href="javascript:void(0)" class="btn btn-success w-100 btn-ver-tour"
                   data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug($slug) }}">
                    {{ __('adminlte::adminlte.see_tours') }}
                </a>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="modal-{{ Str::slug($slug) }}" tabindex="-1"
             aria-labelledby="modalLabel-{{ Str::slug($slug) }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background:#0f2419">
                        <h5 class="modal-title text-center w-100 text-white" id="modalLabel-{{ Str::slug($slug) }}">
                            {{ $translatedTitle }}
                        </h5>
                        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                @foreach ($group as $tour)
                                    <div class="col-6 col-sm-6 col-md-6 col-xl-4 mb-4">
                                        <div class="tour-modal-card h-100">
                                            <img src="{{ $tour->image_path ? asset('storage/' . $tour->image_path) : asset('images/volcano.png') }}"
                                                 class="card-img-top" alt="{{ $tour->translated_name }}">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title card-title-text">{{ $tour->translated_name }}</h5>

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
                                                        <strong style="color: #006633;">${{ number_format($tour->adult_price, 2) }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ __('adminlte::adminlte.kid') }}</strong>
                                                            <small>({{ __('adminlte::adminlte.age_4_to_9') }})</small>
                                                        </div>
                                                        <strong style="color: #006633;">${{ number_format($tour->kid_price, 2) }}</strong>
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
