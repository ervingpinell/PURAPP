@php
  // Obtener categorías activas con precios y límites
  $activeCategories = $tour->prices()
      ->where('is_active', true)
      ->whereHas('category', fn($q) => $q->where('is_active', true))
      ->with('category')
      ->orderBy('category_id')
      ->get();

  // Límites globales
  $maxPersonsGlobal = (int) config('booking.max_persons_per_booking', 12);
  $minAdultsGlobal = (int) config('booking.min_adults_per_booking', 2);
  $maxKidsGlobal = (int) config('booking.max_kids_per_booking', 2);

  // Construir data attributes para JS
  $categoriesData = $activeCategories->map(function($priceRecord) use ($minAdultsGlobal, $maxKidsGlobal, $maxPersonsGlobal) {
      $category = $priceRecord->category;
      $categorySlug = $category->slug ?? strtolower($category->name ?? '');

      // Aplicar límites globales para adultos y niños si aplica
      $min = (int) $priceRecord->min_quantity;
      $max = (int) $priceRecord->max_quantity;

      if (in_array($categorySlug, ['adult', 'adulto', 'adults'])) {
          $min = max($min, $minAdultsGlobal);
      } elseif (in_array($categorySlug, ['kid', 'nino', 'child', 'kids', 'children'])) {
          $max = min($max, $maxKidsGlobal);
      }

      // No permitir que el max de ninguna categoría exceda el límite global
      $max = min($max, $maxPersonsGlobal);

      // Definir valor inicial
      $initial = in_array($categorySlug, ['adult', 'adulto', 'adults']) ? max($min, 2) : 0;

      return [
          'id' => (int) $priceRecord->category_id,
          'name' => $category->name ?? 'N/A',
          'slug' => $categorySlug,
          'price' => (float) $priceRecord->price,
          'min' => $min,
          'max' => $max,
          'initial' => $initial,
          'age_range' => $category->age_min || $category->age_max
              ? ($category->age_min && $category->age_max
                  ? "{$category->age_min}-{$category->age_max}"
                  : ($category->age_min ? "{$category->age_min}+" : "hasta {$category->age_max}"))
              : null,
      ];
  })->values()->toArray();
@endphp

{{-- Travelers inline + total --}}
<div class="mb-3 gv-travelers"
     data-categories='@json($categoriesData)'
     data-max-total="{{ $maxPersonsGlobal }}">

  @if($activeCategories->isNotEmpty())
    <div class="gv-trav-rows mt-2">
      @foreach($categoriesData as $cat)
        @php
          $catId = $cat['id'];
          $catName = $cat['name'];
          $catSlug = $cat['slug'];
          $catMin = $cat['min'];
          $catMax = $cat['max'];
          $catInitial = $cat['initial'];
          $catAgeRange = $cat['age_range'] ?? null;

          // Ícono según slug
          $icon = match(true) {
              in_array($catSlug, ['adult', 'adulto', 'adults']) => 'fa-male',
              in_array($catSlug, ['kid', 'child', 'nino', 'kids', 'children']) => 'fa-child',
              $catSlug === 'senior' => 'fa-user-tie',
              $catSlug === 'student' => 'fa-user-graduate',
              in_array($catSlug, ['infant', 'infante', 'baby']) => 'fa-baby',
              default => 'fa-user',
          };
        @endphp

        <div class="gv-trav-row d-flex align-items-center justify-content-between py-2 border rounded px-2 mb-2"
             data-category-id="{{ $catId }}"
             data-category-slug="{{ $catSlug }}">
          <div class="d-flex align-items-center gap-2">
            <i class="fas {{ $icon }}" aria-hidden="true"></i>
            <div class="d-flex flex-column">
              <span class="fw-semibold">{{ $catName }}</span>
              @if($catAgeRange)
                <small class="text-muted">({{ $catAgeRange }} años)</small>
              @endif
              @if($catMin > 0)
                <small class="text-muted">Min: {{ $catMin }}</small>
              @endif
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-outline-secondary btn-sm category-minus-btn"
                    data-category-id="{{ $catId }}"
                    aria-label="{{ __('adminlte::adminlte.decrease') }} {{ $catName }}">−</button>

            <input class="form-control form-control-sm text-center category-input"
                   type="number"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   data-category-id="{{ $catId }}"
                   data-category-slug="{{ $catSlug }}"
                   min="{{ $catMin }}"
                   max="{{ $catMax }}"
                   step="1"
                   value="{{ $catInitial }}"
                   style="width: 60px;"
                   aria-label="{{ __('adminlte::adminlte.quantity') }} {{ $catName }}">

            <button type="button"
                    class="btn btn-outline-secondary btn-sm category-plus-btn"
                    data-category-id="{{ $catId }}"
                    aria-label="{{ __('adminlte::adminlte.increase') }} {{ $catName }}">+</button>
          </div>
        </div>

        {{-- Hidden input para enviar al servidor --}}
        <input type="hidden"
               name="categories[{{ $catId }}]"
               id="category_quantity_{{ $catId }}"
               value="{{ $catInitial }}">
      @endforeach
    </div>

    {{-- Total --}}
    <div class="gv-total-inline mt-3 p-2 bg-light border rounded">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-bold">{{ __('adminlte::adminlte.total') }}:</span>
        <strong id="reservation-total-price-inline" class="text-success fs-5">$0.00</strong>
      </div>
      <div class="d-flex justify-content-between text-muted small">
        <span>{{ __('adminlte::adminlte.total_persons') ?? 'Total personas' }}:</span>
        <span id="reservation-total-pax">0</span>
      </div>
    </div>
  @else
    <div class="alert alert-danger">
      {{ __('adminlte::adminlte.no_prices_configured') ?? 'Este tour no tiene precios configurados.' }}
    </div>
  @endif
