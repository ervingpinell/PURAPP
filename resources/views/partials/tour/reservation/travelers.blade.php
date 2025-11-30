{{-- ===== VIAJEROS: CANTIDADES Y TOTAL ===== --}}

@php
$trans = function(string $key, string $fallback) {
$t = __($key);
return ($t === $key) ? $fallback : $t;
};
@endphp

<div class="mb-3 gv-travelers"
    data-categories='@json($categoriesData)'
    data-max-total="{{ $maxPersonsGlobal }}"
    data-i18n='@json($travI18n)'>

    {{-- Contenedor de categorías (vacío hasta seleccionar fecha) --}}
    <div class="gv-trav-rows mt-2" id="categoriesContainer">
        {{-- Las categorías se insertarán aquí dinámicamente SOLO después de seleccionar fecha --}}
    </div>

    {{-- Total (oculto hasta que haya categorías) --}}
    <div class="gv-total-inline mt-3 p-2 bg-light border rounded d-none" id="totalSection">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-bold">{{ __('adminlte::adminlte.total') }}:</span>
            <strong id="reservation-total-price-inline" class="text-success fs-5">$0.00</strong>
        </div>
        <div class="d-flex justify-content-between text-muted small">
            <span>{{ __('adminlte::adminlte.total_persons') }}:</span>
            <span id="reservation-total-pax">0</span>
        </div>
    </div>
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

