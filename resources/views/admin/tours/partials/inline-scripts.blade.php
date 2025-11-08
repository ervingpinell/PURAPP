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
    throw new Error(error.message || 'Request failed');
  }

  return response.json();
}

function showToast(icon, title, text = '') {
  Swal.fire({
    icon,
    title,
    text,
    timer: 2500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
  });
}

function bs5Validate(form) {
  // Simple validación Bootstrap 5
  if (!form) return true;
  let ok = true;
  form.querySelectorAll('[required]').forEach(el => {
    if (!el.value) { el.classList.add('is-invalid'); ok = false; }
    else { el.classList.remove('is-invalid'); }
  });
  return ok;
}

// ========== Crear Categoría ==========
async function submitCreateCategory() {
  const form = document.getElementById('formCreateCategory');
  if (!bs5Validate(form)) return;

  // Validación min <= max
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
      // Agregar tarjeta a precios (usa B5)
      const pricesContainer = document.getElementById('prices-container');
      if (pricesContainer) {
        pricesContainer.insertAdjacentHTML('beforeend', createPriceCard(response.category));
      }

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateCategory')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', '{{ __("m_tours.common.error") }}', error.message);
  }
}

function createPriceCard(category) {
  // Usa form-switch en BS5 (no custom-control)
  return `
    <div class="card mb-3">
      <div class="card-header">
        <h4 class="card-title mb-0">
          ${category.name}
          ${category.age_range ? `<small class="text-muted">(${category.age_range})</small>` : ''}
        </h4>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">{{ __('m_tours.tour.prices.price_usd') }}</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" name="prices[${category.id}][price]" class="form-control" value="0.00" step="0.01" min="0">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label">{{ __('m_tours.tour.prices.min_quantity') }}</label>
            <input type="number" name="prices[${category.id}][min_quantity]" class="form-control" value="${category.min_quantity ?? 0}" min="0">
          </div>
          <div class="col-md-3">
            <label class="form-label">{{ __('m_tours.tour.prices.max_quantity') }}</label>
            <input type="number" name="prices[${category.id}][max_quantity]" class="form-control" value="${category.max_quantity ?? 12}" min="0">
          </div>
          <div class="col-md-2">
            <label class="form-label d-block">{{ __('m_tours.tour.prices.status') }}</label>
            <div class="form-check form-switch">
              <input type="hidden" name="prices[${category.id}][is_active]" value="0">
              <input class="form-check-input" type="checkbox" id="active_${category.id}"
                     name="prices[${category.id}][is_active]" value="1" checked>
              <label class="form-check-label" for="active_${category.id}">{{ __('m_tours.tour.prices.active') }}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="prices[${category.id}][category_id]" value="${category.id}">
  `;
}

// ========== Crear Idioma ==========
async function submitCreateLanguage() {
  const form = document.getElementById('formCreateLanguage');
  if (!bs5Validate(form)) return;

  const data = Object.fromEntries(new FormData(form));

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-language") }}', data);

    if (response.ok) {
      // Contenedor de idiomas: intenta por id semántico, luego fallback al primer form-group del tab
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

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateLanguage')).hide();
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
      // Intenta encontrar contenedores específicos; si no, usa dos columnas del tab
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

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateAmenity')).hide();
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

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateSchedule')).hide();
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

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateItinerary'))?.hide();
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
