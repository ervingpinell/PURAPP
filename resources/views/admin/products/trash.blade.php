{{-- resources/views/admin/tours/trash.blade.php --}}
@extends('adminlte::page')

@section('title', 'Papelera de Tours')

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h1 class="m-0">
        <i class="fas fa-trash me-2"></i>Papelera de Tours
    </h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        @if($trashedTours->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>No hay tours eliminados
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Nombre del Tour</th>
                        <th class="text-center">Eliminado por</th>
                        <th>Fecha de eliminación</th>
                        <th class="text-center">Auto-eliminación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedTours as $product)
                    @php
                    $daysLeft = max(0, 30 - now()->diffInDays($product->deleted_at));
                    $productName = $product->getTranslatedName() ?: $product->name ?: 'Sin nombre';
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $productName }}</strong>
                            @if($product->slug)
                            <br><small class="text-muted">{{ $product->slug }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($product->deletedBy)
                            <i class="fas fa-user-circle fa-2x text-primary"
                                data-toggle="tooltip"
                                data-bs-placement="top"
                                title="{{ $product->deletedBy->name }}"
                                style="cursor: help;">
                            </i>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $product->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $daysLeft <= 7 ? 'bg-danger' : 'bg-warning' }}">
                                {{ $daysLeft }} {{ $daysLeft === 1 ? 'día' : 'días' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @can('restore-tours')
                                <form action="{{ route('admin.products.restore', $product->product_id) }}" method="POST" class="d-inline restore-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('force-delete-tours')
                                <form action="{{ route('admin.products.forceDelete', $product->product_id) }}" method="POST" class="d-inline force-delete-form">
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
                    title: '¿Restaurar tour?',
                    text: 'El tour volverá a estar disponible',
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
                    text: 'Esta acción no se puede deshacer. El tour será eliminado definitivamente.',
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