@push('scripts')
<script>
    (function() {
        if (window.__gvTravelersInit) return;
        window.__gvTravelersInit = true;

        const container = document.querySelector('.gv-travelers');
        if (!container) return;

        // ===== i18n =====
        let i18n = {};
        try {
            i18n = JSON.parse(container.getAttribute('data-i18n') || '{}');
        } catch (_) {
            i18n = {};
        }

        // Helper para avisos
        function gvAlert(message, type = 'warning') {
            if (!window.Swal) {
                alert(message);
                return;
            }
            const title =
                type === 'error' ? (i18n.title_error || 'Error') :
                type === 'info' ? (i18n.title_info || 'Info') :
                (i18n.title_warning || 'Atención');
            Swal.fire({
                icon: type,
                title,
                text: message,
                confirmButtonColor: '#198754'
            });
        }

        const maxTotal = parseInt(container.getAttribute('data-max-total') || '12', 10);
        let allCategories = [];
        try {
            allCategories = JSON.parse(container.getAttribute('data-categories') || '[]');
        } catch (_) {
            allCategories = [];
        }

        if (!Array.isArray(allCategories) || !allCategories.length) {
            console.warn('No categories data provided');
            return;
        }

        // Variables globales
        let activeCategories = [];
        const dateInput = document.getElementById('tourDateInput');
        const categoriesContainer = document.getElementById('categoriesContainer');
        const totalSection = document.getElementById('totalSection');

        // ===== TEMPLATE DE CATEGORÍA =====
        function createCategoryRow(cat) {
            const icon = getCategoryIcon(cat.slug);

            const row = document.createElement('div');
            row.className = 'gv-trav-row d-flex align-items-center justify-content-between py-2 border rounded px-2 mb-2';
            row.setAttribute('data-category-id', cat.id);
            row.setAttribute('data-category-slug', cat.slug);

            row.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="fas ${icon}" aria-hidden="true"></i>
                <div class="d-flex flex-column">
                    <span class="fw-semibold">${cat.name}</span>
                    ${cat.age_text ? `<small class="text-muted">(${cat.age_text})</small>` : ''}
                    <small class="text-muted price-display">$${cat.price.toFixed(2)}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button"
                    class="btn btn-outline-secondary btn-sm category-minus-btn"
                    data-category-id="${cat.id}"
                    aria-label="Disminuir ${cat.name}">−</button>

                <input class="form-control form-control-sm text-center category-input"
                    type="number" inputmode="numeric" pattern="[0-9]*"
                    data-category-id="${cat.id}"
                    data-category-slug="${cat.slug}"
                    min="${cat.min}"
                    max="${cat.max}"
                    step="1"
                    value="${cat.initial}"
                    style="width: 60px;"
                    aria-label="Cantidad ${cat.name}">

                <button type="button"
                    class="btn btn-outline-secondary btn-sm category-plus-btn"
                    data-category-id="${cat.id}"
                    aria-label="Aumentar ${cat.name}">+</button>
            </div>
        `;

            return row;
        }

        function createHiddenInput(cat) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `categories[${cat.id}]`;
            input.id = `category_quantity_${cat.id}`;
            input.value = cat.initial;
            return input;
        }

        function getCategoryIcon(slug) {
            if (['adult', 'adulto', 'adults'].includes(slug)) return 'fa-male';
            if (['kid', 'child', 'nino', 'kids', 'children'].includes(slug)) return 'fa-child';
            if (slug === 'senior') return 'fa-user-tie';
            if (slug === 'student') return 'fa-user-graduate';
            if (['infant', 'infante', 'baby'].includes(slug)) return 'fa-baby';
            return 'fa-user';
        }

        // ===== LÓGICA DE PRECIOS POR FECHA =====
        function getPriceForDate(rules, dateStr) {
            if (!dateStr) return null;

            // 1. Buscar regla específica para la fecha
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

            // 2. Fallback a default
            return rules.find(r => r.is_default) || null;
        }

        function getCategoriesForDate(dateStr) {
            if (!dateStr) return [];

            return allCategories
                .map(cat => {
                    const activeRule = getPriceForDate(cat.rules, dateStr);
                    if (!activeRule) return null;

                    return {
                        ...cat,
                        price: activeRule.price,
                        min: activeRule.min,
                        max: activeRule.max,
                    };
                })
                .filter(cat => cat !== null);
        }

        // ===== RENDERIZAR CATEGORÍAS =====
        function renderCategories(dateStr) {
            // 1. Guardar valores actuales antes de limpiar
            const currentValues = {};
            activeCategories.forEach(cat => {
                const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
                if (input) {
                    currentValues[cat.id] = parseInt(input.value || '0', 10);
                }
            });

            // Limpiar contenedor
            categoriesContainer.innerHTML = '';

            // Limpiar inputs ocultos previos
            document.querySelectorAll('input[name^="categories["]').forEach(inp => inp.remove());

            if (!dateStr) {
                // No hay fecha = no mostrar nada
                totalSection.classList.add('d-none');
                activeCategories = [];
                return;
            }

            // Obtener categorías disponibles para esta fecha
            const availableCategories = getCategoriesForDate(dateStr);

            if (availableCategories.length === 0) {
                // No hay precios para esta fecha (esto no debería pasar si el calendario bloqueó correctamente)
                totalSection.classList.add('d-none');
                activeCategories = [];
                console.warn('No prices available for date:', dateStr);
                return;
            }

            // Hay categorías disponibles
            totalSection.classList.remove('d-none');
            activeCategories = availableCategories;

            // Renderizar cada categoría
            availableCategories.forEach(cat => {
                // Restaurar valor previo si existe, respetando min/max nuevos
                let initialVal = cat.initial;
                if (currentValues.hasOwnProperty(cat.id)) {
                    let prevVal = currentValues[cat.id];
                    // Asegurar que esté dentro de los límites de la nueva fecha
                    if (prevVal < cat.min) prevVal = cat.min;
                    if (prevVal > cat.max) prevVal = cat.max;
                    initialVal = prevVal;
                }

                // Actualizar el valor inicial en el objeto cat para que createCategoryRow lo use
                cat.initial = initialVal;

                const row = createCategoryRow(cat);
                categoriesContainer.appendChild(row);

                const hidden = createHiddenInput(cat);
                categoriesContainer.appendChild(hidden);
            });

            // Configurar event listeners
            setupEventListeners();

            // Calcular totales iniciales
            updateTotals();
        }

        // ===== EVENT LISTENERS =====
        function setupEventListeners() {
            // Minus buttons
            document.querySelectorAll('.category-minus-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = parseInt(btn.getAttribute('data-category-id'), 10);
                    const inp = document.querySelector(`.category-input[data-category-id="${id}"]`);
                    if (!inp || inp.disabled) return;

                    const min = parseInt(inp.getAttribute('min') || '0', 10);
                    const cur = parseInt(inp.value || '0', 10);
                    if (cur > min) {
                        inp.value = cur - 1;
                        updateTotals();
                    }
                });
            });

            // Plus buttons
            document.querySelectorAll('.category-plus-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = parseInt(btn.getAttribute('data-category-id'), 10);
                    const inp = document.querySelector(`.category-input[data-category-id="${id}"]`);
                    if (!inp || inp.disabled) return;

                    const cur = parseInt(inp.value || '0', 10);
                    const max = parseInt(inp.getAttribute('max') || '12', 10);

                    // Total actual antes de sumar
                    const totalPax = activeCategories.reduce((sum, cat) => {
                        const i = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
                        return sum + parseInt(i?.value || '0', 10);
                    }, 0);

                    if (cur < max && totalPax < maxTotal) {
                        inp.value = cur + 1;
                        updateTotals();
                    } else if (totalPax >= maxTotal) {
                        const tpl = i18n.max_persons_reached || 'Máximo de personas alcanzado (:max).';
                        gvAlert(tpl.replace(':max', String(maxTotal)), 'warning');
                    } else {
                        const tpl = i18n.max_category_reached || 'El máximo para esta categoría es :max.';
                        gvAlert(tpl.replace(':max', String(max)), 'info');
                    }
                });
            });

            // Edición manual
            document.querySelectorAll('.category-input').forEach(inp => {
                inp.addEventListener('change', () => {
                    if (inp.disabled) return;
                    const min = parseInt(inp.getAttribute('min') || '0', 10);
                    const max = parseInt(inp.getAttribute('max') || '12', 10);
                    let val = parseInt(inp.value || '0', 10);

                    if (Number.isNaN(val)) {
                        const msg = i18n.invalid_quantity || 'Cantidad inválida.';
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
                        const tpl = i18n.max_persons_reached || 'Máximo de personas alcanzado (:max).';
                        gvAlert(tpl.replace(':max', String(maxTotal)), 'warning');
                    }
                });
            });
        }

        // ===== ACTUALIZAR TOTALES =====
        function updateTotals() {
            let totalPax = 0;
            let totalPrice = 0;

            activeCategories.forEach(cat => {
                const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
                if (!input || input.disabled) return;

                const qty = parseInt(input.value || '0', 10);
                totalPax += qty;
                totalPrice += (cat.price || 0) * qty;
            });

            const priceEl = document.getElementById('reservation-total-price-inline');
            const paxEl = document.getElementById('reservation-total-pax');
            if (priceEl) priceEl.textContent = '$' + totalPrice.toFixed(2);
            if (paxEl) paxEl.textContent = totalPax;

            // Sincronizar inputs ocultos y recalcular máximos dinámicos
            activeCategories.forEach(cat => {
                const input = document.querySelector(`.category-input[data-category-id="${cat.id}"]`);
                const hidden = document.getElementById(`category_quantity_${cat.id}`);
                if (input && hidden) hidden.value = input.value;

                if (input && !input.disabled) {
                    const current = parseInt(input.value || '0', 10);
                    const otherTotal = totalPax - current;
                    const maxAllowed = Math.min(cat.max, Math.max(0, maxTotal - otherTotal));
                    input.setAttribute('max', maxAllowed);
                }
            });

            return totalPax;
        }

        // ===== ESCUCHAR CAMBIOS DE FECHA =====
        if (dateInput) {
            dateInput.addEventListener('change', (e) => {
                renderCategories(e.target.value);
            });

            // Inicializar con fecha de referencia si no hay OLD.date
            // Esto muestra las categorías pero NO preselecciona la fecha en el input
            if (dateInput.value) {
                // Si ya tiene valor (old input por validation error), renderizar
                renderCategories(dateInput.value);
            } else {
                // Calcular fecha de referencia (hoy o mañana)
                const today = new Date();
                const todayStr = today.toISOString().split('T')[0];

                // Verificar si hoy tiene precios
                const categoriesForToday = getCategoriesForDate(todayStr);

                if (categoriesForToday.length > 0) {
                    // Renderizar con precios de hoy PERO sin setear el input
                    renderCategories(todayStr);
                } else {
                    // Intentar mañana
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const tomorrowStr = tomorrow.toISOString().split('T')[0];

                    const categoriesForTomorrow = getCategoriesForDate(tomorrowStr);
                    if (categoriesForTomorrow.length > 0) {
                        renderCategories(tomorrowStr);
                    }
                }
            }
        }
    })();
</script>
@endpush