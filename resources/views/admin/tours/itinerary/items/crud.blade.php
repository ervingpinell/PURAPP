<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
  <h4>{{ __('m_tours.itinerary_item.ui.list_title') }}</h4>
  @can('create-itinerary-items')
  <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarItem">
    <i class="fas fa-plus"></i> {{ __('m_tours.itinerary_item.ui.add_item') }}
  </a>
  @endcan
</div>

<div class="table-responsive">
  <table class="table table-bordered table-striped table-hover align-middle">
    <thead class="bg-secondary text-white">
      <tr>
        <th>#</th>
        <th>{{ __('m_tours.itinerary_item.fields.title') }}</th>
        <th>{{ __('m_tours.itinerary_item.fields.description') }}</th>
        <th>{{ __('m_tours.itinerary_item.ui.state') }}</th>
        <th class="text-nowrap">{{ __('m_tours.itinerary_item.ui.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
      @php
      $active = (bool) $item->is_active;
      $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';
      $titleTg = $active ? __('m_tours.itinerary_item.ui.toggle_off') : __('m_tours.itinerary_item.ui.toggle_on');
      @endphp
      <tr>
        <td class="text-nowrap">{{ $item->item_id }}</td>
        <td class="fw-semibold">{{ $item->title }}</td>
        <td>
          <div class="desc-wrapper">
            <div class="desc-truncate" id="desc-{{ $item->item_id }}">
              {{ $item->description }}
            </div>
            <button type="button" class="btn-toggle-desc" data-target="desc-{{ $item->item_id }}">
              {{ __('m_tours.itinerary_item.ui.see_more') }}
            </button>
          </div>
        </td>
        <td class="text-nowrap">
          @if ($active)
          <span class="badge bg-success">{{ __('m_tours.itinerary_item.status.active') }}</span>
          @else
          <span class="badge bg-secondary">{{ __('m_tours.itinerary_item.status.inactive') }}</span>
          @endif
        </td>
        <td class="text-nowrap">
          {{-- Editar --}}
          @can('edit-itinerary-items')
          <a href="#" class="btn btn-edit btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#modalEditarItem{{ $item->item_id }}"
            title="{{ __('m_tours.itinerary_item.ui.edit_item') }}">
            <i class="fas fa-edit"></i>
          </a>
          @endcan

          {{-- Toggle (PATCH) --}}
          {{-- Toggle (PATCH) --}}
          @can('publish-itinerary-items')
          <form action="{{ route('admin.tours.itinerary_items.toggle', $item->item_id) }}"
            method="POST"
            class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="btn btn-sm {{ $active ? 'btn-toggle' : 'btn-secondary' }}"
              title="{{ $titleTg }}">
              <i class="fas {{ $icon }}"></i>
            </button>
          </form>
          @endcan

          {{-- Delete (DELETE definitivo) + SweetAlert --}}
          {{-- Delete (DELETE definitivo) + SweetAlert --}}
          @can('delete-itinerary-items')
          <form action="{{ route('admin.tours.itinerary_items.destroy', $item->item_id) }}"
            method="POST"
            class="d-inline form-delete-item"
            data-label="{{ $item->title }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.itinerary_item.ui.delete_forever') }}">
              <i class="fas fa-trash"></i>
            </button>
          </form>
          @endcan
        </td>
      </tr>

      {{-- Modal editar ítem con pestañas de traducción --}}
      <div class="modal fade" id="modalEditarItem{{ $item->item_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <form action="{{ route('admin.tours.itinerary_items.updateTranslations', $item->item_id) }}"
            method="POST"
            class="form-edit-item-translations">
            @csrf
            @method('PUT')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{{ __('m_tours.itinerary_item.ui.edit_item') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <!-- Tabs de idiomas -->
                <ul class="nav nav-tabs mb-3" id="itemTabs{{ $item->item_id }}" role="tablist">
                  @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']) as $index => $locale)
                  <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                      id="tab-{{ $locale }}-item-{{ $item->item_id }}"
                      data-bs-toggle="tab"
                      data-bs-target="#content-{{ $locale }}-item-{{ $item->item_id }}"
                      type="button"
                      role="tab">
                      {{ strtoupper($locale) }}
                      @if($locale === 'es')
                      <span class="text-danger">*</span>
                      @endif
                    </button>
                  </li>
                  @endforeach
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="itemTabContent{{ $item->item_id }}">
                  @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']) as $index => $locale)
                  @php
                  $translation = $item->translations->where('locale', $locale)->first();
                  @endphp
                  <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                    id="content-{{ $locale }}-item-{{ $item->item_id }}"
                    role="tabpanel">

                    <div class="mb-3">
                      <label for="item-title-{{ $locale }}-{{ $item->item_id }}" class="form-label">
                        {{ __('m_tours.itinerary_item.fields.title') }}
                        @if($locale === 'es')
                        <span class="text-danger">*</span>
                        @endif
                      </label>
                      <input type="text"
                        name="translations[{{ $locale }}][title]"
                        id="item-title-{{ $locale }}-{{ $item->item_id }}"
                        class="form-control"
                        value="{{ $translation->title ?? '' }}"
                        {{ $locale === 'es' ? 'required' : '' }}
                        maxlength="255">
                    </div>

                    <div class="mb-3">
                      <label for="item-desc-{{ $locale }}-{{ $item->item_id }}" class="form-label">
                        {{ __('m_tours.itinerary_item.fields.description') }}
                      </label>
                      <textarea name="translations[{{ $locale }}][description]"
                        id="item-desc-{{ $locale }}-{{ $item->item_id }}"
                        class="form-control"
                        rows="4"
                        maxlength="1000">{{ $translation->description ?? '' }}</textarea>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  {{ __('m_tours.itinerary_item.ui.cancel') }}
                </button>
                <button type="submit" class="btn btn-warning">
                  {{ __('m_tours.itinerary_item.ui.update') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Modal registrar ítem (solo español) --}}
<div class="modal fade" id="modalRegistrarItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.tours.itinerary_items.store') }}"
      method="POST"
      class="form-create-item">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.itinerary_item.ui.register_item') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.itinerary_item.fields.title') }}</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('m_tours.itinerary_item.fields.description') }}</label>
            <textarea name="description" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ __('m_tours.itinerary_item.ui.save') }}</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.itinerary_item.ui.cancel') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('css')
