@php
// Helper para traducciones
$tr = function(string $key, string $fallback) {
$t = __($key);
return ($t === $key) ? $fallback : $t;
};
@endphp

<div class="form-header">
  <h4 class="mb-2">{{ $tr('adminlte::adminlte.price', 'Precio') }}</h4>

  {{-- Contenedor dinámico de precios --}}
  <div id="priceBreakdownContainer" class="price-breakdown d-flex flex-wrap align-items-center gap-2 mb-2">
    {{-- Se llenará dinámicamente con JavaScript --}}
  </div>

  <div id="noPricesWarning" class="alert alert-warning small mb-2 d-none">
    {{ $tr('adminlte::adminlte.no_prices_available', 'No hay precios disponibles para este tour.') }}
  </div>
</div>

<style>
  .price-breakdown {
    line-height: 1.6;
    min-height: 30px;
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

    .price-breakdown .price-item {
      flex-basis: 100%;
    }
  }
</style>

@push('scripts')
<script>
  (function() {
    if (window.__gvPriceHeaderInit) return;
    window.__gvPriceHeaderInit = true;

    const priceContainer = document.getElementById('priceBreakdownContainer');
    const noPricesWarning = document.getElementById('noPricesWarning');
    const dateInput = document.getElementById('tourDateInput');

    if (!priceContainer || !dateInput) return;

    // Obtener datos de categorías del travelers
    const travelersContainer = document.querySelector('.gv-travelers');
    if (!travelersContainer) return;

    let allCategories = [];
    try {
      allCategories = JSON.parse(travelersContainer.getAttribute('data-categories') || '[]');
    } catch (_) {
      console.error('Failed to parse categories data');
      return;
    }

    // Función para obtener precio para una fecha
    function getPriceForDate(rules, dateStr) {
      if (!dateStr || !rules || !rules.length) return null;

      // Buscar regla específica
      const specificRule = rules.find(r => {
        if (r.is_default) return false;
        const from = r.valid_from;
        const until = r.valid_until;
        if (from && until) return dateStr >= from && dateStr <= until;
        if (from) return dateStr >= from;
        if (until) return dateStr <= until;
        return false;
      });
      if (specificRule) return specificRule;

      // Fallback a default
      return rules.find(r => r.is_default) || null;
    }

    // Función para actualizar el header de precios
    function updatePriceHeader(dateStr) {
      if (!dateStr) {
        // Sin fecha, usar hoy como referencia
        const today = new Date().toISOString().split('T')[0];
        dateStr = today;
      }

      const categoriesWithPrices = [];

      allCategories.forEach(cat => {
        const priceRule = getPriceForDate(cat.rules, dateStr);
        if (priceRule) {
          categoriesWithPrices.push({
            name: cat.name,
            price: priceRule.price,
            slug: cat.slug
          });
        }
      });

      // Limpiar container
      priceContainer.innerHTML = '';

      if (categoriesWithPrices.length === 0) {
        noPricesWarning.classList.remove('d-none');
        return;
      }

      noPricesWarning.classList.add('d-none');

      // Renderizar precios
      categoriesWithPrices.forEach((cat, index) => {
        const span = document.createElement('span');
        span.className = 'price-item d-inline-flex align-items-baseline gap-1';

        const strong = document.createElement('strong');
        strong.className = 'text-dark';
        strong.textContent = cat.name + ':';

        const priceSpan = document.createElement('span');
        // AQUÍ forzamos la clase roja para todos
        priceSpan.className = 'price-amount fw-bold text-danger';
        priceSpan.textContent = '$' + parseFloat(cat.price).toFixed(2);

        span.appendChild(strong);
        span.appendChild(priceSpan);

        if (index < categoriesWithPrices.length - 1) {
          const separator = document.createElement('span');
          separator.className = 'text-muted mx-1';
          separator.textContent = '|';
          span.appendChild(separator);
        }

        priceContainer.appendChild(span);
      });
    }

    // Inicializar con fecha de hoy
    updatePriceHeader(null);

    // Escuchar cambios de fecha
    dateInput.addEventListener('change', (e) => {
      updatePriceHeader(e.target.value);
    });

    // También escuchar cuando Flatpickr cambia la fecha
    if (dateInput._flatpickr) {
      dateInput._flatpickr.config.onChange.push((selectedDates, dateStr) => {
        updatePriceHeader(dateStr);
      });
    }
  })();
</script>
@endpush