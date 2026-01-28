@extends('adminlte::page')

@section('title', __('m_tours.amenity.ui.page_title'))

@section('content_header')
<h1>{{ __('m_tours.amenity.ui.page_heading') }}</h1>
@stop

@section('content')
<div class="p-3 table-responsive">

    {{-- Tabs: Activos / Papelera --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="{{ route('admin.products.amenities.index') }}" role="tab">
                {{ __('m_config.amenities.active_tab') ?? 'Activos' }}
            </a>
        </li>
        @can('restore-amenities')
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{ route('admin.products.amenities.trash') }}" role="tab">
                {{ __('m_config.amenities.trash_tab') ?? 'Papelera' }}
                @if(isset($trashedCount) && $trashedCount > 0)
                <span class="badge badge-danger ml-1">{{ $trashedCount }}</span>
                @endif
            </a>
        </li>
        @endcan
    </ul>

    @can('create-amenities')
    <a href="#" class="btn btn-success mb-3" data-toggle="modal" data-target="#modalRegistrar">
        <i class="fas fa-plus"></i> {{ __('m_tours.amenity.ui.add') }}
    </a>
    @endcan

    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>{{ __('m_tours.amenity.fields.name') }}</th>
                <th>{{ __('m_tours.amenity.ui.state') }}</th>
                <th class="text-nowrap">{{ __('m_tours.amenity.ui.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($amenities as $amenity)
            @php
            $active = (bool) $amenity->is_active;
            $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';
            $toggleTt = $active ? __('m_tours.amenity.ui.toggle_off') : __('m_tours.amenity.ui.toggle_on');
            @endphp
            <tr>
                <td class="text-nowrap">{{ $amenity->amenity_id }}</td>
                <td class="fw-semibold">{{ $amenity->name }}</td>
                <td class="text-nowrap">
                    @if ($active)
                    <span class="badge bg-success">{{ __('m_tours.amenity.status.active') }}</span>
                    @else
                    <span class="badge bg-secondary">{{ __('m_tours.amenity.status.inactive') }}</span>
                    @endif
                </td>
                <td class="text-nowrap">
                    {{-- Editar --}}
                    @can('edit-amenities')
                    <a href="#" class="btn btn-edit btn-sm"
                        data-toggle="modal"
                        data-target="#modalEditar{{ $amenity->amenity_id }}"
                        title="{{ __('m_tours.amenity.ui.edit_title') }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    @endcan

                    {{-- Toggle (PATCH) --}}
                    @can('publish-amenities')
                    <form action="{{ route('admin.products.amenities.toggle', $amenity->amenity_id) }}"
                        method="POST"
                        class="d-inline form-toggle-amenity"
                        data-name="{{ $amenity->name }}"
                        data-active="{{ $active ? 1 : 0 }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn btn-sm {{ $active ? 'btn-toggle' : 'btn-secondary' }}"
                            title="{{ $toggleTt }}">
                            <i class="fas {{ $icon }}"></i>
                        </button>
                    </form>
                    @endcan

                    {{-- Eliminar definitivo --}}
                    @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
                    @can('soft-delete-amenities')
                    <form action="{{ route('admin.products.amenities.destroy', $amenity->amenity_id) }}"
                        method="POST"
                        class="d-inline form-delete-amenity"
                        data-name="{{ $amenity->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn btn-delete btn-sm"
                            title="{{ __('m_tours.amenity.ui.delete_forever') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                    @endif
                </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $amenity->amenity_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.products.amenities.update', $amenity->amenity_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('m_tours.amenity.ui.edit_title') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.amenity.ui.close') }}"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                @php
                                $locales = supported_locales();
                                $currentLocale = app()->getLocale();
                                @endphp

                                <ul class="nav nav-tabs mb-3" id="amenityTabs{{ $amenity->amenity_id }}" role="tablist">
                                    @foreach($locales as $locale)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="tab-{{ $amenity->amenity_id }}-{{ $locale }}"
                                            data-toggle="tab"
                                            data-target="#content-{{ $amenity->amenity_id }}-{{ $locale }}"
                                            type="button"
                                            role="tab">
                                            {{ strtoupper($locale) }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content" id="amenityTabsContent{{ $amenity->amenity_id }}">
                                    @foreach($locales as $locale)
                                    @php
                                    $value = $amenity->getTranslation('name', $locale, false);
                                    // If creating new translation for existing amenity, maybe default to Spanish value or empty?
                                    // User asked to edit manually, so empty if not exists is fine, or fallback to name attribute if it's the "main" one
                                    @endphp
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="content-{{ $amenity->amenity_id }}-{{ $locale }}"
                                        role="tabpanel">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ __('m_tours.amenity.fields.name') }} ({{ strtoupper($locale) }})
                                            </label>
                                            <input type="text"
                                                name="translations[{{ $locale }}]"
                                                class="form-control"
                                                value="{{ $value }}"
                                                
                                                maxlength="255">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('m_tours.amenity.ui.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    {{ __('m_tours.amenity.ui.update') }}
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

<!-- Modal registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.products.amenities.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_tours.amenity.ui.create_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_tours.amenity.ui.close') }}"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('m_tours.amenity.fields.name') }}</label>
                        <input type="text" name="name" class="form-control" required maxlength="255" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('m_tours.amenity.ui.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('m_tours.amenity.ui.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Toggle Amenidad (SweetAlert)
    document.querySelectorAll('.form-toggle-amenity').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = form.getAttribute('data-name') || @json(__('m_tours.amenity.ui.item_this'));
            const isActive = form.getAttribute('data-active') === '1';
            Swal.fire({
                title: isActive ? @json(__('m_tours.amenity.ui.toggle_confirm_off_title')) : @json(__('m_tours.amenity.ui.toggle_confirm_on_title')),
                html: (isActive ?
                    @json(__('m_tours.amenity.ui.toggle_confirm_off_html', ['label' => ':label'])) :
                    @json(__('m_tours.amenity.ui.toggle_confirm_on_html', ['label' => ':label']))
                ).replace(':label', name),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.amenity.ui.yes_continue'))
            }).then(r => {
                if (r.isConfirmed) form.submit();
            });
        });
    });

    // Eliminar Amenidad (SweetAlert)
    document.querySelectorAll('.form-delete-amenity').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = form.getAttribute('data-name') || @json(__('m_tours.amenity.ui.item_this'));
            Swal.fire({
                title: @json(__('m_tours.amenity.ui.delete_confirm_title')),
                html: @json(__('m_tours.amenity.ui.delete_confirm_html', ['label' => ':label'])).replace(':label', name),
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.amenity.ui.yes_delete'))
            }).then(r => {
                if (r.isConfirmed) form.submit();
            });
        });
    });

    // Flashes
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: @json(__('m_tours.common.success_title')),
        text: @json(session('success')),
        timer: 2200,
        showConfirmButton: false
    });
    @endif
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: @json(__('m_tours.common.error_title')),
        text: @json(session('error')),
        confirmButtonColor: '#d33'
    });
    @endif
    @if($errors -> has('name'))
    Swal.fire({
        icon: 'error',
        title: @json(__('m_tours.amenity.validation.name.title')),
        text: @json($errors -> first('name')),
        confirmButtonColor: '#d33'
    });
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalRegistrar'));
        modal.show();
    });
    @endif
</script>
@stop
