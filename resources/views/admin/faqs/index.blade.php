@extends('adminlte::page')

@section('title', 'Preguntas Frecuentes')

@section('content_header')
    <h1><i class="fas fa-question-circle"></i> Preguntas Frecuentes</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botón Crear -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createFaqModal">
        <i class="fas fa-plus"></i> Nueva Pregunta
    </button>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Pregunta</th>
                <th>Respuesta</th>
                <th>Estado</th>
                <th style="width: 160px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($faqs as $faq)
                <tr>
                    <td>{{ $faq->question }}</td>
                    <td>{{ Str::limit(strip_tags($faq->answer), 80) }}</td>
                    <td>
                        <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                            {{ $faq->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td>
                        <!-- Botón Editar -->
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                            data-bs-target="#editFaqModal{{ $faq->id }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <!-- Botón Activar/Desactivar -->
                        <form action="{{ route('admin.faqs.toggleStatus', $faq) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="fas fa-toggle-{{ $faq->is_active ? 'off' : 'on' }}"></i>
                            </button>
                        </form>

                        <!-- Botón Eliminar -->
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar esta pregunta?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Editar -->
                <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form class="modal-content" method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Pregunta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Pregunta</label>
                                    <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Respuesta</label>
                                    <textarea name="answer" class="form-control" rows="4" required>{{ $faq->answer }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Crear -->
    <div class="modal fade" id="createFaqModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.faqs.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Pregunta Frecuente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Pregunta</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Respuesta</label>
                        <textarea name="answer" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
@stop
