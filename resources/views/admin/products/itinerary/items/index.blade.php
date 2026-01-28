@extends('adminlte::page')

@section('title', __('menu.itinerary_items'))

@section('content_header')
    <h1>{{ __('menu.itinerary_items') }}</h1>
@stop

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

@section('content')

<div class="row mb-3">
    <div class="col-12">
        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.products.itinerary_items.index') }}">
                    <i class="fas fa-list"></i> {{ __('m_tours.common.active') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.products.itinerary_items.trash') }}">
                    <i class="fas fa-trash"></i> {{ __('m_tours.itinerary.ui.trash_title') }}
                </a>
            </li>
        </ul>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card mb-3">
    <div class="card-body">
         <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <form action="{{ route('admin.products.itinerary_items.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="{{ __('m_tours.common.search') }}..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.products.itinerary_items.index') }}" class="btn btn-outline-secondary ms-2" title="{{ __('m_tours.common.clear_search') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                @can('create-itinerary-items')
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarItem">
                    <i class="fas fa-plus"></i> {{ __('m_tours.itinerary_item.ui.add_item') }}
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive d-none d-md-block">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th>#</th>
                <th>{{ __('m_tours.itinerary_item.fields.title') }}</th>
                <th>{{ __('m_tours.itinerary_item.fields.description') }}</th>
                <th>{{ __('m_tours.itinerary_item.ui.state') }}</th>
                <th class="text-nowrap" style="width: 150px;">{{ __('m_tours.itinerary_item.ui.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($items as $item)
              @php
              $active = (bool) $item->is_active;
              $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';
              $titleTg = $active ? __('m_tours.itinerary_item.ui.toggle_off') : __('m_tours.itinerary_item.ui.toggle_on');
              @endphp
              <tr>
                <td class="text-nowrap ps-3">{{ $item->item_id }}</td>
                <td class="fw-semibold">{{ $item->title }}</td>
                <td>
                  <div class="desc-wrapper">
                    <div class="desc-truncate" id="desc-{{ $item->item_id }}">
                      {{ $item->description }}
                    </div>
                    @if(strlen($item->description) > 50)
                    <button type="button" class="btn-toggle-desc" data-target="desc-{{ $item->item_id }}">
                      {{ __('m_tours.itinerary_item.ui.see_more') }}
                    </button>
                    @endif
                  </div>
                </td>
                <td class="text-nowrap">
                  @if ($active)
                  <span class="badge bg-success">{{ __('m_tours.itinerary_item.status.active') }}</span>
                  @else
                  <span class="badge bg-secondary">{{ __('m_tours.itinerary_item.status.inactive') }}</span>
                  @endif
                </td>
                <td class="text-nowrap pe-3">
                  {{-- Editar --}}
                  @can('edit-itinerary-items')
                  <a href="#" class="btn btn-edit btn-sm"
                    data-toggle="modal"
                    data-target="#modalEditarItem{{ $item->item_id }}"
                    title="{{ __('m_tours.itinerary_item.ui.edit_item') }}">
                    <i class="fas fa-edit"></i>
                  </a>
                  @endcan
        
                  {{-- Toggle (PATCH) --}}
                  @can('publish-itinerary-items')
                  <form action="{{ route('admin.products.itinerary_items.toggle', $item->item_id) }}"
                    method="POST"
                    class="d-inline form-toggle-item"
                    data-label="{{ $item->title }}"
                    data-active="{{ $active ? 1 : 0 }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                      class="btn btn-sm {{ $active ? 'btn-toggle' : 'btn-secondary' }}"
                      title="{{ $titleTg }}">
                      <i class="fas {{ $icon }}"></i>
                    </button>
                  </form>
                  @endcan
        
                  {{-- Delete (Soft Delete via destroy) + SweetAlert --}}
                  @can('delete-itinerary-items')
                  <form action="{{ route('admin.products.itinerary_items.destroy', $item->item_id) }}"
                    method="POST"
                    class="d-inline form-delete-item"
                    data-label="{{ $item->title }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_tours.common.delete') }}">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  @endcan
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    {{ __('m_tours.itinerary_item.ui.list_empty') }}
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        {{-- Paginación Desktop --}}
        <div class="d-none d-md-block px-3 py-3">
          {{ $items->links() }}
        </div>
        
        {{-- Vista Móvil (Tarjetas) --}}
        <div class="d-md-none p-3">
          @forelse ($items as $item)
          @php
          $active = (bool) $item->is_active;
          $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';
          $titleTg = $active ? __('m_tours.itinerary_item.ui.toggle_off') : __('m_tours.itinerary_item.ui.toggle_on');
          @endphp
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <span class="badge bg-light text-dark border me-1">#{{ $item->item_id }}</span>
                  <h5 class="d-inline-block fw-bold mb-0">{{ $item->title }}</h5>
                </div>
                <div>
                  @if ($active)
                  <span class="badge bg-success">{{ __('m_tours.itinerary_item.status.active') }}</span>
                  @else
                  <span class="badge bg-secondary">{{ __('m_tours.itinerary_item.status.inactive') }}</span>
                  @endif
                </div>
              </div>
        
              <div class="mb-3 text-muted">
                <div class="desc-wrapper w-100" style="max-width: 100%;">
                  <div class="desc-truncate" id="desc-mobile-{{ $item->item_id }}">
                    {{ $item->description }}
                  </div>
                  @if(strlen($item->description) > 50)
                  <button type="button" class="btn-toggle-desc" data-target="desc-mobile-{{ $item->item_id }}">
                    {{ __('m_tours.itinerary_item.ui.see_more') }}
                  </button>
                  @endif
                </div>
              </div>
        
              <div class="d-flex justify-content-end gap-2">
                {{-- Editar --}}
                @can('edit-itinerary-items')
                <button type="button" class="btn btn-primary btn-sm"
                  data-toggle="modal"
                  data-target="#modalEditarItem{{ $item->item_id }}">
                  <i class="fas fa-edit me-1"></i> {{ __('m_tours.itinerary_item.ui.edit_item') }}
                </button>
                @endcan
        
                {{-- Toggle --}}
                @can('publish-itinerary-items')
                <form action="{{ route('admin.products.itinerary_items.toggle', $item->item_id) }}"
                  method="POST"
                  class="d-inline form-toggle-item"
                  data-label="{{ $item->title }}"
                  data-active="{{ $active ? 1 : 0 }}">
                  @csrf @method('PATCH')
                  <button type="submit"
                    class="btn btn-sm {{ $active ? 'btn-warning' : 'btn-secondary' }}"
                    title="{{ $titleTg }}">
                    <i class="fas {{ $icon }}"></i>
                  </button>
                </form>
                @endcan
        
                {{-- Delete --}}
                @can('delete-itinerary-items')
                <form action="{{ route('admin.products.itinerary_items.destroy', $item->item_id) }}"
                  method="POST"
                  class="d-inline form-delete-item"
                  data-label="{{ $item->title }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                @endcan
              </div>
            </div>
          </div>
          @empty
          <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-1"></i> {{ __('m_tours.itinerary_item.ui.list_empty') }}
          </div>
          @endforelse
        </div>
        
        {{-- Paginación Móvil --}}
        <div class="d-md-none px-3 pb-3">
          {{ $items->links() }}
        </div>
    </div>
</div>

{{-- Modals de Edición (Compartidos) --}}
@foreach ($items as $item)
<div class="modal fade" id="modalEditarItem{{ $item->item_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admin.products.itinerary_items.update', $item->item_id) }}"
      method="POST"
      class="form-edit-item-translations">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.itinerary_item.ui.edit_item') }}</h5>
          <button type="button" class="close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Tabs de idiomas -->
          <ul class="nav nav-tabs mb-3" id="itemTabs{{ $item->item_id }}" role="tablist">
            @foreach(config('app.supported_locales', ['es', 'en', 'fr', 'de', 'pt']) as $index => $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                id="tab-{{ $locale }}-item-{{ $item->item_id }}"
                data-toggle="tab"
                data-target="#content-{{ $locale }}-item-{{ $item->item_id }}"
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
            $tTitle = $item->getTranslation('title', $locale, false);
            $tDesc  = $item->getTranslation('description', $locale, false);
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
                  value="{{ $tTitle }}"
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
                  maxlength="1000">{{ $tDesc }}</textarea>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
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

{{-- Modal registrar ítem (solo español) --}}
<div class="modal fade" id="modalRegistrarItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.products.itinerary_items.store') }}"
      method="POST"
      class="form-create-item">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_tours.itinerary_item.ui.register_item') }}</h5>
          <button type="button" class="close" data-dismiss="modal"></button>
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
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_tours.itinerary_item.ui.cancel') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

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

  // SweetAlert para Toggle
  document.querySelectorAll('.form-toggle-item').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const label = form.getAttribute('data-label') || @json(__('m_tours.itinerary_item.ui.item_this'));
      const isActive = form.getAttribute('data-active') === '1';

      Swal.fire({
        title: isActive ?
          @json(__('m_tours.itinerary_item.ui.toggle_confirm_off_title')) : @json(__('m_tours.itinerary_item.ui.toggle_confirm_on_title')),
        html: (isActive ?
          @json(__('m_tours.itinerary_item.ui.toggle_confirm_off_html', ['label' => ':label'])) :
          @json(__('m_tours.itinerary_item.ui.toggle_confirm_on_html', ['label' => ':label']))
        ).replace(':label', label),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#ffc107' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('m_tours.itinerary_item.ui.yes_continue')),
        cancelButtonText: @json(__('m_tours.itinerary_item.ui.cancel'))
      }).then(r => {
        if (r.isConfirmed) {
          const btn = form.querySelector('button[type="submit"]');
          if (btn) {
            btn.disabled = true;
            btn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
              (isActive ? @json(__('m_tours.itinerary_item.ui.deactivating')) : @json(__('m_tours.itinerary_item.ui.activating')));
          }
          form.submit();
        }
      });
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