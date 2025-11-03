@php
  // Obtener categorías activas con precios
  $activeCategories = $tour->prices()
      ->where('is_active', true)
      ->whereHas('category', fn($q) => $q->where('is_active', true))
      ->with('category')
      ->orderBy('category_id')
      ->get();
@endphp

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

  @if($activeCategories->isNotEmpty())
    <div class="price-breakdown d-flex flex-wrap align-items-center gap-2 mb-2">
      @foreach($activeCategories as $index => $priceRecord)
        @php
          $category = $priceRecord->category;
          $categoryName = $category->name ?? 'N/A';
          $categorySlug = $category->slug ?? strtolower($categoryName);
          $price = $priceRecord->price;

          // Construir rango de edad si existe
          $ageRange = '';
          if ($category->age_min || $category->age_max) {
              if ($category->age_min && $category->age_max) {
                  $ageRange = " ({$category->age_min}-{$category->age_max} años)";
              } elseif ($category->age_min) {
                  $ageRange = " ({$category->age_min}+ años)";
              } elseif ($category->age_max) {
                  $ageRange = " (hasta {$category->age_max} años)";
              }
          }

          // Agregar separador después de cada categoría excepto la última
          $showSeparator = $index < $activeCategories->count() - 1;
        @endphp

        <span class="price-item d-inline-flex align-items-baseline gap-1">
          <strong class="text-dark">{{ $categoryName }}{{ $ageRange }}:</strong>
          <span class="price-{{ $categorySlug }} fw-bold text-danger">${{ number_format($price, 2) }}</span>
          @if($showSeparator)
            <span class="text-muted mx-1">|</span>
          @endif
        </span>
      @endforeach
    </div>
  @else
    <div class="alert alert-warning small mb-2">
      {{ __('adminlte::adminlte.no_prices_available') ?? 'No hay precios disponibles para este tour.' }}
    </div>
  @endif
</div>

<style>
  .price-breakdown {
    line-height: 1.6;
  }

  .price-item {
    white-space: nowrap;
  }

  .price-item strong {
    font-size: 0.95rem;
  }

  @media (max-width: 576px) {
    .price-breakdown {
      font-size: 0.9rem;
    }
  }
</style>
