<style>

</style>
    
    <h2>{{ __('adminlte::adminlte.our_tours') }}</h2>
    <div class="tour-cards">
        @foreach($tours as $tour)
            <div class="card" style="width: 18rem;">
                <img src="{{ $tour->image_path ? asset('storage/' . $tour->image_path) : asset('images/volcano.png') }}" class="card-img-top" alt="{{ $tour->name }}">
                <div class="card-body">
                    <h5 class="card-title" style="background-color: #F92526; color: white; padding: 0.5em;">{{ $tour->name }}</h5>
<div class="overview-container collapsed" id="overview-{{ $tour->tour_id }}">
  <p>{{ $tour->overview }}</p>
</div>
<a href="javascript:void(0);"
   class="toggle-overview-link"
   data-target="overview-{{ $tour->tour_id }}">
   {{ __('adminlte::adminlte.read_more') }}
</a>
                    
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

               <a href="{{ route('tours.show', $tour->tour_id) }}" class="btn btn-success w-100">
    {{ __('adminlte::adminlte.see_tour') }}
</a>
                </div>
            </div>
        @endforeach
    </div>
