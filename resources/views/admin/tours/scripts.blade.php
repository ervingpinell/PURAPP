{{-- Bootstrap y SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Mostrar/ocultar sección de nuevo itinerario al seleccionar "new"
    const itinerarySelect = document.getElementById('select-itinerary');
    const newItinerarySection = document.getElementById('new-itinerary-section');

    function toggleNewItinerary() {
        if (!itinerarySelect || !newItinerarySection) return;
        const isNew = itinerarySelect.value === 'new';
        newItinerarySection.style.display = isNew ? 'block' : 'none';
    }

    if (itinerarySelect && newItinerarySection) {
        itinerarySelect.addEventListener('change', toggleNewItinerary);
        toggleNewItinerary();
    }

    // Añadir y quitar ítems dinámicos
    document.body.addEventListener('click', function (e) {
        // Añadir ítem
        const addBtn = e.target.closest('.btn-add-itinerary');
        if (addBtn) {
            const container = document.querySelector(addBtn.dataset.target);
            const template = document.getElementById('itinerary-template');
            if (!container || !template) return;

            const idx = container.querySelectorAll('.itinerary-item').length;
            const html = template.innerHTML
                .replace(/__NAME__/g, `itinerary[${idx}][title]`)
                .replace(/__DESC__/g, `itinerary[${idx}][description]`);
            container.insertAdjacentHTML('beforeend', html);
        }

        // Quitar ítem
        const removeBtn = e.target.closest('.btn-remove-itinerary');
        if (removeBtn) {
            const container = removeBtn.closest('.itinerary-container');
            if (!container) return;

            removeBtn.closest('.itinerary-item').remove();

            // Reindexar inputs (aunque esté vacío)
            container.querySelectorAll('.itinerary-item').forEach((row, i) => {
                const title = row.querySelector('input[placeholder="Título"]');
                const desc = row.querySelector('input[placeholder="Descripción"]');
                if (title) title.name = `itinerary[${i}][title]`;
                if (desc) desc.name = `itinerary[${i}][description]`;
            });
        }
    });

    // Mostrar alertas de sesión
    @if(session('success'))
        Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: '{{ session("error") }}', timer: 2500, showConfirmButton: false });
    @endif

    // Reabrir modal tras validación fallida
    @if(session('showCreateModal'))
        new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    @endif

    @if(session('showEditModal'))
        new bootstrap.Modal(document.getElementById('modalEditar' + '{{ session("showEditModal") }}')).show();
    @endif
});
</script>
