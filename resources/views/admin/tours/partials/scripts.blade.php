<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== Actualización dinámica del resumen ==========
    function updateSummary() {
        // Nombre
        const nameInput = document.getElementById('name');
        if (nameInput) {
            document.getElementById('summary-name').textContent = nameInput.value || 'Sin especificar';
        }

        // Slug
        const slugInput = document.getElementById('slug');
        if (slugInput) {
            document.getElementById('summary-slug').innerHTML =
                `<code>${slugInput.value || 'Se generará automáticamente'}</code>`;
        }

        // Overview
        const overviewInput = document.getElementById('overview');
        if (overviewInput) {
            document.getElementById('summary-overview').textContent =
                overviewInput.value || 'Sin descripción';
        }

        // Duración
        const lengthInput = document.getElementById('length');
        if (lengthInput) {
            document.getElementById('summary-length').textContent =
                (lengthInput.value || 'N/A') + ' horas';
        }

        // Capacidad
        const capacityInput = document.getElementById('max_capacity');
        if (capacityInput) {
            document.getElementById('summary-capacity').textContent =
                (capacityInput.value || 'N/A') + ' personas';
        }

        // Color
        const colorInput = document.getElementById('color');
        if (colorInput) {
            const colorBadge = document.getElementById('summary-color').querySelector('.badge');
            if (colorBadge) {
                colorBadge.style.backgroundColor = colorInput.value;
                colorBadge.textContent = colorInput.value;
            }
        }

        // Estado
        const activeInput = document.getElementById('is_active');
        if (activeInput) {
            const statusBadge = document.getElementById('summary-status');
            if (activeInput.checked) {
                statusBadge.innerHTML = '<span class="badge badge-success">Activo</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge badge-secondary">Inactivo</span>';
            }
        }
    }

    // Event listeners para actualizar resumen en tiempo real
    ['name', 'slug', 'overview', 'length', 'max_capacity', 'color', 'is_active'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updateSummary);
            element.addEventListener('change', updateSummary);
        }
    });

    // ========== Validación del formulario ==========
    const tourForm = document.getElementById('tourForm');
    if (tourForm) {
        tourForm.addEventListener('submit', function(e) {
            // Validar campos requeridos
            const name = document.getElementById('name')?.value;
            const maxCapacity = document.getElementById('max_capacity')?.value;

            if (!name || !maxCapacity) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campos requeridos',
                    text: 'Por favor completa los campos obligatorios: Nombre y Capacidad Máxima'
                });
                return false;
            }
        });
    }

    // ========== Alertas de sesión ==========
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: @json(session('success')),
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error')),
            timer: 2500,
            showConfirmButton: false
        });
    @endif
});
</script>
