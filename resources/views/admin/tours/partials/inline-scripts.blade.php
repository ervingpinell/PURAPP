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

// ========== Validación de Slug en Tiempo Real ==========
let slugTimeout;
const slugInput = document.getElementById('slug');
const slugFeedback = document.getElementById('slug-feedback');

if (slugInput && slugFeedback) {
  slugInput.addEventListener('input', function() {
    clearTimeout(slugTimeout);

    if (!this.value) {
      slugFeedback.textContent = '';
      slugFeedback.className = '';
      return;
    }

    slugFeedback.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';
    slugFeedback.className = 'text-muted small';

    slugTimeout = setTimeout(async () => {
      try {
        const tourId = '{{ $tour->tour_id ?? "" }}';
        const url = '{{ route("admin.tours.ajax.validate-slug") }}';
        const params = new URLSearchParams({
          slug: slugInput.value,
          ...(tourId && { tour_id: tourId })
        });

        const response = await fetch(`${url}?${params}`);
        const data = await response.json();

        if (data.available) {
          slugFeedback.innerHTML = '<i class="fas fa-check"></i> ' + data.message;
          slugFeedback.className = 'text-success small';
          slugInput.value = data.slug;
        } else {
          slugFeedback.innerHTML = '<i class="fas fa-times"></i> ' + data.message;
          slugFeedback.className = 'text-danger small';
        }
      } catch (error) {
        slugFeedback.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error al validar';
        slugFeedback.className = 'text-warning small';
      }
    }, 500);
  });
}

// ========== Crear Categoría ==========
async function submitCreateCategory() {
  const form = document.getElementById('formCreateCategory');
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-category") }}', data);

    if (response.ok) {
      // Agregar al select de precios
      const pricesContainer = document.getElementById('prices-container');
      if (pricesContainer) {
        const newPriceHtml = createPriceCard(response.category);
        pricesContainer.insertAdjacentHTML('beforeend', newPriceHtml);
      }

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateCategory')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', 'Error', error.message);
  }
}

function createPriceCard(category) {
  return `
    <div class="card mb-3">
      <div class="card-header">
        <h4 class="card-title mb-0">
          ${category.name}
          ${category.age_range ? `<small class="text-muted">(${category.age_range})</small>` : ''}
        </h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>{{ __('m_tours.tour.prices.price_usd') }}</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                <input type="number" name="prices[${category.id}][price]" class="form-control" value="0.00" step="0.01" min="0">
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>{{ __('m_tours.tour.prices.min_quantity') }}</label>
              <input type="number" name="prices[${category.id}][min_quantity]" class="form-control" value="${category.min_quantity}" min="0">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>{{ __('m_tours.tour.prices.max_quantity') }}</label>
              <input type="number" name="prices[${category.id}][max_quantity]" class="form-control" value="${category.max_quantity}" min="0">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>{{ __('m_tours.tour.prices.status') }}</label>
              <div class="custom-control custom-switch">
                <input type="hidden" name="prices[${category.id}][is_active]" value="0">
                <input type="checkbox" class="custom-control-input" id="active_${category.id}" name="prices[${category.id}][is_active]" value="1" checked>
                <label class="custom-control-label" for="active_${category.id}">{{ __('m_tours.tour.prices.active') }}</label>
              </div>
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
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-language") }}', data);

    if (response.ok) {
      // Agregar checkbox al tab de idiomas
      const languagesContainer = document.querySelector('#languages .card-body .form-group');
      if (languagesContainer) {
        const newCheckbox = `
          <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input" id="language_${response.language.id}"
                   name="languages[]" value="${response.language.id}" checked>
            <label class="custom-control-label" for="language_${response.language.id}">
              <i class="fas fa-language"></i>
              <strong>${response.language.name}</strong>
              <code>${response.language.code.toUpperCase()}</code>
            </label>
          </div>
        `;
        languagesContainer.insertAdjacentHTML('beforeend', newCheckbox);
      }

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateLanguage')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', 'Error', error.message);
  }
}

// ========== Crear Amenidad ==========
async function submitCreateAmenity() {
  const form = document.getElementById('formCreateAmenity');
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-amenity") }}', data);

    if (response.ok) {
      // Agregar a incluidas y excluidas
      ['included', 'excluded'].forEach(type => {
        const container = document.querySelector(`#amenities .col-md-6:${type === 'included' ? 'first' : 'last'}-child .form-group`);
        if (container) {
          const newCheckbox = `
            <div class="form-check mb-2">
              <input type="checkbox" class="form-check-input" id="${type}_${response.amenity.id}"
                     name="${type}_amenities[]" value="${response.amenity.id}">
              <label class="form-check-label" for="${type}_${response.amenity.id}">
                <i class="${response.amenity.icon}"></i>
                ${response.amenity.name}
              </label>
            </div>
          `;
          container.insertAdjacentHTML('beforeend', newCheckbox);
        }
      });

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateAmenity')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', 'Error', error.message);
  }
}

