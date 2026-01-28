{{-- resources/views/admin/tours/amenities/trash.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_config.amenities.trash_title') ?? 'Papelera de Amenidades')

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h1 class="m-0">
        <i class="fas fa-trash me-2"></i>{{ __('m_config.amenities.trash_header') ?? 'Papelera de Amenidades' }}
    </h1>
    <a href="{{ route('admin.products.amenities.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('m_config.buttons.back') ?? 'Volver' }}
    </a>
</div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        @if($amenities->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>{{ __('m_config.amenities.trash_empty') ?? 'No hay amenidades eliminadas' }}
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Amenidad</th>
                        <th class="text-center">Eliminado por</th>
                        <th>Fecha de eliminación</th>
                        <th class="text-center">Auto-eliminación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($amenities as $amenity)
                    @php
                    $daysLeft = max(0, 30 - now()->diffInDays($amenity->deleted_at));
                    $aName = $amenity->name ?: 'Sin nombre'; // Accessor handles translation/fallback
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $aName }}</strong>
                        </td>
                        <td class="text-center">
                            @if($amenity->deletedBy)
                            <i class="fas fa-user-circle fa-2x text-primary"
                                data-toggle="tooltip"
                                data-bs-placement="top"
                                title="{{ $amenity->deletedBy->name }}"
                                style="cursor: help;">
                            </i>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $amenity->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $daysLeft <= 7 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $daysLeft }} {{ $daysLeft === 1 ? 'día' : 'días' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @can('restore-amenities')
                                <form action="{{ route('admin.products.amenities.restore', $amenity->amenity_id) }}" method="POST" class="d-inline restore-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('force-delete-amenities')
                                <form action="{{ route('admin.products.amenities.forceDelete', $amenity->amenity_id) }}" method="POST" class="d-inline force-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Eliminar permanentemente">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Restore confirmation
        document.querySelectorAll('.restore-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Restaurar amenidad?',
                    text: 'Volverá a estar disponible',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Force delete confirmation
        document.querySelectorAll('.force-delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar permanentemente?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Flash messages
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            timer: 3000
        });
        @endif
    });
</script>
@endpush