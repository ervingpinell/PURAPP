{{-- resources/views/admin/tours/scripts.blade.php --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========= 1. Itinerary JSON =========
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
                if (sectionNew) sectionNew.style.display = 'block';
                if (sectionView) sectionView.style.display = 'none';
                if (descContainer) descContainer.style.display = 'none';
                return;
            }

            if (sectionNew) sectionNew.style.display = 'none';
            if (sectionView) sectionView.style.display = 'block';

            const data = itineraryData[selectedId];
            if (data) {
                if (descContainer){
                  descContainer.textContent = data.description || '';
                  descContainer.style.display = data.description ? 'block' : 'none';
                }
                if (listContainer){
                  listContainer.innerHTML = data.items.length
                    ? data.items.map(item => `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`).join('')
                    : `<li class="list-group-item text-muted">{{ __('m_tours.itinerary.ui.no_items_assigned') }}</li>`;
                }
            } else {
                if (descContainer){
                  descContainer.textContent = '';
                  descContainer.style.display = 'none';
                }
                if (listContainer){
                  listContainer.innerHTML = '<li class="list-group-item text-muted">{{ __('m_tours.itinerary.ui.no_items_assigned') }}</li>';
                }
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
        const selectedId = itinerarySelect?.value;

        if (selectedId === 'new') {
            if (newItinerarySection) newItinerarySection.style.display = 'block';
            if (viewSectionCreate) viewSectionCreate.style.display = 'none';
            if (descContainerCreate) descContainerCreate.style.display = 'none';
            return;
        }

        if (selectedId && itineraryData[selectedId]) {
            const data = itineraryData[selectedId];
            if (newItinerarySection) newItinerarySection.style.display = 'none';
            if (viewSectionCreate) viewSectionCreate.style.display = 'block';
            if (viewListCreate) viewListCreate.innerHTML = data.items.length
                ? data.items.map(item => `<li class="list-group-item"><strong>${item.title}</strong><br><small class="text-muted">${item.description}</small></li>`).join('')
                : `<li class="list-group-item text-muted">{{ __('m_tours.itinerary.ui.no_items_assigned') }}</li>`;
            if (descContainerCreate){
              descContainerCreate.textContent = data.description || '';
              descContainerCreate.style.display = data.description ? 'block' : 'none';
            }
        } else {
            if (newItinerarySection) newItinerarySection.style.display = 'none';
            if (viewSectionCreate) viewSectionCreate.style.display = 'none';
            if (descContainerCreate) descContainerCreate.style.display = 'none';
        }
    }

    if (itinerarySelect) {
        itinerarySelect.addEventListener('change', updateItineraryViewCreate);
        updateItineraryViewCreate();
    }

    // ========= 4. Añadir / quitar ítems dinámicos (sin textos visibles) =========
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

    // ========= 5. Totales (sin textos visibles; solo valores) =========

    // ========= 6. Enviar carrito (mensajes i18n) =========
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
                    title: '{{ __('m_tours.tour.ui.added_to_cart') }}', // i18n: agregar ui.added_to_cart
                    text: data.message || '{{ __('m_tours.tour.ui.added_to_cart_text') }}', // i18n: agregar ui.added_to_cart_text
                    timer: 1500,
                    showConfirmButton: false
                });
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) modal.hide();
            })
            .catch(() => {
                Swal.fire('{{ __('m_tours.common.error_title') }}', '{{ __('m_tours.tour.error.create') }}', 'error');
            });
        });
    });

    // ========= 7. Alertas por sesión =========
    @if(session('success'))
        Swal.fire({ icon: 'success', title: @json(session('success')), timer: 2000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: @json(session('error')), timer: 2500, showConfirmButton: false });
    @endif

    // ========= 8. Reabrir modal si hay errores =========
    @if(session('showCreateModal'))
        new bootstrap.Modal(document.getElementById('modalRegistrar')).show();
    @endif
    @if(session('showEditModal'))
        new bootstrap.Modal(document.getElementById('modalEditar{{ session("showEditModal") }}')).show();
    @endif
});
</script>
