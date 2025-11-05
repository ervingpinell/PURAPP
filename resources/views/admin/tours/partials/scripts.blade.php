{{-- resources/views/admin/tours/partials/scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ========== Actualización dinámica del resumen ==========
  function updateSummary() {
    // Nombre
    const nameInput = document.getElementById('name');
    if (nameInput) {
      document.getElementById('summary-name').textContent = nameInput.value || @json(__('m_tours.common.unspecified'));
    }

    // Slug
    const slugInput = document.getElementById('slug');
    if (slugInput) {
      document.getElementById('summary-slug').innerHTML =
        `<code>${slugInput.value || @json(__('m_tours.tour.ui.slug_auto'))}</code>`;
    }

    // Overview
    const overviewInput = document.getElementById('overview');
    if (overviewInput) {
      document.getElementById('summary-overview').textContent =
        overviewInput.value || @json(__('m_tours.common.no_description'));
    }

    // Duración
    const lengthInput = document.getElementById('length');
    if (lengthInput) {
      document.getElementById('summary-length').textContent =
        (lengthInput.value || 'N/A') + ' ' + @json(__('m_tours.common.hours'));
    }

    // Capacidad
    const capacityInput = document.getElementById('max_capacity');
    if (capacityInput) {
      document.getElementById('summary-capacity').textContent =
        (capacityInput.value || 'N/A') + ' ' + @json(__('m_tours.common.people'));
    }

    // Color
    const colorInput = document.getElementById('color');
    if (colorInput) {
      const wrap = document.getElementById('summary-color');
      if (wrap) {
        let badge = wrap.querySelector('.badge');
        if (!badge) {
          wrap.innerHTML = '<span class="badge rounded-pill">&nbsp;</span>';
          badge = wrap.querySelector('.badge');
        }
        badge.style.backgroundColor = colorInput.value;
        badge.textContent = colorInput.value;
      }
    }

    // Estado
    const activeInput = document.getElementById('is_active');
    if (activeInput) {
      const statusBadge = document.getElementById('summary-status');
      if (activeInput.checked) {
        statusBadge.innerHTML = '<span class="badge bg-success">{{ __('m_tours.common.active') }}</span>';
      } else {
        statusBadge.innerHTML = '<span class="badge bg-secondary">{{ __('m_tours.common.inactive') }}</span>';
      }
    }
  }

  // Event listeners para actualizar resumen en tiempo real
  ['name', 'slug', 'overview', 'length', 'max_capacity', 'color', 'is_active'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', updateSummary);
      el.addEventListener('change', updateSummary);
    }
  });

  // Inicializar
  updateSummary();

  // ========== Validación del formulario ==========
  const tourForm = document.getElementById('tourForm');
  if (tourForm) {
    tourForm.addEventListener('submit', function(e) {
      const name = document.getElementById('name')?.value;
      const maxCapacity = document.getElementById('max_capacity')?.value;

      if (!name || !maxCapacity) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: @json(__('m_tours.common.required_fields_title')),
          text:  @json(__('m_tours.common.required_fields_text'))
        });
        return false;
      }
    });
  }

  // ========== Alertas de sesión ==========
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: @json(__('m_tours.common.success')),
      text:  @json(session('success')),
      timer: 2000,
      showConfirmButton: false
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: @json(__('m_tours.common.error')),
      text:  @json(session('error')),
      timer: 2500,
      showConfirmButton: false
    });
  @endif
});
</script>
