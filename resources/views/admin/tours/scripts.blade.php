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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(function(modal) {
            const adultQty = modal.querySelector('.qty-adults');
            const kidQty = modal.querySelector('.qty-kids');
            const adultPrice = parseFloat(modal.querySelector('.adult-price').value);
            const kidPrice = parseFloat(modal.querySelector('.kid-price').value);
            const totalField = modal.querySelector('.total-field');

            function updateTotal() {
                const a = parseInt(adultQty.value) || 0;
                const k = parseInt(kidQty.value) || 0;
                const total = (a * adultPrice) + (k * kidPrice);
                totalField.value = total.toFixed(2);
            }

            if (adultQty && kidQty && totalField) {
                adultQty.addEventListener('input', updateTotal);
                kidQty.addEventListener('input', updateTotal);
                updateTotal();
            }
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal form[action*="cart.store"]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error al agregar al carrito');
                return response.json(); // importante si devuelves JSON
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: 'El tour fue añadido al carrito correctamente.',
                    timer: 1800,
                    showConfirmButton: false
                });

                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Ups...',
                    text: 'No se pudo agregar al carrito. Intenta de nuevo.',
                });
            });
        });
    });
});
</script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.cart-form').forEach(form => {
        const adults = form.querySelector('.qty-adults');
        const kids = form.querySelector('.qty-kids');
        const total = form.querySelector('.total-field');
        const adultPrice = parseFloat(form.querySelector('[name="adult_price"]').value);
        const kidPrice = parseFloat(form.querySelector('[name="kid_price"]').value);

        const updateTotal = () => {
            const a = parseInt(adults.value || 0);
            const k = parseInt(kids.value || 0);
            total.value = '₡' + ((a * adultPrice) + (k * kidPrice)).toFixed(2);
        };

        adults.addEventListener('input', updateTotal);
        kids.addEventListener('input', updateTotal);
        updateTotal();

        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo agregar al carrito.', 'error');
            });
        });
    });
});
</script>

