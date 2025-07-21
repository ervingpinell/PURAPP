
<h2>{{ __('adminlte::adminlte.our_tours') }}</h2>

<div class="tour-cards">
    @foreach (['Full Day' => '6 - 9 Horas', 'Half Day' => '2 - 4 Horas'] as $type => $duration)
        @php
            $group = $tours[$type] ?? collect();
            if ($group->isEmpty()) continue;
            $first = $group->first();
        @endphp

        <div class="card" style="width: 18rem; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug($type) }}">
            <img src="{{ $first->image_path ? asset('storage/' . $first->image_path) : asset('images/volcano.png') }}"
                 class="card-img-top" alt="{{ $first->name }}">
            <div class="card-body">
                <h5 class="card-title">{{ $type }} Tours</h5>
@php
    $translatedDuration = str_replace('Horas', __('adminlte::adminlte.horas'), $duration);
@endphp

<p class="card-text text-muted">{{ $translatedDuration }}</p>

                <a href="javascript:void(0)" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modal-{{ Str::slug($type) }}">
                    {{ __('adminlte::adminlte.see_tour') }}
                </a>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="modal-{{ Str::slug($type) }}" tabindex="-1" aria-labelledby="modalLabel-{{ Str::slug($type) }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background:#0f2419">
                        <h5 class="modal-title  text-center w-100" id="modalLabel-{{ Str::slug($type) }}">{{ $type }} Tours</h5>
                        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            @foreach ($group as $tour)
                <div class="col-6 col-sm-6 col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ $tour->image_path ? asset('storage/' . $tour->image_path) : asset('images/volcano.png') }}"
                             class="card-img-top" alt="{{ $tour->name }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $tour->name }}</h5>

                            <div class="mb-3 small mt-auto">
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('adminlte::adminlte.adult_price') }}</span>
                                    <strong style="color: #006633;">${{ number_format($tour->adult_price, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('adminlte::adminlte.kid_price') }}</span>
                                    <strong style="color: #006633;">${{ number_format($tour->kid_price, 2) }}</strong>
                                </div>
                            </div>

                            <a href="{{ route('tours.show', $tour->tour_id) }}" class="btn btn-success w-100 mt-2">
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
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('adminlte::adminlte.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
