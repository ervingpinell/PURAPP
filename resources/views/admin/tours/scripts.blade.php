{{-- resources/views/admin/tours/scripts.blade.php --}}

{{-- Bootstrap y SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Mostrar/ocultar bloque de nuevo itinerario en el CREATE
    const itinerarySelect = document.getElementById('select-itinerary');
    const newItineraryFields = document.getElementById('new-itinerary-fields');
    if (itinerarySelect && newItineraryFields) {
        const toggleIt = () => {
            const isNew = itinerarySelect.value === 'new';
            newItineraryFields.style.display = isNew ? 'block' : 'none';
            newItineraryFields.querySelectorAll('input, textarea, select')
                .forEach(i => i.disabled = !isNew);
        };
        itinerarySelect.addEventListener('change', toggleIt);
        toggleIt();
    }

    // 2. Añadir/Quitar ítems dinámicos (tanto en CREATE como en EDIT)
    document.body.addEventListener('click', function (e) {
        // añadir
        const add = e.target.closest('.btn-add-itinerary');
        if (add) {
            const container = document.querySelector(add.dataset.target);
            const idx = container.querySelectorAll('.itinerary-item').length;
            const tpl = document.getElementById('itinerary-template').innerHTML;
            const html = tpl
                .replace(/__NAME__/g, `itinerary[${idx}][title]`)
                .replace(/__DESC__/g, `itinerary[${idx}][description]`);
            container.insertAdjacentHTML('beforeend', html);
        }
        // eliminar
        const rem = e.target.closest('.btn-remove-itinerary');
        if (rem) {
            const cont = rem.closest('.itinerary-container');
            if (cont.querySelectorAll('.itinerary-item').length > 1) {
                rem.closest('.itinerary-item').remove();
                // reindexar
                cont.querySelectorAll('.itinerary-item').forEach((row,i) => {
                    row.querySelector('input[placeholder="Título"]').name = `itinerary[${i}][title]`;
                    row.querySelector('input[placeholder="Descripción"]').name = `itinerary[${i}][description]`;
                });
            } else {
                Swal.fire('Aviso','Debe haber al menos un ítem en el itinerario','warning');
            }
        }
    });

    // 3. SweetAlert para feedback
    @if(session('success'))
        Swal.fire({ icon:'success', title:'{{ session("success") }}', timer:2000, showConfirmButton:false });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:'{{ session("error") }}', timer:2000, showConfirmButton:false });
    @endif

    // 4. Reabrir modal tras validación fallida
    @if(session('showCreateModal'))
        new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    @endif
    @if(session('showEditModal'))
        new bootstrap.Modal(
          document.getElementById('modalEditar' + '{{ session("showEditModal") }}')
        ).show();
    @endif
});
</script>