</div>

@push('scripts')
<script>
(function() {
  // Prevenir doble inicialización
  if (window.__gvTravelersInit) {
    console.log('Travelers already initialized');
    return;
  }
  window.__gvTravelersInit = true;

  console.log('🚀 Initializing travelers...');

  const container = document.querySelector('.gv-travelers');
  if (!container) {
    console.error('❌ Container .gv-travelers not found');
    return;
  }

  const categoriesJson = container.getAttribute('data-categories');
  console.log('📦 Raw categories JSON:', categoriesJson);

  const maxTotal = parseInt(container.getAttribute('data-max-total') || '12');

  let categories = [];
  try {
    categories = JSON.parse(categoriesJson || '[]');
    console.log('✅ Categories parsed successfully:', categories);
  } catch(e) {
    console.error('❌ Error parsing categories:', e);
    console.error('JSON string was:', categoriesJson);
    return;
  }

  if (!categories.length) {
    console.error('❌ No categories found');
    return;
  }

  // Función para calcular total
  function updateTotals() {
    let totalPrice = 0;
    let totalPax = 0;

    categories.forEach(cat => {
      const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
      if (!input) {
        console.warn(`⚠️ Input not found for category ${cat.id}`);
        return;
      }

      const qty = parseInt(input.value || '0');
      totalPrice += cat.price * qty;
      totalPax += qty;
    });

    const priceEl = document.getElementById('reservation-total-price-inline');
    const paxEl = document.getElementById('reservation-total-pax');

    if (priceEl) priceEl.textContent = '$' + totalPrice.toFixed(2);
    if (paxEl) paxEl.textContent = totalPax;

    // Actualizar hidden inputs
    categories.forEach(cat => {
      const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
      const hidden = document.getElementById(`category_quantity_${cat.id}`);
      if (input && hidden) {
        hidden.value = input.value;
      }
    });

    // Validar máximo total y ajustar límites dinámicamente
    const allInputs = document.querySelectorAll('.category-input');
    allInputs.forEach(inp => {
      const currentQty = parseInt(inp.value || '0');
      const catId = parseInt(inp.getAttribute('data-category-id'));
      const cat = categories.find(c => c.id === catId);
      if (!cat) return;

      const otherTotal = totalPax - currentQty;
      const maxAllowed = Math.min(cat.max, maxTotal - otherTotal);

      inp.setAttribute('max', maxAllowed);
    });

    console.log(`💰 Total: $${totalPrice.toFixed(2)}, 👥 Pax: ${totalPax}`);
    return totalPax;
  }

  // Event listeners para botones MINUS
  const minusButtons = document.querySelectorAll('.category-minus-btn');
  console.log(`🔘 Found ${minusButtons.length} minus buttons`);

  minusButtons.forEach((btn, index) => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      console.log(`➖ Minus button ${index} clicked`);

      const catId = parseInt(this.getAttribute('data-category-id'));
      console.log(`   Category ID: ${catId}`);

      const input = document.querySelector(`.category-input[data-category-id="${catId}"]`);
      if (!input) {
        console.error(`   ❌ Input not found for category ${catId}`);
        return;
      }

      const min = parseInt(input.getAttribute('min') || '0');
      const current = parseInt(input.value || '0');

      console.log(`   Current: ${current}, Min: ${min}`);

      if (current > min) {
        input.value = current - 1;
        console.log(`   ✅ New value: ${input.value}`);
        updateTotals();
      } else {
        console.log(`   ⚠️ Already at minimum`);
      }
    });
  });

  // Event listeners para botones PLUS
  const plusButtons = document.querySelectorAll('.category-plus-btn');
  console.log(`🔘 Found ${plusButtons.length} plus buttons`);

  plusButtons.forEach((btn, index) => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      console.log(`➕ Plus button ${index} clicked`);

      const catId = parseInt(this.getAttribute('data-category-id'));
      console.log(`   Category ID: ${catId}`);

      const input = document.querySelector(`.category-input[data-category-id="${catId}"]`);
      if (!input) {
        console.error(`   ❌ Input not found for category ${catId}`);
        return;
      }

      const current = parseInt(input.value || '0');
      const max = parseInt(input.getAttribute('max') || '12');

      // Calcular total actual
      const totalPax = categories.reduce((sum, cat) => {
        const inp = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
        return sum + parseInt(inp?.value || '0');
      }, 0);

      console.log(`   Current: ${current}, Max: ${max}, Total Pax: ${totalPax}`);

      if (current < max && totalPax < maxTotal) {
        input.value = current + 1;
        console.log(`   ✅ New value: ${input.value}`);
        updateTotals();
      } else if (totalPax >= maxTotal) {
        const msg = @json(__('adminlte::adminlte.max_persons_reached', ['max' => ':max'])).replace(':max', maxTotal);
        alert(msg);
        console.log(`   ⚠️ Max total reached`);
      } else {
        alert('Máximo ' + max + ' para esta categoría');
        console.log(`   ⚠️ Max category reached`);
      }
    });
  });

  // Permitir edición manual del input
  document.querySelectorAll('.category-input').forEach(input => {
    input.addEventListener('change', function() {
      const min = parseInt(this.getAttribute('min') || '0');
      const max = parseInt(this.getAttribute('max') || '12');
      let value = parseInt(this.value || '0');

      console.log(`📝 Input changed: ${value}, Min: ${min}, Max: ${max}`);

      // Validar rango
      if (value < min) value = min;
      if (value > max) value = max;

      this.value = value;
      updateTotals();
    });
  });

  // Inicializar totales
  console.log('🎬 Initializing totals...');
  updateTotals();

  console.log('✅ Travelers initialization complete');
})();
</script>
@endpush
