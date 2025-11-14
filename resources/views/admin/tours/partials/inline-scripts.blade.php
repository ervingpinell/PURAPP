{{-- resources/views/admin/tours/partials/inline-scripts.blade.php --}}

{{-- ===== Inline Scripts (creación rápida vía AJAX) ===== --}}
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// ========== Helpers ==========
async function ajaxPost(url, data) {
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN,
      'Accept': 'application/json'
    },
    body: JSON.stringify(data)
  });

  if (!response.ok) {
    const error = await response.json().catch(() => ({}));
    // Intentar extraer primer mensaje de error de validación
    if (error.errors) {
      const firstKey = Object.keys(error.errors)[0];
      const msg = error.errors[firstKey]?.[0] || 'Request failed';
      throw new Error(msg);
    }
    throw new Error(error.message || 'Request failed');
  }

  return response.json();
}

// Toast oscuro (para tema dark de AdminLTE)
function showToast(icon, title, text = '') {
  if (!window.Swal) {
    alert(`${title}\n${text}`);
    return;
  }

  Swal.fire({
    icon,
    title,
    text,
    timer: 2800,
    showConfirmButton: false,
    toast: true,
    position: 'top-end',
    background: '#111827',    // gris muy oscuro
    color: '#E5E7EB',         // texto claro
    iconColor: icon === 'success'
      ? '#22C55E'             // verde
      : icon === 'error'
        ? '#F87171'           // rojo
        : '#60A5FA',          // azul info
    customClass: {
      popup: 'shadow-lg border-0',
      title: 'fw-semibold',
    }
  });
}

// Helper para validación simple Bootstrap 5
function bs5Validate(form) {
  if (!form) return true;
  let ok = true;
  form.querySelectorAll('[required]').forEach(el => {
    if (!el.value) { el.classList.add('is-invalid'); ok = false; }
    else { el.classList.remove('is-invalid'); }
  });
  return ok;
}

// Helper para cerrar bien un modal (y limpiar el backdrop si se queda pegado)
function hideModalById(id) {
  const modalEl = document.getElementById(id);
  if (!modalEl || !window.bootstrap) return;

  const instance = bootstrap.Modal.getInstance(modalEl) ?? new bootstrap.Modal(modalEl);
  instance.hide();

  modalEl.addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
  }, { once: true });
}

// ========== Helper: remover card de precios ==========
function removePriceCard(categoryId) {
  const card = document.getElementById('price-card-' + categoryId);
  if (card) {
    card.remove();
    if (window.updateTourSummary) {
      window.updateTourSummary();
    }
  }
}

// ========== Helper: construir card de precios en JS ==========
function createPriceCard(category) {
  const id        = category.id || category.category_id;
  const name      = category.name || category.label || category.slug || ('ID ' + id);
  const ageRange  = category.age_range || '';
  const slug      = category.slug || '';
  const price     = parseFloat(category.price ?? 0);
  const minQty    = parseInt(category.min_quantity ?? 0, 10);
  const maxQty    = parseInt(category.max_quantity ?? 12, 10);
  const isActive  = (category.is_active ?? true) ? true : false;

  return `
    <div class="card mb-3 shadow-sm price-card" id="price-card-${id}" data-category-id="${id}">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="card-title mb-0">
            ${name}
            ${ageRange ? `<small class="text-muted">(${ageRange})</small>` : ''}
          </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
          ${slug ? `<span class="badge bg-secondary">${slug}</span>` : ''}
          <button
            type="button"
            class="btn btn-sm btn-outline-danger"
            onclick="removePriceCard(${id})"
            aria-label="{{ __('m_tours.tour.pricing.remove_category') }}"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label">
              {{ __('m_tours.tour.pricing.price_usd') }}
            </label>
            <div class="input-group">
              <span class="input-group-text" aria-hidden="true">{{ config('app.currency_symbol', '$') }}</span>
              <input
                type="number"
                name="prices[${id}][price]"
                class="form-control"
                value="${price.toFixed(2)}"
                step="0.01"
                min="0"
                inputmode="decimal"
              >
            </div>
          </div>

          <div class="col-6 col-md-3 col-lg-3">
            <label class="form-label">
              {{ __('m_tours.tour.pricing.min_quantity') }}
            </label>
            <input
              type="number"
              name="prices[${id}][min_quantity]"
              class="form-control"
              value="${isNaN(minQty) ? 0 : minQty}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          <div class="col-6 col-md-3 col-lg-3">
            <label class="form-label">
              {{ __('m_tours.tour.pricing.max_quantity') }}
            </label>
            <input
              type="number"
              name="prices[${id}][max_quantity]"
              class="form-control"
              value="${isNaN(maxQty) ? 12 : maxQty}"
              min="0"
              max="255"
              inputmode="numeric"
            >
          </div>

          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label d-block">
              {{ __('m_tours.tour.pricing.status') }}
            </label>
            <div class="form-check form-switch">
              <input type="hidden" name="prices[${id}][is_active]" value="0">
              <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="active_${id}"
                name="prices[${id}][is_active]"
                value="1"
                ${isActive ? 'checked' : ''}
              >
              <label class="form-check-label" for="active_${id}">
                {{ __('m_tours.tour.pricing.active') }}
              </label>
            </div>
            <small class="text-muted d-block mt-1">
              {{ __('m_tours.tour.pricing.hints.zero_disables') }}
            </small>
          </div>
        </div>
      </div>

      <input type="hidden" name="prices[${id}][category_id]" value="${id}">
    </div>
  `;
}

