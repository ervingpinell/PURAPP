{{-- resources/views/admin/tours/partials/scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ===== i18n helper (inyectamos textos una sola vez) =====
  const I18N = {
    unspecified: @json(__('m_tours.common.unspecified')),
    slug_auto:   @json(__('m_tours.tour.ui.slug_auto')),
    no_desc:     @json(__('m_tours.common.no_description')),
    hours:       @json(__('m_tours.common.hours')),
    people:      @json(__('m_tours.common.people')),
    na:          @json(__('m_tours.common.na')),
    active:      @json(__('m_tours.common.active')),
    inactive:    @json(__('m_tours.common.inactive')),
    req_title:   @json(__('m_tours.common.required_fields_title')),
    req_text:    @json(__('m_tours.common.required_fields_text')),
    success:     @json(__('m_tours.common.success')),
    error:       @json(__('m_tours.common.error')),
    currency:    @json(config('app.currency_symbol', '$')),
    no_prices:   @json(__('m_tours.tour.pricing.no_prices_preview') ?? 'No prices configured yet'),
  };

  // Pequeños helpers para tocar el DOM
  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  }
  function setHTML(id, html) {
    const el = document.getElementById(id);
    if (el) el.innerHTML = html;
  }

  // ===== Helper: construir resumen de precios =====
  function updateSummaryPrices() {
    const container       = document.getElementById('summary-prices-body');
    const pricesContainer = document.getElementById('prices-container');

    if (!container || !pricesContainer) return;

    const cards = pricesContainer.querySelectorAll('.card');
    const items = [];

    cards.forEach(card => {
      const titleEl = card.querySelector('.card-title');
      const name    = titleEl ? titleEl.textContent.trim() : '';

      const priceInput  = card.querySelector('input[name^="prices["][name$="[price]"]');
      const minInput    = card.querySelector('input[name^="prices["][name$="[min_quantity]"]');
      const maxInput    = card.querySelector('input[name^="prices["][name$="[max_quantity]"]');
      const activeInput = card.querySelector('input[name^="prices["][name$="[is_active]"]');

      if (!priceInput) return;

      const rawPrice = priceInput.value || '0';
      const price    = parseFloat(rawPrice.replace(',', '.')) || 0;

      const minQ = parseInt(minInput?.value ?? '0', 10);
      const maxQ = parseInt(maxInput?.value ?? '0', 10);

      const isActive = !!(activeInput && activeInput.checked);

      items.push({
        name,
        price,
        min: isNaN(minQ) ? 0 : minQ,
        max: isNaN(maxQ) ? 0 : maxQ,
        isActive,
      });
    });

    if (!items.length) {
      container.innerHTML = `
        <tr>
          <td colspan="4" class="text-muted small">${I18N.no_prices}</td>
        </tr>
      `;
      return;
    }

    let html = '';
    items.forEach(item => {
      const priceStr    = `${I18N.currency}${item.price.toFixed(2)}`;
      const rangeStr    = `${item.min} - ${item.max}`;
      const statusBadge = item.isActive
        ? `<span class="badge bg-success">${I18N.active}</span>`
        : `<span class="badge bg-secondary">${I18N.inactive}</span>`;

      html += `
        <tr>
          <td>${item.name || I18N.unspecified}</td>
          <td>${priceStr}</td>
          <td>${rangeStr}</td>
          <td>${statusBadge}</td>
        </tr>
      `;
    });

    container.innerHTML = html;
  }

  // ===== Helper: actualizar resumen de Itinerario =====
  function updateSummaryItinerary() {
    const emptyBox   = document.getElementById('summary-itinerary-empty');
    const contentBox = document.getElementById('summary-itinerary-content');
    const nameEl     = document.getElementById('summary-itinerary-name');
    const descEl     = document.getElementById('summary-itinerary-description');
    const listEl     = document.getElementById('summary-itinerary-items');

    if (!emptyBox && !contentBox) return;

    const select  = document.getElementById('select-itinerary');
    const nameNew = document.getElementById('new_itinerary_name');
    const descNew = document.getElementById('new_itinerary_description');

    let name        = '';
    let description = '';
    let items       = [];

    // 1) Itinerario existente seleccionado
    if (select && select.value) {
      const id   = select.value;
      const data = (window.ITINERARY_DATA || {})[id] || null;

      if (data) {
        name        = (data.name || '').trim();
        description = (data.description || '').trim();

        if (Array.isArray(data.items)) {
          items = data.items
            .map(it => (it.title || '').trim())
            .filter(t => t.length > 0);
        }
      }
    } else {
      // 2) Nuevo itinerario
      if (nameNew) name = nameNew.value.trim();
      if (descNew) descNewValue = descNew.value.trim();
      const descNewValue = descNew ? descNew.value.trim() : '';
      description = descNewValue;

      const cards = document.querySelectorAll('#itinerary-items-sortable .itinerary-sortable-item');
      cards.forEach(card => {
        const inputVisible = card.querySelector('input[name*="[title]"][type="text"]');
        const inputHidden  = card.querySelector('input[name$="[title]"][type="hidden"]');
        const title        = (inputVisible?.value || inputHidden?.value || '').trim();
        if (title) items.push(title);
      });
    }

    const hasData = !!name || !!description || items.length > 0;

    if (!hasData) {
      if (contentBox) contentBox.classList.add('d-none');
      if (emptyBox)   emptyBox.classList.remove('d-none');
      return;
    }

    if (emptyBox)   emptyBox.classList.add('d-none');
    if (contentBox) contentBox.classList.remove('d-none');

    if (nameEl) nameEl.textContent = name || @json(__('m_tours.itinerary.fields.name'));
    if (descEl) descEl.textContent = description || '';

    if (listEl) {
      if (items.length) {
        listEl.innerHTML = items
          .map(t => `<li><strong>${t}</strong></li>`)
          .join('');
      } else {
        listEl.innerHTML = '';
      }
    }
  }

  // ===== Actualización dinámica del resumen =====
  function updateSummary() {
    // Nombre
    const nameInput = document.getElementById('name');
    setText('summary-name', (nameInput?.value?.trim() || I18N.unspecified));

    // Slug
    const slugInput = document.getElementById('slug');
    setHTML('summary-slug', `<code>${(slugInput?.value?.trim() || I18N.slug_auto)}</code>`);

    // Overview
    const overviewInput = document.getElementById('overview');
    setText('summary-overview', (overviewInput?.value?.trim() || I18N.no_desc));

    // Duración
    const lengthInput = document.getElementById('length');
    const lengthText  = (lengthInput?.value?.trim() || I18N.na) + ' ' + I18N.hours;
    setText('summary-length', lengthText);

    // Capacidad
    const capacityInput = document.getElementById('max_capacity');
    const capText       = (capacityInput?.value?.trim() || I18N.na) + ' ' + I18N.people;
    setText('summary-capacity', capText);

    // Tamaño de grupo
    const groupSizeInput = document.getElementById('group_size');
    const gsText         = (groupSizeInput?.value?.trim() || I18N.na) + ' ' + I18N.people;
    setText('summary-group-size', gsText);

    // Color
    const colorInput = document.getElementById('color');
    const colorWrap  = document.getElementById('summary-color');
    if (colorInput && colorWrap) {
      let badge = colorWrap.querySelector('.badge');
      if (!badge) {
        colorWrap.innerHTML = '<span class="badge rounded-pill">&nbsp;</span>';
        badge = colorWrap.querySelector('.badge');
      }
      badge.style.backgroundColor = colorInput.value || '#cccccc';
      badge.textContent           = colorInput.value || '#cccccc';
    }

    // Estado
    const activeInput  = document.getElementById('is_active');
    const statusBadge  = document.getElementById('summary-status');
    if (statusBadge) {
      statusBadge.innerHTML = activeInput?.checked
        ? `<span class="badge bg-success">${I18N.active}</span>`
        : `<span class="badge bg-secondary">${I18N.inactive}</span>`;
    }

    // Tipo de tour
    const tourTypeSelect = document.getElementById('tour_type_id');
    if (tourTypeSelect) {
      const typeText = document.getElementById('summary-type-text');
      if (typeText) {
        const selectedOption = tourTypeSelect.options[tourTypeSelect.selectedIndex];
        typeText.textContent = selectedOption?.text || I18N.unspecified;
      }
    }

    // Precios
    updateSummaryPrices();

    // Itinerario
    updateSummaryItinerary();
  }

  // Exponer para que inline-scripts y otros puedan llamarla
  window.updateTourSummary = updateSummary;

  // Listeners para actualizar el resumen (campos base)
  ['name','slug','overview','length','max_capacity','group_size','color','is_active','tour_type_id']
    .forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('input',  updateSummary);
        el.addEventListener('change', updateSummary);
      }
    });

  // Listeners para el tab de Itinerario
  ['select-itinerary','new_itinerary_name','new_itinerary_description']
    .forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('input',  updateSummary);
        el.addEventListener('change', updateSummary);
      }
    });

  const itinContainer = document.getElementById('itinerary-items-sortable');
  if (itinContainer) {
    itinContainer.addEventListener('input', function(e) {
      if (e.target.matches('input')) updateSummary();
    });
    itinContainer.addEventListener('change', function(e) {
      if (e.target.matches('input')) updateSummary();
    });
  }

  // Listener delegado para cambios en precios (price, min, max, activo)
  const pricesContainer = document.getElementById('prices-container');
  if (pricesContainer) {
    pricesContainer.addEventListener('input', function(e) {
      if (e.target.matches('input[name^="prices["]')) {
        updateSummary();
      }
    });
    pricesContainer.addEventListener('change', function(e) {
      if (e.target.matches('input[name^="prices["]')) {
        updateSummary();
      }
    });
  }

  // Inicializar resumen una vez al cargar
  updateSummary();

  // ===== Validación del formulario =====
  const tourForm = document.getElementById('tourForm');
  function withSwal(fn) {
    if (window.Swal) {
      fn();
    } else {
      setTimeout(() => {
        if (window.Swal) fn();
        else alert(I18N.req_title + ': ' + I18N.req_text);
      }, 100);
    }
  }

  if (tourForm) {
    tourForm.addEventListener('submit', function(e) {
      const name        = document.getElementById('name')?.value?.trim();
      const maxCapacity = document.getElementById('max_capacity')?.value?.trim();

      if (!name || !maxCapacity) {
        e.preventDefault();
        withSwal(() => {
          Swal.fire({
            icon: 'error',
            title: I18N.req_title,
            text: I18N.req_text
          });
        });
        return false;
      }
    });
  }

  // ===== Alertas de sesión =====
  @if(session('success'))
    withSwal(() => {
      Swal.fire({
        icon: 'success',
        title: I18N.success,
        text: @json(session('success')),
        timer: 2000,
        showConfirmButton: false
      });
    });
  @endif

  @if(session('error'))
    withSwal(() => {
      Swal.fire({
        icon: 'error',
        title: I18N.error,
        text: @json(session('error')),
        timer: 2500,
        showConfirmButton: false
      });
    });
  @endif
});
</script>
