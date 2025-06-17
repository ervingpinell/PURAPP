{{-- Bootstrap y SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const itinerarySelect = document.getElementById('select-itinerary');
    const newItineraryFields = document.getElementById('new-itinerary-fields');

    function toggleItineraryFields() {
        const isNew = itinerarySelect.value === 'new';
        newItineraryFields.style.display = isNew ? 'block' : 'none';

        // Desactivar campos si no se usa "nuevo"
        const inputs = newItineraryFields.querySelectorAll('input, textarea, select');
        inputs.forEach(input => input.disabled = !isNew);
    }

    if (itinerarySelect && newItineraryFields) {
        itinerarySelect.addEventListener('change', toggleItineraryFields);
        toggleItineraryFields(); // Inicial
    }

    // Añadir nuevos ítems al itinerario dinámico
    document.body.addEventListener('click', function (e) {
        const addBtn = e.target.closest('.btn-add-itinerary');
        if (addBtn) {
            const container = document.querySelector(addBtn.dataset.target);
            if (!container) return;

            const idx = container.querySelectorAll('.itinerary-item').length;
            const tpl = document.getElementById('itinerary-template').innerHTML;
            const html = tpl
                .replace(/__NAME__/g, `itinerary[${idx}][title]`)
                .replace(/__DESC__/g, `itinerary[${idx}][description]`);
            container.insertAdjacentHTML('beforeend', html);
        }

        const removeBtn = e.target.closest('.btn-remove-itinerary');
        if (removeBtn) {
            const container = removeBtn.closest('.itinerary-container');
            const items = container.querySelectorAll('.itinerary-item');
            if (items.length <= 1) {
                return Swal.fire('Aviso', 'Debe haber al menos un ítem en el itinerario', 'warning');
            }

            removeBtn.closest('.itinerary-item').remove();

            // Reordenar nombres después de borrar
            container.querySelectorAll('.itinerary-item').forEach((row, i) => {
                const title = row.querySelector('input[placeholder="Título"]');
                const desc = row.querySelector('input[placeholder="Descripción"]');
                if (title && desc) {
                    title.name = `itinerary[${i}][title]`;
                    desc.name = `itinerary[${i}][description]`;
                }
            });
        }
    });

    // Mensajes de alerta con SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif
});
</script>
