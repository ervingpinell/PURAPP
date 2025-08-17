{{-- resources/views/admin/tours/scripts.blade.php --}}

{{-- Bootstrap y SweetAlert2 (si no los cargas globalmente en layout) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========= 1. Itinerary JSON (backend to JS) =========
    const itineraryData = @json($itineraryJson);

    // ========= 2. Mostrar ítems de itinerario (editar) =========
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
                    ? data.items.map(item => `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`).join('')
                    : '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';
            } else {
                descContainer.textContent = '';
                descContainer.style.display = 'none';
                listContainer.innerHTML = '<li class="list-group-item text-muted">Este itinerario no contiene ítems.</li>';
            }
        });
    });

    // ========= 3. Mostrar ítems en crear =========
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
                ? data.items.map(item => `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`).join('')
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
        updateItineraryViewCreate();
    }

    // ========= 4. Añadir y quitar ítems dinámicos =========
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

    // ========= 5. Actualizar totales en formularios =========
    document.querySelectorAll('.modal, .cart-form').forEach(container => {
        const adultQty = container.querySelector('.qty-adults');
        const kidQty = container.querySelector('.qty-kids');
        const totalField = container.querySelector('.total-field');
        const adultPriceInput = container.querySelector('.adult-price, [name="adult_price"]');
        const kidPriceInput = container.querySelector('.kid-price, [name="kid_price"]');
        if (!adultQty || !kidQty || !totalField || !adultPriceInput || !kidPriceInput) return;

        const adultPrice = parseFloat(adultPriceInput.value);
        const kidPrice = parseFloat(kidPriceInput.value);

        const updateTotal = () => {
            const a = parseInt(adultQty.value || 0);
            const k = parseInt(kidQty.value || 0);
            const total = (a * adultPrice) + (k * kidPrice);
            totalField.value = container.classList.contains('cart-form') ? '₡' + total.toFixed(2) : total.toFixed(2);
        };

        adultQty.addEventListener('input', updateTotal);
        kidQty.addEventListener('input', updateTotal);
        updateTotal();
    });

    // ========= 6. Enviar formulario del carrito =========
    document.querySelectorAll('form[action*="cart.store"], .cart-form').forEach(form => {
        form.addEventListener('submit', function (e) {
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
                    text: data.message || 'El tour fue añadido al carrito correctamente.',
                    timer: 1500,
                    showConfirmButton: false
                });
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) modal.hide();
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo agregar al carrito.', 'error');
            });
        });
    });

    // ========= 7. Alertas con sesiones =========
    @if(session('success'))
        Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: '{{ session("error") }}', timer: 2500, showConfirmButton: false });
    @endif

    // ========= 8. Reabrir modal si hay errores de validación =========
    @if(session('showCreateModal'))
        new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    @endif
    @if(session('showEditModal'))
        new bootstrap.Modal(document.getElementById('modalEditar{{ session("showEditModal") }}')).show();
    @endif
});
</script>
