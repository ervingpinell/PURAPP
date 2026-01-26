@extends('adminlte::page')

@section('title', __('m_config.tourtypes.title'))

@section('content_header')
<h1><i class="fas fa-map-signs"></i> {{ __('m_config.tourtypes.title') }}</h1>
@stop

@section('content')
<div class="p-3 table-responsive">
    {{-- Tabs: Activos / Papelera --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="{{ route('admin.product-types.index') }}" role="tab">
                {{ __('m_config.tourtypes.active_tab') ?? 'Activos' }}
            </a>
        </li>
        @can('restore-tour-types')
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{ route('admin.product-types.trash') }}" role="tab">
                {{ __('m_config.tourtypes.trash_tab') ?? 'Papelera' }}
                @if(isset($trashedCount) && $trashedCount > 0)
                <span class="badge badge-danger ml-1">{{ $trashedCount }}</span>
                @endif
            </a>
        </li>
        @endcan
    </ul>

    <div class="d-flex flex-wrap gap-2 mb-3">
        @can('create-tour-types')
        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> {{ __('m_config.tourtypes.new') }}
        </a>
        @endcan

        {{-- Botón global para ordenar tours --}}
        @can('edit-tours')
        <a href="{{ route('admin.products.order.index') }}" class="btn btn-primary">
            <i class="fas fa-sort-amount-down"></i> Ordenar tours
        </a>
        @endcan
    </div>

    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-primary text-white">
            <tr>
                <th>{{ __('m_config.tourtypes.id') }}</th>
                <th>{{ __('m_config.tourtypes.name') }}</th>
                <th>{{ __('m_config.tourtypes.description') }}</th>
                <th>{{ __('m_config.tourtypes.duration') }}</th>
                <th>{{ __('m_config.tourtypes.status') }}</th>
                <th>{{ __('m_config.tourtypes.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tourTypes as $tourtype)
            <tr>
                <td>{{ $tourtype->tour_type_id }}</td>
                <td>
                    {{ $tourtype->getTranslatedName($currentLocale) ?? $tourtype->name }}
                    {{-- Badges de idiomas disponibles --}}
                    @php
                    $availableLocales = $tourtype->getTranslations('name');
                    $availableLocales = array_keys($availableLocales);
                    @endphp
                    @if(count($availableLocales) > 0)
                    <div class="mt-1">
                        @foreach(['es' => 'ES', 'en' => 'EN', 'fr' => 'FR', 'pt' => 'PT', 'de' => 'DE'] as $locale => $label)
                        @if(in_array($locale, $availableLocales))
                        <span class="badge badge-success badge-sm" title="Traducción disponible en {{ $locale }}">
                            {{ $label }}
                        </span>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </td>
                <td>{{ $tourtype->getTranslation('description', $currentLocale) ?: $tourtype->description }}</td>
                <td>{{ $tourtype->getTranslation('duration', $currentLocale) ?: $tourtype->duration }}</td>
                <td>
                    @if ($tourtype->is_active)
                    <span class="badge bg-success">{{ __('m_config.tourtypes.active') }}</span>
                    @else
                    <span class="badge bg-secondary">{{ __('m_config.tourtypes.inactive') }}</span>
                    @endif
                </td>

                <td class="text-nowrap">
                    {{-- Editar --}}
                    @can('edit-tour-types')
                    <a href="#"
                        class="btn btn-edit btn-sm me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $tourtype->product_type_id }}"
                        title="{{ __('m_config.tourtypes.edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- (Botón Traducciones Eliminado) --}}

                    {{-- Activar/Desactivar (SweetAlert) --}}
                    <form action="{{ route('admin.product-types.toggle', $tourtype->product_type_id) }}"
                        method="POST"
                        class="d-inline me-1 js-confirm-toggle"
                        data-name="{{ $tourtype->name }}"
                        data-active="{{ $tourtype->is_active ? 1 : 0 }}">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="btn btn-sm {{ $tourtype->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                            title="{{ $tourtype->is_active ? __('m_config.tourtypes.deactivate') : __('m_config.tourtypes.activate') }}">
                            <i class="fas fa-toggle-{{ $tourtype->is_active ? 'on' : 'off' }}"></i>
                        </button>
                    </form>
                    @endcan

                    {{-- Ordenar tours de esta categoría --}}
                    @can('edit-tours')
                    <a href="{{ route('admin.products.order.index', ['tour_type_id' => $tourtype->product_type_id]) }}"
                        class="btn btn-sm btn-primary me-1"
                        title="Ordenar tours de «{{ $tourtype->name }}»">
                        <i class="fas fa-sort-amount-down"></i>
                    </a>
                    @endcan

                    {{-- Eliminar --}}
                    @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
                    @can('soft-delete-tour-types')
                    <form action="{{ route('admin.product-types.destroy', $tourtype->product_type_id) }}"
                        method="POST"
                        class="d-inline js-confirm-delete"
                        data-name="{{ $tourtype->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete btn-sm" title="{{ __('m_config.tourtypes.delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                    @endif
                </td>
            </tr>

            {{-- Modal editar --}}
            <div class="modal fade" id="modalEditar{{ $tourtype->product_type_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form action="{{ route('admin.product-types.update', $tourtype->product_type_id) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('m_config.tourtypes.edit_title') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Pestañas de Idiomas --}}
                                @php
                                    $locales = ['es', 'en', 'fr', 'pt', 'de'];
                                @endphp
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($locales as $i => $loc)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $i===0 ? 'active' : '' }}"
                                            id="edit-tab-{{ $tourtype->product_type_id }}-{{ $loc }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#edit-pane-{{ $tourtype->product_type_id }}-{{ $loc }}"
                                            type="button"
                                            role="tab">
                                            {{ strtoupper($loc) }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($locales as $i => $loc)
                                    @php
                                        // Spatie: getTranslation returns string directly
                                        $t_name = $tourtype->getTranslation('name', $loc, false);
                                        $t_desc = $tourtype->getTranslation('description', $loc, false);
                                        $t_dur  = $tourtype->getTranslation('duration', $loc, false);
                                    @endphp
                                    <div class="tab-pane fade {{ $i===0 ? 'show active' : '' }}" 
                                         id="edit-pane-{{ $tourtype->product_type_id }}-{{ $loc }}" 
                                         role="tabpanel">
                                        
                                        {{-- Nombre --}}
                                        <div class="mb-3">
                                            <label>{{ __('m_config.tourtypes.name') }} ({{ strtoupper($loc) }})</label>
                                            <input type="text"
                                                name="translations[{{ $loc }}][name]"
                                                class="form-control"
                                                value="{{ old("translations.$loc.name", $t_name) }}"
                                                placeholder="{{ __('m_config.tourtypes.examples_placeholder') }}">
                                        </div>

                                        {{-- Descripción --}}
                                        <div class="mb-3">
                                            <label>{{ __('m_config.tourtypes.description') }} ({{ strtoupper($loc) }})</label>
                                            <textarea
                                                name="translations[{{ $loc }}][description]"
                                                class="form-control"
                                                rows="3"
                                                placeholder="{{ __('m_config.tourtypes.description') }}">{{ old("translations.$loc.description", $t_desc) }}</textarea>
                                        </div>

                                        {{-- Duración --}}
                                        <div class="mb-3">
                                            <label>{{ __('m_config.tourtypes.duration') }} ({{ strtoupper($loc) }})</label>
                                            <input type="text"
                                                name="translations[{{ $loc }}][duration]"
                                                class="form-control"
                                                list="durationOptions-{{ $tourtype->product_type_id }}-{{ $loc }}"
                                                value="{{ old("translations.$loc.duration", $t_dur) }}"
                                                placeholder="{{ __('m_config.tourtypes.duration_placeholder') }}">
                                            <datalist id="durationOptions-{{ $tourtype->product_type_id }}-{{ $loc }}">
                                                <option value="4 horas"></option>
                                                <option value="6 horas"></option>
                                                <option value="8 horas"></option>
                                                <option value="10 horas"></option>
                                            </datalist>
                                        </div>

                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">{{ __('m_config.tourtypes.update') }}</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_config.tourtypes.cancel') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal registrar --}}
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.product-types.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('m_config.tourtypes.create_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('m_config.tourtypes.name') }}</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="{{ __('m_config.tourtypes.examples_placeholder') }}"
                            value="{{ old('name') }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_config.tourtypes.description') }}</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="3"
                            placeholder="{{ __('m_config.tourtypes.description') }} ({{ __('m_config.tourtypes.optional') ?? 'opcional' }})">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('m_config.tourtypes.duration') }}</label>
                        <input
                            type="text"
                            name="duration"
                            class="form-control"
                            list="durationOptionsCreate"
                            placeholder="{{ __('m_config.tourtypes.duration_placeholder') }}"
                            title="{{ __('m_config.tourtypes.suggested_duration_hint') }}"
                            value="{{ old('duration', '4 horas') }}">
                        <datalist id="durationOptionsCreate">
                            <option value="4 horas"></option>
                            <option value="6 horas"></option>
                            <option value="8 horas"></option>
                            <option value="10 horas"></option>
                        </datalist>
                        <small class="text-muted">{{ __('m_config.tourtypes.keep_default_hint') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">{{ __('m_config.tourtypes.register') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_config.tourtypes.cancel') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Éxito / Error (modal centrado, sin toast) --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: @json(__(session('success'))),
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: @json(__('m_config.tourtypes.error_title')),
        text: @json(__(session('error')))
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltips si el layout trae Bootstrap
        if (window.bootstrap && bootstrap.Tooltip) {
            [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));
        }

        // Eliminar backdrops sobrantes
        document.addEventListener('hidden.bs.modal', () => {
            const backs = document.querySelectorAll('.modal-backdrop');
            if (backs.length > 1) backs.forEach((b, i) => {
                if (i < backs.length - 1) b.remove();
            });
        });

        // Confirmación ELIMINAR
        document.querySelectorAll('.js-confirm-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.dataset.name || '';
                const tmpl = @json(__('m_config.tourtypes.confirm_delete', ['name' => ':name']));
                const text = tmpl.replace(':name', name);

                Swal.fire({
                    title: @json(__('m_config.tourtypes.delete')),
                    text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: @json(__('m_config.tourtypes.delete')),
                    cancelButtonText: @json(__('m_config.tourtypes.cancel'))
                }).then(res => {
                    if (res.isConfirmed) this.submit();
                });
            });
        });

        // Confirmación ACTIVAR / DESACTIVAR
        document.querySelectorAll('.js-confirm-toggle').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.dataset.name || '';
                const active = Number(this.dataset.active) === 1;

                const title = active ? @json(__('m_config.tourtypes.deactivate')) : @json(__('m_config.tourtypes.activate'));
                const tmpl = active ?
                    @json(__('m_config.tourtypes.confirm_deactivate', ['name' => ':name'])) :
                    @json(__('m_config.tourtypes.confirm_activate', ['name' => ':name']));
                const text = tmpl.replace(':name', name);

                Swal.fire({
                    icon: 'question',
                    title,
                    text,
                    showCancelButton: true,
                    confirmButtonColor: '#fd7e14',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: active ? @json(__('m_config.tourtypes.deactivate')) : @json(__('m_config.tourtypes.activate')),
                    cancelButtonText: @json(__('m_config.tourtypes.cancel'))
                }).then(res => {
                    if (res.isConfirmed) this.submit();
                });
            });
        });

        // Errores de validación => abrir modal correcto
        @if($errors -> any())
        const firstError = @json($errors -> first());
        Swal.fire({
            icon: 'warning',
            title: @json(__('m_config.tourtypes.validation_errors')),
            text: firstError || '',
            confirmButtonColor: '#d33'
        });

        @if(session('edit_modal'))
        const modalId = 'modalEditar{{ session('
        edit_modal ') }}';
        @else
        const modalId = 'modalRegistrar';
        @endif

        const modalEl = document.getElementById(modalId);
        if (modalEl && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
        @endif
    });
</script>
@stop
