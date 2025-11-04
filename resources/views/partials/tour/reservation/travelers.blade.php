@php
  // ===== CATEGORÍAS ACTIVAS (precios y límites) =====
  $activeCategories = $tour->prices()
      ->where('is_active', true)
      ->whereHas('category', fn($q) => $q->where('is_active', true))
      ->with('category')
      ->orderBy('category_id')
      ->get();

  // Límites globales (del config/booking.php)
  $maxPersonsGlobal = (int) config('booking.max_persons_per_booking', 12);
  $minAdultsGlobal  = (int) config('booking.min_adults_per_booking', 2);
  $maxKidsGlobal    = (int) config('booking.max_kids_per_booking', 2);

  // Estructura para JS con textos YA traducidos
  $categoriesData = $activeCategories->map(function($priceRecord) use ($minAdultsGlobal, $maxKidsGlobal, $maxPersonsGlobal) {
      $category = $priceRecord->category;
      $slug = $category->slug ?? strtolower($category->name ?? '');

      $min = (int) $priceRecord->min_quantity;
      $max = (int) $priceRecord->max_quantity;

      // Reglas globales por slug
      if (in_array($slug, ['adult','adulto','adults'])) {
        $min = max($min, $minAdultsGlobal);
      } elseif (in_array($slug, ['kid','nino','child','kids','children'])) {
        $max = min($max, $maxKidsGlobal);
      }

      // Ninguna categoría debe superar el global
      $max = min($max, $maxPersonsGlobal);

      // Valor inicial
      $initial = in_array($slug, ['adult','adulto','adults']) ? max($min, 2) : 0;

      // Texto de rango de edad (traducido) desde m_bookings.travelers.*
      $ageMin = $category->age_min;
      $ageMax = $category->age_max;
      $ageRangeText = null;
      if ($ageMin && $ageMax) {
          $ageRangeText = __('m_bookings.travelers.age_between', ['min' => $ageMin, 'max' => $ageMax]);
      } elseif ($ageMin) {
          $ageRangeText = __('m_bookings.travelers.age_from', ['min' => $ageMin]);
      } elseif ($ageMax) {
          $ageRangeText = __('m_bookings.travelers.age_to', ['max' => $ageMax]);
      }

      return [
        'id'       => (int) $priceRecord->category_id,
        'name'     => $category->name ?? 'N/A',
        'slug'     => $slug,
        'price'    => (float) $priceRecord->price,
        'min'      => $min,
        'max'      => $max,
        'initial'  => $initial,
        'age_text' => $ageRangeText, // <-- ya traducido
      ];
  })->values()->toArray();

  // Paquete i18n para el JS (títulos y plantillas de mensajes)
  // Asegúrate de tener estas claves en resources/lang/{locale}/m_bookings.php -> travelers.*
  $travI18n = [
    'title_warning'        => __('m_bookings.travelers.title_warning'),        // p.ej. "Atención"
    'title_info'           => __('m_bookings.travelers.title_info'),           // p.ej. "Información"
    'title_error'          => __('m_bookings.travelers.title_error'),          // p.ej. "Error"
    'max_persons_reached'  => __('m_bookings.travelers.max_persons_reached'),  // "Máximo :max personas por reserva"
    'max_category_reached' => __('m_bookings.travelers.max_category_reached'), // "Máximo :max para esta categoría"
    'invalid_quantity'     => __('m_bookings.travelers.invalid_quantity'),     // "Cantidad inválida. Ingresa un número válido."
  ];
@endphp

