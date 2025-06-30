
    <h2>{{ __('adminlte::adminlte.our_tours') }}</h2>
    <div class="tour-cards">
        @foreach($tours as $tour)
            <div class="card" style="width: 18rem;">
                <img src="{{ $tour->image_path ? asset('storage/' . $tour->image_path) : asset('images/volcano.png') }}" class="card-img-top" alt="{{ $tour->name }}">
                <div class="card-body">
                    <h5 class="card-title" style="background-color: #F92526; color: white; padding: 0.5em;">{{ $tour->name }}</h5>

                    <input type="checkbox" id="toggle-overview-{{ $tour->id }}" class="toggle-overview" />
                    <div class="overview-container text-muted">
                        <p class="mb-0">{{ $tour->overview }}</p>
                    </div>
                    <label for="toggle-overview-{{ $tour->id }}" class="toggle-label">{{ __('adminlte::adminlte.read_more') }}</label>
                    <br>
                    <div class="mb-3 small">
                        <div class="d-flex justify-content-between">
                            <span>{{ __('adminlte::adminlte.adult_price') }}</span>
                            <strong style="color: #006633;">${{ number_format($tour->adult_price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('adminlte::adminlte.kid_price') }}</span>
                            <strong style="color: #006633;">${{ number_format($tour->kid_price, 2) }}</strong>
                        </div>
                    </div>

                    <a href="#" class="btn btn-success w-100">{{ __('adminlte::adminlte.see_tour') }}</a>
                </div>
            </div>
        @endforeach
    </div>