// ========== Asignar categoría existente desde el selector ==========
document.addEventListener('DOMContentLoaded', () => {
  const selector        = document.getElementById('category-selector');
  const addBtn          = document.getElementById('btn-add-category');
  const pricesContainer = document.getElementById('prices-container');

  if (selector && addBtn && pricesContainer) {
    addBtn.addEventListener('click', () => {
      const id = selector.value;
      if (!id) return;

      // Evitar duplicados
      if (document.getElementById('price-card-' + id)) {
        showToast(
          'info',
          '{{ __('m_tours.common.info') }}',
          '{{ __('m_tours.tour.pricing.category_already_added') }}'
        );
        return;
      }

      const opt = selector.options[selector.selectedIndex];
      if (!opt) return;

      const label    = opt.textContent.trim();
      const ageRange = opt.dataset.ageRange || '';
      const slug     = opt.dataset.slug || '';

      const cat = {
        id: id,
        name: label,
        age_range: ageRange,
        slug: slug,
        is_active: true,
        min_quantity: 0,
        max_quantity: 12,
        price: 0
      };

      pricesContainer.insertAdjacentHTML('beforeend', createPriceCard(cat));

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }
    });
  }
});

// ========== Crear Categoría (AJAX) ==========
async function submitCreateCategory() {
  const form = document.getElementById('formCreateCategory');
  if (!bs5Validate(form)) return;

  const fd  = new FormData(form);
  const min = parseInt(fd.get('min_quantity') || '0', 10);
  const max = parseInt(fd.get('max_quantity') || '0', 10);

  if (max < min) {
    showToast('error', '{{ __("m_tours.common.error") }}', '{{ __("m_tours.tour.modal.errors.min_le_max") }}');
    return;
  }

  const data = Object.fromEntries(fd);

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-category") }}', data);

    if (response.ok) {
      const pricesContainer = document.getElementById('prices-container');
      if (pricesContainer) {
        pricesContainer.insertAdjacentHTML('beforeend', createPriceCard(response.category));
      }

      // Añadir también la nueva categoría al selector
      const selector = document.getElementById('category-selector');
      if (selector) {
        const opt = document.createElement('option');
        opt.value = response.category.id;
        opt.textContent = response.category.name;
        opt.dataset.ageRange = response.category.age_range || '';
        opt.dataset.slug = response.category.slug || '';
        selector.appendChild(opt);
        selector.value = response.category.id;
      }

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }

      showToast('success', response.message);
      hideModalById('modalCreateCategory');
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

// ========== Crear Idioma ==========
async function submitCreateLanguage() {
  const form = document.getElementById('formCreateLanguage');
  if (!bs5Validate(form)) return;

  const data = Object.fromEntries(new FormData(form));

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-language") }}', data);

    if (response.ok) {
      const container =
        document.getElementById('languages-container') ||
        document.querySelector('#languages .card-body .form-group') ||
        document.querySelector('#languages .card-body');

      if (container) {
        const newCheckbox = `
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="language_${response.language.id}"
                   name="languages[]" value="${response.language.id}" checked>
            <label class="form-check-label" for="language_${response.language.id}">
              <i class="fas fa-language"></i>
              <strong>${response.language.name}</strong>
              <code>${(response.language.code || '').toUpperCase()}</code>
            </label>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', newCheckbox);
      }

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }

      showToast('success', response.message);
      hideModalById('modalCreateLanguage');
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

// ========== Crear Amenidad ==========
async function submitCreateAmenity() {
  const form = document.getElementById('formCreateAmenity');
  if (!bs5Validate(form)) return;

  const data = Object.fromEntries(new FormData(form));

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-amenity") }}', data);

    if (response.ok) {
      const includedContainer =
        document.getElementById('amenities-included') ||
        document.querySelector('#amenities .col-md-6:nth-child(1) .form-group') ||
        document.querySelector('#amenities .col-md-6:nth-child(1)');

      const excludedContainer =
        document.getElementById('amenities-excluded') ||
        document.querySelector('#amenities .col-md-6:nth-child(2) .form-group') ||
        document.querySelector('#amenities .col-md-6:nth-child(2)');

      const checkboxHtml = (type) => `
        <div class="form-check mb-2">
          <input type="checkbox" class="form-check-input" id="${type}_${response.amenity.id}"
                 name="${type}_amenities[]" value="${response.amenity.id}">
          <label class="form-check-label" for="${type}_${response.amenity.id}">
            <i class="${response.amenity.icon || 'fas fa-check'}"></i>
            ${response.amenity.name}
          </label>
        </div>
      `;

      if (includedContainer) includedContainer.insertAdjacentHTML('beforeend', checkboxHtml('included'));
      if (excludedContainer) excludedContainer.insertAdjacentHTML('beforeend', checkboxHtml('excluded'));

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }

      showToast('success', response.message);
      hideModalById('modalCreateAmenity');
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

// ========== Crear Horario ==========
async function submitCreateSchedule() {
  const form = document.getElementById('formCreateSchedule');
  if (!bs5Validate(form)) return;

  const data = Object.fromEntries(new FormData(form));

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-schedule") }}', data);

    if (response.ok) {
      const container =
        document.getElementById('schedules-container') ||
        document.querySelector('#schedules .card .card-body .form-group') ||
        document.querySelector('#schedules .card .card-body');

      if (container) {
        const newCheckbox = `
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="schedule_${response.schedule.id}"
                   name="schedules[]" value="${response.schedule.id}" checked>
            <label class="form-check-label" for="schedule_${response.schedule.id}">
              <strong>${response.schedule.formatted}</strong>
              ${response.schedule.label ? `<span class="badge bg-info ms-1">${response.schedule.label}</span>` : ''}
            </label>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', newCheckbox);
      }

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }

      showToast('success', response.message);
      hideModalById('modalCreateSchedule');
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

// ========== Crear Itinerario ==========
async function submitCreateItinerary() {
  const form = document.getElementById('formCreateItinerary');
  if (!form) return;

  if (!bs5Validate(form)) return;

  const fd = new FormData(form);
  const data = {
    name: fd.get('name'),
    description: fd.get('description') || '',
    items: []
  };

  document.querySelectorAll('#itinerary-items-container .itinerary-item-card').forEach(card => {
    const title = card.querySelector('input[name*="[title]"]')?.value?.trim();
    const description = card.querySelector('input[name*="[description]"]')?.value?.trim() || '';
    if (title) data.items.push({ title, description });
  });

  if (!data.name) {
    showToast('error', '{{ __("m_tours.common.error") }}', '{{ __("m_tours.itinerary.validation.name_required") }}');
    return;
  }

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-itinerary") }}', data);

    if (response.ok) {
      const itinerarySelect =
        document.getElementById('select-itinerary') ||
        document.querySelector('select[id^="edit-itinerary-"]');

      if (itinerarySelect) {
        const opt = document.createElement('option');
        opt.value = response.itinerary.id;
        opt.textContent = response.itinerary.name;
        opt.selected = true;

        const beforeNew = itinerarySelect.querySelector('option[value="new"]');
        if (beforeNew) itinerarySelect.insertBefore(opt, beforeNew);
        else itinerarySelect.appendChild(opt);

        itinerarySelect.dispatchEvent(new Event('change'));
      }

      if (window.updateTourSummary) {
        window.updateTourSummary();
      }

      showToast('success', response.message);
      hideModalById('modalCreateItinerary');
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

// ========== Previsualizar Traducciones ==========
async function previewTranslations(text, targetElementId) {
  if (!text) return;

  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;

  targetElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("m_tours.common.translating") }}...';

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.preview-translations") }}', { text });

    if (response.ok) {
      let html = '<div class="translation-preview">';
      Object.entries(response.translations).forEach(([lang, translation]) => {
        html += `
          <div class="mb-2">
            <strong class="text-uppercase">${lang}:</strong>
            <span class="text-muted">${translation}</span>
          </div>`;
      });
      html += '</div>';
      targetElement.innerHTML = html;
    }
  } catch {
    targetElement.innerHTML = '<span class="text-danger">{{ __("m_tours.common.error_translating") }}</span>';
  }
}
</script>
