{{-- Cart button - Available for all users (guests + registered) --}}
@php
$showMaintenance = config('site.public_readonly') && !auth()->check();
$guestBlocked = !auth()->check() && !config('site.allow_guest_checkout', true);
@endphp

@if($showMaintenance)
<button type="button" class="btn btn-secondary w-100 mt-3" disabled>
  <i class="fas fa-lock me-2"></i> {{ __('Maintenance Mode') }}
</button>
@elseif($guestBlocked)
<a href="{{ route('login') }}" class="btn btn-primary w-100 mt-3">
  <i class="fas fa-sign-in-alt me-2"></i> {{ __('adminlte::adminlte.login_to_book') }}
</a>
@else
<button id="addToCartBtn" type="submit" class="btn btn-success gv-cta w-100 mt-3">
  <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
</button>
@endif