@auth
  <button id="addToCartBtn" type="button" class="btn btn-success gv-cta w-100 mt-3">
    <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
  </button>
@else
  <a href="{{ route('login') }}" class="btn btn-success gv-cta w-100 mt-3"
     onclick="return askLoginWithSwal(event, this.href);">
    <i class="fas fa-cart-plus me-2"></i> {{ __('adminlte::adminlte.add_to_cart') }}
  </a>
@endauth
