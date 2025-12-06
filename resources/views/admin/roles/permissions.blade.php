@extends('adminlte::page')

@section('title', __('permissions.manage_permissions') . ': ' . $role->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="text-dark mb-2 mb-md-0">{{ __('permissions.manage_permissions') }}: <span class="text-primary">{{ $role->name }}</span></h1>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light btn-sm border-secondary text-secondary">
        <i class="fas fa-arrow-left mr-1"></i> {{ __('permissions.back_to_roles') }}
    </a>
</div>
@stop

@section('content')
<form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST" id="permissionsForm">
    @csrf
    @method('PUT')

    {{-- Top Action Bar --}}
    <div class="card card-outline card-secondary mb-4 shadow-sm">
        <div class="card-body p-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

                {{-- Presets Group --}}
                <div class="btn-group mb-3 mb-md-0 mr-md-3">
                    <button type="button" class="btn btn-app bg-dark border my-0 ml-0" onclick="applyPreset('auditor')">
                        <i class="fas fa-eye text-white"></i> {{ __('permissions.presets.auditor') }}
                        <div class="text-muted small" style="font-size: 0.7rem">{{ __('permissions.presets.auditor_desc') }}</div>
                    </button>
                    <button type="button" class="btn btn-app bg-dark border my-0" onclick="applyPreset('reservas')">
                        <i class="fas fa-calendar-check text-white"></i> {{ __('permissions.presets.reservations') }}
                        <div class="text-muted small" style="font-size: 0.7rem">{{ __('permissions.presets.reservations_desc') }}</div>
                    </button>
                </div>

                {{-- Global Actions --}}
                <div class="d-flex align-items-center flex-wrap justify-content-center">
                    <button type="button" class="btn btn-primary mr-2 mb-2 mb-md-0" id="btnSelectAllGlobal">
                        <i class="fas fa-check-double mr-1"></i> {{ __('permissions.select_all') }}
                    </button>
                    <button type="button" class="btn btn-danger mr-3 mb-2 mb-md-0" id="btnDeselectAllGlobal">
                        <i class="fas fa-times mr-1"></i> {{ __('permissions.deselect_all') }}
                    </button>

                    <button type="submit" class="btn btn-success font-weight-bold px-4 mb-2 mb-md-0">
                        <i class="fas fa-save mr-1"></i> {{ __('permissions.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @php
        // Definir iconos por módulo
        $icons = [
        'admin' => 'fas fa-shield-alt',
        'tours' => 'fas fa-map-marked-alt',
        'bookings' => 'fas fa-calendar-alt',
        'reviews' => 'fas fa-star',
        'reports' => 'fas fa-chart-line',
        'users' => 'fas fa-users',
        'roles' => 'fas fa-user-tag',
        'config' => 'fas fa-cogs',
        'payments' => 'fas fa-money-bill-wave',
        'amenities' => 'fas fa-umbrella-beach',
        'categories' => 'fas fa-layer-group',
        'policies' => 'fas fa-file-contract',
        'tour_images' => 'fas fa-images',
        'tour_pricing' => 'fas fa-tags',
        'default' => 'fas fa-cube'
        ];
        @endphp

        @foreach($permissionGroups as $module => $permissions)
        @php
        // Determinar icono
        $icon = $icons['default'];
        foreach($icons as $key => $val) {
        if(strpos($module, $key) !== false) {
        $icon = $val;
        break;
        }
        }
        @endphp

        <div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch mb-3">
            <div class="card card-dark card-outline w-100 shadow-sm mb-0" style="background-color: #343a40; color: white;">
                <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center py-2">
                    <h3 class="card-title text-capitalize font-weight-bold mb-0" style="font-size: 1.1rem;">
                        <i class="{{ $icon }} mr-2 text-light"></i>
                        {{ __('permissions.modules.' . $module) }}
                    </h3>

                    <div class="card-tools d-flex">
                        <button type="button" class="btn btn-xs btn-success btn-circle mr-1"
                            onclick="toggleGroup('{{ $module }}', true)"
                            title="{{ __('permissions.select_all') }}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger btn-circle"
                            onclick="toggleGroup('{{ $module }}', false)"
                            title="{{ __('permissions.deselect_all') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-2" style="background-color: #454d55;">
                    <ul class="list-unstyled mb-0 module-group" id="group_{{ $module }}">
                        @foreach($permissions as $perm)
                        @php
                        // Simplificar etiqueta: 'view-tours' -> 'Ver'
                        $parts = explode('-', $perm->name);
                        $actionKey = $parts[0];

                        // Casos especiales
                        if ($perm->name === 'access-admin') {
                        $label = __('permissions.actions.access'); // Acceder
                        } elseif (count($parts) > 0) {
                        $label = __('permissions.actions.' . $actionKey);
                        // Si no existe traducción, usar ucfirst
                        if (strpos($label, 'permissions.actions.') !== false) {
                        $label = ucfirst($actionKey);
                        }
                        } else {
                        $label = $perm->name;
                        }
                        @endphp

                        <li class="mb-2 p-2 rounded d-flex align-items-center justify-content-between permission-row"
                            style="background-color: rgba(255,255,255,0.05);">

                            <div class="custom-control custom-checkbox d-flex align-items-center w-100">
                                <input type="checkbox"
                                    class="custom-control-input permission-checkbox"
                                    id="perm_{{ $perm->id }}"
                                    name="permissions[]"
                                    value="{{ $perm->name }}"
                                    data-module="{{ $module }}"
                                    {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal cursor-pointer text-white w-100 ml-1"
                                    for="perm_{{ $perm->id }}" style="user-select: none;">
                                    {{ $label }}
                                    {{-- <small class="text-muted ml-1">({{ $perm->name }})</small> --}}
                                </label>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</form>
@stop

@section('css')
<style>
    .btn-circle {
        width: 24px;
        height: 24px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .permission-row:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Estilo para los botones de presets */
    .btn-app {
        height: auto;
        padding: 8px 15px;
        min-width: 100px;
        margin: 0 5px;
    }

    @media (max-width: 768px) {
        .btn-app {
            min-width: 80px;
            font-size: 0.9em;
        }
    }
</style>
@stop

@section('js')
<script>
    // --- Global Actions ---
    $('#btnSelectAllGlobal').click(function() {
        $('.permission-checkbox').prop('checked', true);
    });

    $('#btnDeselectAllGlobal').click(function() {
        $('.permission-checkbox').prop('checked', false);
    });

    // --- Group Actions ---
    function toggleGroup(moduleId, state) {
        // Escapar caracteres especiales en ID si es necesario, pero data-module es seguro
        // Usamos find dentro del contenedor
        $('#group_' + moduleId + ' .permission-checkbox').prop('checked', state);
    }

    // --- Presets Logic ---
    function applyPreset(type) {
        // Primero limpiar todo
        $('.permission-checkbox').prop('checked', false);

        if (type === 'auditor') {
            // Auditor: Solo permisos 'view-' y 'access-admin'
            $('.permission-checkbox').each(function() {
                const val = $(this).val();
                if (val.startsWith('view-') || val === 'access-admin') {
                    $(this).prop('checked', true);
                }
            });
            toastr.info("{{ __('permissions.messages.auditor_applied') }}");
        } else if (type === 'reservas') {
            // Reservas: Todo excepto DELETE y módulos sensibles

            const forbiddenModules = ['users', 'roles', 'config', 'review_providers', 'reports'];

            $('.permission-checkbox').each(function() {
                const $cb = $(this);
                const permName = $cb.val();
                const module = $cb.data('module');

                // Criterio 1: No delete
                const isDelete = /delete|destroy|purge|remove/i.test(permName);

                // Criterio 2: Modulo prohibido
                // Nota: admin -> access-admin se permite, pero admin tiene otros? No, solo access-admin usualmente.
                let isForbiddenModule = false;
                if (forbiddenModules.includes(module)) {
                    isForbiddenModule = true;
                }

                if (!isDelete && !isForbiddenModule) {
                    $cb.prop('checked', true);
                }
            });

            // Asegurar access-admin
            $('input[value="access-admin"]').prop('checked', true);

            toastr.info("{{ __('permissions.messages.reservations_applied') }}");
        }
    }
</script>
@stop