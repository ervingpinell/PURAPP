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

    <table class="table table-bordered">
        <thead class="table-dark">
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
                    <td>
                        <div class="faq-answer position-relative" style="max-height: 3.5em; overflow: hidden;" data-expanded="false">
                            <div class="answer-content">{{ strip_tags($faq->answer) }}</div>
                            <div class="fade-overlay position-absolute bottom-0 start-0 w-100" style="height: 1.5em; background: linear-gradient(to bottom, transparent, white);"></div>
                        </div>
                        <button class="btn btn-link p-0 mt-1 toggle-answer d-none" style="font-size: 0.85em;">Leer más</button>
                    </td>
                    <td>
                        <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                            {{ $faq->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                            data-bs-target="#editFaqModal{{ $faq->id }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('admin.faqs.toggleStatus', $faq) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="fas fa-toggle-{{ $faq->is_active ? 'off' : 'on' }}"></i>
                            </button>
                        </form>

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

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.faq-answer').forEach(container => {
      const content = container.querySelector('.answer-content');
      const toggleBtn = container.parentElement.querySelector('.toggle-answer');
      const fadeOverlay = container.querySelector('.fade-overlay');

      // Verifica si el contenido sobrepasa el contenedor
      const contentHeight = content.scrollHeight;
      const maxHeight = parseFloat(getComputedStyle(container).maxHeight);

      if (contentHeight > maxHeight + 5) {
        toggleBtn.classList.remove('d-none');
      }

      toggleBtn.addEventListener('click', function () {
        const isExpanded = container.getAttribute('data-expanded') === 'true';
        container.style.maxHeight = isExpanded ? '3.5em' : 'none';
        fadeOverlay.style.display = isExpanded ? '' : 'none';
        container.setAttribute('data-expanded', !isExpanded);
        this.textContent = isExpanded ? 'Leer más' : 'Leer menos';
      });
    });
  });
</script>
@endpush