<style>
  .desc-wrapper {
    max-width: 240px;
    min-width: 100px;
    word-wrap: break-word;
  }

  .desc-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    max-height: 2em;
    transition: all 0.3s ease;
    word-break: break-word;
    white-space: normal;
  }

  .desc-expanded {
    -webkit-line-clamp: unset !important;
    max-height: none !important;
  }

  .btn-toggle-desc {
    font-size: 0.75rem;
    color: #007bff;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .desc-wrapper {
      max-width: 140px;
    }

    .desc-truncate {
      font-size: 0.8rem;
    }

    .btn-toggle-desc {
      font-size: 0.7rem;
    }
  }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Toggle "ver más / ver menos"
  document.querySelectorAll('.btn-toggle-desc').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const el = document.getElementById(id);
      el.classList.toggle('desc-expanded');
      btn.textContent = el.classList.contains('desc-expanded') ?
        @json(__('m_tours.itinerary_item.ui.see_less')) :
        @json(__('m_tours.itinerary_item.ui.see_more'));
    });
  });

  // SweetAlert para DELETE
  document.querySelectorAll('.form-delete-item').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const label = form.getAttribute('data-label') || @json(__('m_tours.itinerary_item.ui.item_this'));
      Swal.fire({
        title: @json(__('m_tours.itinerary_item.ui.delete_confirm_title')),
        html: @json(__('m_tours.itinerary_item.ui.delete_confirm_html', ['label' => ':label'])).replace(':label', label),
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.itinerary_item.ui.yes_delete'))
      }).then(r => {
        if (r.isConfirmed) {
          const btn = form.querySelector('button[type="submit"]');
          if (btn) {
            btn.disabled = true;
            btn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
              @json(__('m_tours.itinerary_item.ui.deleting'));
          }
          form.submit();
        }
      });
    });
  });
</script>
@endpush