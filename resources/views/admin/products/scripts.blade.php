{{-- resources/views/admin/tours/scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ========= i18n (JS) =========
  const I18N = {
    noItems: @json(__('m_tours.itinerary.ui.no_items_assigned')),
    addedTitle: @json(__('m_tours.product.ui.added_to_cart')),
    addedText:  @json(__('m_tours.product.ui.added_to_cart_text')),
    errorTitle: @json(__('m_tours.common.error')),
    errorText:  @json(__('m_tours.product.error.create')),
  };

  // ========= 1. Itinerary JSON =========
  // Estructura esperada: { [itinerary_id]: { description: string, items: [{title, description}, ...] } }
  const itineraryData = @json($itineraryJson ?? []);

  // ========= Utils =========
  const $ = (s, ctx=document) => ctx.querySelector(s);

  function setBlock(el, show) {
    if (!el) return;
    el.style.display = show ? 'block' : 'none';
  }

  function renderItemsList(items) {
    if (!Array.isArray(items) || items.length === 0) {
      return `<li class="list-group-item text-muted">${I18N.noItems}</li>`;
    }
    return items.map(item => `
      <li class="list-group-item">
        <strong>${item?.title ?? ''}</strong><br>
        <small class="text-muted">${item?.description ?? ''}</small>
      </li>
    `).join('');
  }

  function reindexItinerary(container) {
    if (!container) return;
    container.querySelectorAll('.itinerary-item').forEach((row, i) => {
      const title = row.querySelector('input[data-role="title"], input[name$="[title]"], textarea[name$="[title]"]');
      const desc  = row.querySelector('textarea[data-role="description"], input[data-role="description"], input[name$="[description]"], textarea[name$="[description]"]');
      if (title) title.name = `itinerary[${i}][title]`;
      if (desc)  desc.name  = `itinerary[${i}][description]`;
    });
  }

  // ========= 2. Mostrar ítems de itinerario (editar) =========
  document.querySelectorAll('select[id^="edit-itinerary-"]').forEach(select => {
    select.addEventListener('change', function () {
      const productId        = this.id.replace('edit-itinerary-', '');
      const selectedId    = this.value;
      const sectionView   = document.getElementById(`view-itinerary-items-${productId}`);
      const sectionNew    = document.getElementById(`new-itinerary-section-${productId}`);
      const descContainer = document.getElementById(`edit-itinerary-description-${productId}`);
      const listContainer = sectionView?.querySelector('ul');

      if (selectedId === 'new') {
        setBlock(sectionNew, true);
        setBlock(sectionView, false);
        setBlock(descContainer, false);
        return;
      }

      setBlock(sectionNew, false);
      setBlock(sectionView, true);

      const data = itineraryData?.[selectedId];
      if (data) {
        if (descContainer){
          descContainer.textContent = data.description || '';
          setBlock(descContainer, !!data.description);
        }
        if (listContainer){
          listContainer.innerHTML = renderItemsList(data.items);
        }
      } else {
        if (descContainer){ descContainer.textContent = ''; setBlock(descContainer, false); }
        if (listContainer){ listContainer.innerHTML = `<li class="list-group-item text-muted">${I18N.noItems}</li>`; }
      }
    });
  });

  // ========= 3. Mostrar ítems en crear =========
  const itinerarySelect       = document.getElementById('select-itinerary');
  const newItinerarySection   = document.getElementById('new-itinerary-section');
  const viewSectionCreate     = document.getElementById('view-itinerary-items-create');
  const viewListCreate        = viewSectionCreate?.querySelector('ul');
  const descContainerCreate   = document.getElementById('selected-itinerary-description');

  function updateItineraryViewCreate() {
    const selectedId = itinerarySelect?.value;

    if (selectedId === 'new') {
      setBlock(newItinerarySection, true);
      setBlock(viewSectionCreate, false);
      setBlock(descContainerCreate, false);
      return;
    }

    const data = selectedId ? itineraryData?.[selectedId] : null;

    if (data) {
      setBlock(newItinerarySection, false);
      setBlock(viewSectionCreate, true);
      if (viewListCreate) viewListCreate.innerHTML = renderItemsList(data.items);
      if (descContainerCreate){
        descContainerCreate.textContent = data.description || '';
        setBlock(descContainerCreate, !!data.description);
      }
    } else {
      setBlock(newItinerarySection, false);
      setBlock(viewSectionCreate, false);
      setBlock(descContainerCreate, false);
    }
  }

  if (itinerarySelect) {
    itinerarySelect.addEventListener('change', updateItineraryViewCreate);
    updateItineraryViewCreate();
  }

  // ========= 4. Añadir / quitar ítems dinámicos =========
  document.body.addEventListener('click', function (e) {
    const addBtn = e.target.closest('.btn-add-itinerary');
    if (addBtn) {
      const container = document.querySelector(addBtn.dataset.target);
      const template  = document.getElementById('itinerary-template');
      if (!container || !template) return;

      const idx = container.querySelectorAll('.itinerary-item').length;
      // El template debe tener los placeholders __NAME__ y __DESC__
      const html = template.innerHTML
        .replace(/__NAME__/g, `itinerary[${idx}][title]`)
        .replace(/__DESC__/g, `itinerary[${idx}][description]`);
      container.insertAdjacentHTML('beforeend', html);
      reindexItinerary(container);
    }

    const removeBtn = e.target.closest('.btn-remove-itinerary');
    if (removeBtn) {
      const container = removeBtn.closest('.itinerary-container');
      if (!container) return;
      removeBtn.closest('.itinerary-item')?.remove();
      reindexItinerary(container);
    }
  });

  // ========= 6. Enviar carrito (i18n y manejo de errores) =========
  document.querySelectorAll('.cart-form').forEach(form => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      try {
        const formData = new FormData(form);
        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });

        let data = {};
        try { data = await res.json(); } catch (_) {}

        if (!res.ok) {
          throw new Error(data?.message || 'Request failed');
        }

        Swal.fire({
          icon: 'success',
          title: I18N.addedTitle,
          text: data?.message || I18N.addedText,
          timer: 1500,
          showConfirmButton: false
        });

        const modalEl = form.closest('.modal');
        if (modalEl) {
          const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          modal.hide();
        }
      } catch (err) {
        Swal.fire(I18N.errorTitle, I18N.errorText, 'error');
      }
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
    const m1 = document.getElementById('modalRegistrar');
    if (m1) new bootstrap.Modal(m1).show();
  @endif
  @if(session('showEditModal'))
    const m2 = document.getElementById('modalEditar{{ session('showEditModal') }}');
    if (m2) new bootstrap.Modal(m2).show();
  @endif
});
</script>
