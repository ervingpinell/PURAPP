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
                            <div class="fade-overlay position-absolute bottom-0 start-0 w-100"
                                 style="height: 1.5em; background: linear-gradient(to bottom, transparent, white);"></div>
                        </div>
                        <button class="btn btn-link p-0 mt-1 toggle-answer d-none" style="font-size: 0.85em;">Leer más</button>
                    </td>
                    <td>
                        <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                            {{ $faq->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td>
                        <!-- EDITAR: modal único; pasar datos seguros por data-* -->
                        @php
                          $qAttr = e(strip_tags($faq->question));
                          $aAttr = e(str_replace(["\r", "\n"], ' ', strip_tags($faq->answer)));
                        @endphp
                        <button
                          class="btn btn-sm btn-edit"
                          data-bs-toggle="modal"
                          data-bs-target="#editFaqModal"
                          data-id="{{ $faq->id }}"
                          data-action="{{ route('admin.faqs.update', $faq) }}"
                          data-question="{{ $qAttr }}"
                          data-answer="{{ $aAttr }}"
                        >
                          <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('admin.faqs.toggleStatus', $faq) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Cambiar estado">
                                <i class="fas fa-toggle-{{ $faq->is_active ? 'off' : 'on' }}"></i>
                            </button>
                        </form>

                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta pregunta?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal CREAR (único) -->
    <div class="modal fade" id="createFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.faqs.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Pregunta Frecuente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pregunta</label>
                            <input type="text" name="question" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Respuesta</label>
                            <textarea name="answer" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR (único, reutilizable) -->
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFaqForm" method="POST" action="#">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pregunta</label>
                            <input id="editQuestion" type="text" name="question" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Respuesta</label>
                            <textarea id="editAnswer" name="answer" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
@verbatim
<script>
  // Leer más / Leer menos
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.faq-answer').forEach(container => {
      const content = container.querySelector('.answer-content');
      const toggleBtn = container.parentElement.querySelector('.toggle-answer');
      const fadeOverlay = container.querySelector('.fade-overlay');

      const contentHeight = content.scrollHeight;
      const maxHeight = parseFloat(getComputedStyle(container).maxHeight);

      if (contentHeight > maxHeight + 5) {
        toggleBtn.classList.remove('d-none');
      }

      toggleBtn.addEventListener('click', function () {
        const isExpanded = container.getAttribute('data-expanded') === 'true';
        container.style.maxHeight = isExpanded ? '3.5em' : 'none';
        fadeOverlay.style.display = isExpanded ? '' : 'none';
        container.setAttribute('data-expanded', (!isExpanded).toString());
        this.textContent = isExpanded ? 'Leer más' : 'Leer menos';
      });
    });
  });

  // Rellenar modal de EDICIÓN con los datos del botón que lo abrió
  const editModal = document.getElementById('editFaqModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button) return;

    const action   = button.getAttribute('data-action')   || '#';
    const question = button.getAttribute('data-question') || '';
    const answer   = button.getAttribute('data-answer')   || '';

    const form = document.getElementById('editFaqForm');
    const qInp = document.getElementById('editQuestion');
    const aTxt = document.getElementById('editAnswer');

    form.action = action;
    qInp.value  = question;
    aTxt.value  = answer;
  });
</script>
@endverbatim
@endpush