{{-- ===== VIAJEROS: CANTIDADES Y TOTAL ===== --}}
<div class="mb-3 gv-travelers"
     data-categories='@json($categoriesData)'
     data-max-total="{{ $maxPersonsGlobal }}"
     data-i18n='@json($travI18n)'>

  @if($activeCategories->isNotEmpty())
    <div class="gv-trav-rows mt-2">
      @foreach($categoriesData as $cat)
        @php
          $catId       = $cat['id'];
          $catName     = $cat['name'];
          $catSlug     = $cat['slug'];
          $catMin      = $cat['min'];
          $catMax      = $cat['max'];
          $catInitial  = $cat['initial'];
          $catAgeText  = $cat['age_text'] ?? null;

          $icon = match(true) {
              in_array($catSlug, ['adult','adulto','adults'])              => 'fa-male',
              in_array($catSlug, ['kid','child','nino','kids','children']) => 'fa-child',
              $catSlug === 'senior'   => 'fa-user-tie',
              $catSlug === 'student'  => 'fa-user-graduate',
              in_array($catSlug, ['infant','infante','baby'])              => 'fa-baby',
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
              @if($catAgeText)
                <small class="text-muted">({{ $catAgeText }})</small>
              @endif
              @if($catMin > 0)
                <small class="text-muted">{{ __('adminlte::adminlte.min') }}: {{ $catMin }}</small>
              @endif
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-outline-secondary btn-sm category-minus-btn"
                    data-category-id="{{ $catId }}"
                    aria-label="{{ __('adminlte::adminlte.decrease') }} {{ $catName }}">−</button>

            <input class="form-control form-control-sm text-center category-input"
                   type="number" inputmode="numeric" pattern="[0-9]*"
                   data-category-id="{{ $catId }}" data-category-slug="{{ $catSlug }}"
                   min="{{ $catMin }}" max="{{ $catMax }}" step="1"
                   value="{{ $catInitial }}" style="width: 60px;"
                   aria-label="{{ __('adminlte::adminlte.quantity') }} {{ $catName }}">

            <button type="button"
                    class="btn btn-outline-secondary btn-sm category-plus-btn"
                    data-category-id="{{ $catId }}"
                    aria-label="{{ __('adminlte::adminlte.increase') }} {{ $catName }}">+</button>
          </div>
        </div>

        {{-- hidden para request --}}
        <input type="hidden" name="categories[{{ $catId }}]"
               id="category_quantity_{{ $catId }}" value="{{ $catInitial }}">
      @endforeach
    </div>

    {{-- Total --}}
    <div class="gv-total-inline mt-3 p-2 bg-light border rounded">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="fw-bold">{{ __('adminlte::adminlte.total') }}:</span>
        <strong id="reservation-total-price-inline" class="text-success fs-5">$0.00</strong>
      </div>
      <div class="d-flex justify-content-between text-muted small">
        <span>{{ __('adminlte::adminlte.total_persons') }}:</span>
        <span id="reservation-total-pax">0</span>
      </div>
    </div>
  @else
    <div class="alert alert-danger">
      {{ __('adminlte::adminlte.no_prices_configured') }}
    </div>
  @endif
</div>

@once
  {{-- SweetAlert2 para los avisos --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

@push('scripts')
<script>
(function() {
  if (window.__gvTravelersInit) return;
  window.__gvTravelersInit = true;

  // ===== i18n =====
  const container = document.querySelector('.gv-travelers');
  if (!container) return;

  let i18n = {};
  try { i18n = JSON.parse(container.getAttribute('data-i18n') || '{}'); } catch(_) { i18n = {}; }

  // Helper para avisos (usa títulos traducidos si existen)
  function gvAlert(message, type = 'warning') {
    if (!window.Swal) { alert(message); return; }
    const title =
      type === 'error' ? (i18n.title_error || 'Error') :
      type === 'info'  ? (i18n.title_info  || 'Info')  :
                         (i18n.title_warning || 'Attention');
    Swal.fire({ icon: type, title, text: message, confirmButtonColor: '#198754' });
  }

  const maxTotal = parseInt(container.getAttribute('data-max-total') || '12', 10);
  let categories = [];
  try { categories = JSON.parse(container.getAttribute('data-categories') || '[]'); }
  catch (_) { categories = []; }

  if (!Array.isArray(categories) || !categories.length) return;

  function updateTotals() {
    let totalPax = 0, totalPrice = 0;

    categories.forEach(cat => {
      const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
      if (!input) return;
      const qty = parseInt(input.value || '0', 10);
      totalPax  += qty;
      totalPrice += (cat.price || 0) * qty;
    });

    const priceEl = document.getElementById('reservation-total-price-inline');
    const paxEl   = document.getElementById('reservation-total-pax');
    if (priceEl) priceEl.textContent = '$' + totalPrice.toFixed(2);
    if (paxEl)   paxEl.textContent   = totalPax;

    // Sincronizar inputs ocultos y recalcular máximos dinámicos
    categories.forEach(cat => {
      const input  = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
      const hidden = document.getElementById(`category_quantity_${cat.id}`);
      if (input && hidden) hidden.value = input.value;

      if (input) {
        const current = parseInt(input.value || '0', 10);
        const otherTotal = totalPax - current;
        const maxAllowed = Math.min(cat.max, Math.max(0, maxTotal - otherTotal));
        input.setAttribute('max', maxAllowed);
      }
    });

    return totalPax;
  }

  // Minus
  document.querySelectorAll('.category-minus-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault(); e.stopPropagation();
      const id  = parseInt(btn.getAttribute('data-category-id'), 10);
      const inp = document.querySelector(`.category-input[data-category-id="${id}"]`);
      if (!inp) return;
      const min = parseInt(inp.getAttribute('min') || '0', 10);
      const cur = parseInt(inp.value || '0', 10);
      if (cur > min) { inp.value = cur - 1; updateTotals(); }
    });
  });

  // Plus
  document.querySelectorAll('.category-plus-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault(); e.stopPropagation();
      const id  = parseInt(btn.getAttribute('data-category-id'), 10);
      const inp = document.querySelector(`.category-input[data-category-id="${id}"]`);
      if (!inp) return;

      const cur = parseInt(inp.value || '0', 10);
      const max = parseInt(inp.getAttribute('max') || '12', 10);

      // total actual antes de sumar
      const totalPax = categories.reduce((sum, cat) => {
        const i = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
        return sum + parseInt(i?.value || '0', 10);
      }, 0);

      if (cur < max && totalPax < maxTotal) {
        inp.value = cur + 1;
        updateTotals();
      } else if (totalPax >= maxTotal) {
        const tpl = i18n.max_persons_reached || 'Maximum people reached (:max).';
        gvAlert(tpl.replace(':max', String(maxTotal)), 'warning');
      } else {
        const tpl = i18n.max_category_reached || 'The maximum for this category is :max.';
        gvAlert(tpl.replace(':max', String(max)), 'info');
      }
    });
  });

  // Edición manual
  document.querySelectorAll('.category-input').forEach(inp => {
    inp.addEventListener('change', () => {
      const min = parseInt(inp.getAttribute('min') || '0', 10);
      const max = parseInt(inp.getAttribute('max') || '12', 10);
      let val   = parseInt(inp.value || '0', 10);
      if (Number.isNaN(val)) {
        const msg = i18n.invalid_quantity || 'Invalid quantity. Please enter a valid number.';
        gvAlert(msg, 'warning');
        val = min;
      }
      if (val < min) val = min;
      if (val > max) val = max;
      inp.value = val;
      const total = updateTotals();
      if (total > maxTotal) {
        inp.value = Math.max(min, val - (total - maxTotal));
        updateTotals();
        const tpl = i18n.max_persons_reached || 'Maximum people reached (:max).';
        gvAlert(tpl.replace(':max', String(maxTotal)), 'warning');
      }
    });
  });

  // Inicial
  updateTotals();
})();
</script>
@endpush
