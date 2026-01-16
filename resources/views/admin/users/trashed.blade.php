@extends('adminlte::page')

@section('title', 'Usuarios Eliminados')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="mb-0">Usuarios Eliminados</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver a Usuarios
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if($users->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No hay usuarios eliminados.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Eliminado el</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles->isNotEmpty())
                            <span class="badge bg-primary">{{ $user->roles->first()->name }}</span>
                            @else
                            <span class="badge bg-secondary">Sin rol</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $user->deleted_at->format('d/m/Y H:i') }}
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @can('restore-users')
                                <button type="button" class="btn btn-success restore-user-btn"
                                    data-url="{{ route('admin.users.restore', $user->user_id) }}"
                                    data-name="{{ $user->full_name }}"
                                    title="Restaurar">
                                    <i class="fas fa-undo"></i>
                                </button>
                                @endcan
                                @can('hard-delete-users')
                                <button type="button" class="btn btn-danger force-delete-btn"
                                    data-url="{{ route('admin.users.forceDelete', $user->user_id) }}"
                                    data-name="{{ $user->full_name }}"
                                    title="Eliminar Permanentemente">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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

<!-- Formulario oculto para restaurar -->
<form id="restoreForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<!-- Formulario oculto para eliminar permanentemente -->
<form id="forceDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirm = (title, text) => {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            });
        };

        // Restaurar usuario
        document.querySelectorAll('.restore-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                const name = this.dataset.name;
                confirm('¿Restaurar usuario?', `${name} será restaurado y volverá a estar activo.`)
                    .then(r => {
                        if (r.isConfirmed) {
                            document.getElementById('restoreForm').action = url;
                            document.getElementById('restoreForm').submit();
                        }
                    });
            });
        });

        // Eliminar permanentemente
        document.querySelectorAll('.force-delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                const name = this.dataset.name;
                Swal.fire({
                    title: '¿Eliminar permanentemente?',
                    html: `<strong>${name}</strong> será eliminado de forma <strong>PERMANENTE</strong>.<br><br>Esta acción <strong>NO SE PUEDE DESHACER</strong>.`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar permanentemente',
                    cancelButtonText: 'Cancelar'
                }).then(r => {
                    if (r.isConfirmed) {
                        document.getElementById('forceDeleteForm').action = url;
                        document.getElementById('forceDeleteForm').submit();
                    }
                });
            });
        });

        // Flash messages
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: @json(session('success'))
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error'))
        });
        @endif
    });
</script>
@stop