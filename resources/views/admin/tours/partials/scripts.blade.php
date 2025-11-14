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
    currency:    @json(config('app.currency_symbol', '$')), // 👈 NUEVO
    no_prices:   @json(__('m_tours.tour.pricing.no_prices_preview') ?? 'No prices configured yet'),
  };

  // Pequeño helper para setear texto si el nodo existe
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
    const container = document.getElementById('summary-prices-body');
    const pricesContainer = document.getElementById('prices-container');

    if (!container || !pricesContainer) return;

    const cards = pricesContainer.querySelectorAll('.card');
    const items = [];

    cards.forEach(card => {
      const titleEl = card.querySelector('.card-title');
      const name = titleEl ? titleEl.textContent.trim() : '';

      const priceInput = card.querySelector('input[name^="prices["][name$="[price]"]');
      const minInput   = card.querySelector('input[name^="prices["][name$="[min_quantity]"]');
      const maxInput   = card.querySelector('input[name^="prices["][name$="[max_quantity]"]');
      const activeInput= card.querySelector('input[name^="prices["][name$="[is_active]"]');

      if (!priceInput) return; // si no hay precio, ignoramos

      const rawPrice = priceInput.value || '0';
      const price = parseFloat(rawPrice.replace(',', '.')) || 0;

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
      const priceStr = `${I18N.currency}${item.price.toFixed(2)}`;
      const rangeStr = `${item.min} - ${item.max}`;
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
    const capText = (capacityInput?.value?.trim() || I18N.na) + ' ' + I18N.people;
    setText('summary-capacity', capText);

    // Group size (nuevo)
    const groupSizeInput = document.getElementById('group_size');
    const gsText = (groupSizeInput?.value?.trim() || I18N.na) + ' ' + I18N.people;
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
      badge.textContent = colorInput.value || '#cccccc';
    }

    // Estado
    const activeInput = document.getElementById('is_active');
    const statusBadge = document.getElementById('summary-status');
    if (statusBadge) {
      statusBadge.innerHTML = activeInput?.checked
        ? `<span class="badge bg-success">${I18N.active}</span>`
        : `<span class="badge bg-secondary">${I18N.inactive}</span>`;
    }

    // 👇 NUEVO: precios
    updateSummaryPrices();
  }

  // Exponer para que inline-scripts pueda usarla
  window.updateTourSummary = updateSummary;

  // Listeners para actualizar el resumen (campos base)
  ['name','slug','overview','length','max_capacity','group_size','color','is_active'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', updateSummary);
      el.addEventListener('change', updateSummary);
    }
  });

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

  // Inicializar resumen una vez
  updateSummary();

  // ===== Validación del formulario =====
  const tourForm = document.getElementById('tourForm');
  function withSwal(fn) {
    if (window.Swal) { fn(); }
    else { setTimeout(() => window.Swal ? fn() : alert(I18N.req_title + ': ' + I18N.req_text), 100); }
  }

  if (tourForm) {
    tourForm.addEventListener('submit', function(e) {
      const name = document.getElementById('name')?.value?.trim();
      const maxCapacity = document.getElementById('max_capacity')?.value?.trim();

      if (!name || !maxCapacity) {
        e.preventDefault();
        withSwal(() => {
          Swal.fire({ icon: 'error', title: I18N.req_title, text: I18N.req_text });
        });
        return false;
      }
    });
  }

  // ===== Alertas de sesión =====
  @if(session('success'))
    withSwal(() => {
      Swal.fire({ icon: 'success', title: I18N.success, text: @json(session('success')), timer: 2000, showConfirmButton: false });
    });
  @endif

  @if(session('error'))
    withSwal(() => {
      Swal.fire({ icon: 'error', title: I18N.error, text: @json(session('error')), timer: 2500, showConfirmButton: false });
    });
  @endif
});
</script>
