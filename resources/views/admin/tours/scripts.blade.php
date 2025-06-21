{{-- Bootstrap y SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========== 1. DATA JSON de itinerarios ==========
    const itineraryData = @json($itineraryJson);

    // ========== 2. Cambiar itinerario en MODALES DE EDICIÓN ==========
    document.querySelectorAll('select[id^="edit-itinerary-"]').forEach(select => {
        select.addEventListener('change', function () {
            const tourId = this.id.replace('edit-itinerary-', '');
            const selectedId = this.value;

            const sectionView = document.getElementById(`view-itinerary-items-${tourId}`);
            const sectionNew = document.getElementById(`new-itinerary-section-${tourId}`);
            const descContainer = document.getElementById(`edit-itinerary-description-${tourId}`);
            const listContainer = sectionView?.querySelector('ul');

            if (selectedId === 'new') {
                sectionNew.style.display = 'block';
                sectionView.style.display = 'none';
                if (descContainer) descContainer.style.display = 'none';
                return;
            }

            sectionNew.style.display = 'none';
            sectionView.style.display = 'block';

            const data = itineraryData[selectedId];
            if (data) {
                descContainer.textContent = data.description || '';
                descContainer.style.display = data.description ? 'block' : 'none';

                listContainer.innerHTML = data.items.length
                    ? data.items.map(item =>
                        `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`
                    ).join('')
                    : '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';
            } else {
                descContainer.textContent = '';
                descContainer.style.display = 'none';
                listContainer.innerHTML = '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';
            }
        });
    });

    // ========== 3. Mostrar descripción e ítems en CREAR ==========
    const itinerarySelect = document.getElementById('select-itinerary');
    const newItinerarySection = document.getElementById('new-itinerary-section');
    const viewSectionCreate = document.getElementById('view-itinerary-items-create');
    const viewListCreate = viewSectionCreate?.querySelector('ul');
    const descContainerCreate = document.getElementById('selected-itinerary-description');

    function updateItineraryViewCreate() {
        const selectedId = itinerarySelect.value;

        if (selectedId === 'new') {
            newItinerarySection.style.display = 'block';
            viewSectionCreate.style.display = 'none';
            descContainerCreate.style.display = 'none';
            return;
        }

        if (selectedId && itineraryData[selectedId]) {
            const data = itineraryData[selectedId];
            newItinerarySection.style.display = 'none';
            viewSectionCreate.style.display = 'block';

            viewListCreate.innerHTML = data.items.length
                ? data.items.map(item =>
                    `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`
                ).join('')
                : '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';

            descContainerCreate.textContent = data.description || '';
            descContainerCreate.style.display = data.description ? 'block' : 'none';
        } else {
            newItinerarySection.style.display = 'none';
            viewSectionCreate.style.display = 'none';
            descContainerCreate.style.display = 'none';
        }
    }

    if (itinerarySelect && newItinerarySection && viewSectionCreate) {
        itinerarySelect.addEventListener('change', updateItineraryViewCreate);
        updateItineraryViewCreate(); // inicializar al cargar
    }

    // ========== 4. Añadir y quitar ítems dinámicos ==========
    document.body.addEventListener('click', function (e) {
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

        const removeBtn = e.target.closest('.btn-remove-itinerary');
        if (removeBtn) {
            const container = removeBtn.closest('.itinerary-container');
            if (!container) return;

            removeBtn.closest('.itinerary-item').remove();

            container.querySelectorAll('.itinerary-item').forEach((row, i) => {
                const title = row.querySelector('input[placeholder="Título"]');
                const desc = row.querySelector('input[placeholder="Descripción"]');
                if (title) title.name = `itinerary[${i}][title]`;
                if (desc) desc.name = `itinerary[${i}][description]`;
            });
        }
    });

    // ========== 5. Alertas de éxito o error ==========
    @if(session('success'))
        Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: '{{ session("error") }}', timer: 2500, showConfirmButton: false });
    @endif

    // ========== 6. Reabrir modal en validación fallida ==========
    @if(session('showCreateModal'))
        new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    @endif

    @if(session('showEditModal'))
        new bootstrap.Modal(document.getElementById('modalEditar' + '{{ session("showEditModal") }}')).show();
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