// ========== Crear Horario ==========
async function submitCreateSchedule() {
  const form = document.getElementById('formCreateSchedule');
  const formData = new FormData(form);
  const data = Object.fromEntries(formData);

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-schedule") }}', data);

    if (response.ok) {
      // Agregar checkbox al tab de horarios
      const schedulesContainer = document.querySelector('#schedules .card .card-body .form-group');
      if (schedulesContainer) {
        const newCheckbox = `
          <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input" id="schedule_${response.schedule.id}"
                   name="schedules[]" value="${response.schedule.id}" checked>
            <label class="custom-control-label" for="schedule_${response.schedule.id}">
              <strong>${response.schedule.formatted}</strong>
              ${response.schedule.label ? `<span class="badge badge-info">${response.schedule.label}</span>` : ''}
            </label>
          </div>
        `;
        schedulesContainer.insertAdjacentHTML('beforeend', newCheckbox);
      }

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateSchedule')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', 'Error', error.message);
  }
}

// ========== Crear Itinerario ==========
async function submitCreateItinerary() {
  const form = document.getElementById('formCreateItinerary');
  const formData = new FormData(form);

  // Construir objeto con items
  const data = {
    name: formData.get('name'),
    description: formData.get('description'),
    items: []
  };

  // Recolectar items
  document.querySelectorAll('#itinerary-items-container .itinerary-item-card').forEach((card, index) => {
    const title = card.querySelector(`input[name*="[title]"]`)?.value;
    const description = card.querySelector(`input[name*="[description]"]`)?.value;

    if (title) {
      data.items.push({ title, description: description || '' });
    }
  });

  if (!data.name) {
    showToast('error', 'Error', 'El nombre del itinerario es requerido');
    return;
  }

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.create-itinerary") }}', data);

    if (response.ok) {
      // Agregar al select de itinerarios
      const itinerarySelect = document.getElementById('select-itinerary') ||
                              document.querySelector('select[id^="edit-itinerary-"]');

      if (itinerarySelect) {
        const newOption = document.createElement('option');
        newOption.value = response.itinerary.id;
        newOption.textContent = response.itinerary.name;
        newOption.selected = true;

        // Insertar antes de "Crear nuevo"
        const newOption_element = itinerarySelect.querySelector('option[value="new"]');
        if (newOption_element) {
          itinerarySelect.insertBefore(newOption, newOption_element);
        } else {
          itinerarySelect.appendChild(newOption);
        }

        // Disparar cambio para actualizar vista
        itinerarySelect.dispatchEvent(new Event('change'));
      }

      showToast('success', response.message);
      bootstrap.Modal.getInstance(document.getElementById('modalCreateItinerary')).hide();
      form.reset();
    }
  } catch (error) {
    showToast('error', 'Error', error.message);
  }
}

// ========== Previsualizar Traducciones ==========
async function previewTranslations(text, targetElementId) {
  if (!text) return;

  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;

  targetElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traduciendo...';

  try {
    const response = await ajaxPost('{{ route("admin.tours.ajax.preview-translations") }}', { text });

    if (response.ok) {
      let html = '<div class="translation-preview">';
      Object.entries(response.translations).forEach(([lang, translation]) => {
        html += `
          <div class="mb-2">
            <strong class="text-uppercase">${lang}:</strong>
            <span class="text-muted">${translation}</span>
          </div>
        `;
      });
      html += '</div>';
      targetElement.innerHTML = html;
    }
  } catch (error) {
    targetElement.innerHTML = '<span class="text-danger">Error al traducir</span>';
  }
}
</script>
