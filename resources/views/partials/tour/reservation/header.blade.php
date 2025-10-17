<div class="form-header">
  @guest
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
      <i class="fas fa-lock me-2"></i>
      <div class="flex-grow-1">
        <strong>{{ __('adminlte::adminlte.auth_required_title') ?? 'Debes iniciar sesión para reservar' }}</strong>
        <div class="small">
          {{ __('adminlte::adminlte.auth_required_body') ?? 'Inicia sesión o regístrate para completar tu compra. Los campos se desbloquean al iniciar sesión.' }}
        </div>
      </div>
      <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-success ms-auto">
        {{ __('adminlte::adminlte.login_now') }}
      </a>
    </div>
  @endguest

  <h4 class="mb-2">{{ __('adminlte::adminlte.price') }}</h4>

  <div class="price-breakdown mb-1">
    <span class="fw-bold">{{ __('adminlte::adminlte.adult') }}:</span>
    <span class="price-adult fw-bold text-danger">${{ number_format($tour->adult_price, 2) }}</span> |
    <span class="fw-bold">{{ __('adminlte::adminlte.kid') }}:</span>
    <span class="price-kid fw-bold text-danger">${{ number_format($tour->kid_price, 2) }}</span>
  </div>
</div>
