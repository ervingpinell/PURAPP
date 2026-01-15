{{-- Cart button - Available for all users (guests + registered) --}}
@if(config('site.public_readonly') && !auth()->check())
<button type="button" class="btn btn-secondary w-100 mt-3" disabled>
  <i class="fas fa-lock me-2"></i> {{ __('Maintenance Mode') }}
</button>
@else
<button id="addToCartBtn" type="submit" class="btn btn-success gv-cta w-100 mt-3">
  <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
</button>
@endif