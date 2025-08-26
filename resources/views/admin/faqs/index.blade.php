@extends('adminlte::page')

@section('title', __('faq.title'))

@section('content_header')
    <h1><i class="fas fa-question-circle"></i> {{ __('faq.title') }}</h1>
@stop

@section('content')
    <!-- Botón Crear -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createFaqModal">
        <i class="fas fa-plus"></i> {{ __('faq.new') }}
    </button>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>{{ __('faq.question') }}</th>
                <th>{{ __('faq.answer') }}</th>
                <th>{{ __('faq.status') }}</th>
                <th style="width: 160px;">{{ __('faq.actions') }}</th>
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
                        <button class="btn btn-link p-0 mt-1 toggle-answer d-none" style="font-size: 0.85em;">
                            {{ __('faq.read_more') }}
                        </button>
                    </td>
                    <td>
                        <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                            {{ $faq->is_active ? __('faq.active') : __('faq.inactive') }}
                        </span>
                    </td>
                    <td>
                        @php
                          $qAttr = e(strip_tags($faq->question));
                          $aAttr = e(str_replace(["\r", "\n"], ' ', strip_tags($faq->answer)));
                        @endphp

                        <!-- EDITAR -->
                        <button
                          class="btn btn-sm btn-edit"
                          data-bs-toggle="modal"
                          data-bs-target="#editFaqModal"
                          data-id="{{ $faq->faq_id ?? $faq->id }}"
                          data-action="{{ route('admin.faqs.update', $faq) }}"
                          data-question="{{ $qAttr }}"
                          data-answer="{{ $aAttr }}"
                          title="{{ __('faq.edit') }}"
                        >
                          <i class="fas fa-edit"></i>
                        </button>

                        <!-- TOGGLE -->
                        <form action="{{ route('admin.faqs.toggleStatus', $faq) }}" method="POST"
                              class="d-inline js-confirm-toggle" data-active="{{ $faq->is_active ? 1 : 0 }}">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $faq->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                                    title="{{ $faq->is_active ? __('faq.deactivate') : __('faq.activate') }}" data-bs-toggle="tooltip">
                                <i class="fas fa-toggle-{{ $faq->is_active ? 'on' : 'off' }}"></i>
                            </button>
                        </form>

                        <!-- ELIMINAR -->
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST"
                              class="d-inline js-confirm-delete" data-message="{{ __('faq.confirm_delete') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('faq.delete') }}" data-bs-toggle="tooltip">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal CREAR -->
    <div class="modal fade" id="createFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.faqs.store') }}" class="js-confirm-create">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('faq.new') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('faq.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('faq.question') }}</label>
                            <input type="text" name="question" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('faq.answer') }}</label>
                            <textarea name="answer" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('faq.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('faq.create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal EDITAR (reutilizable) -->
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFaqForm" method="POST" action="#" class="js-confirm-edit">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('faq.edit') }} {{ __('faq.question') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('faq.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('faq.question') }}</label>
                            <input id="editQuestion" type="text" name="question" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('faq.answer') }}</label>
                            <textarea id="editAnswer" name="answer" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('faq.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('faq.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
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
      title: @json(__('faq.error_title')),
      text: @json(__(session('error')))
    });
  </script>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Tooltips (si usas Bootstrap 5 a través del layout)
  if (window.bootstrap && bootstrap.Tooltip) {
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));
  }

  // Limpieza de backdrops sobrantes
  document.addEventListener('hidden.bs.modal', () => {
    const backs = document.querySelectorAll('.modal-backdrop');
    if (backs.length > 1) backs.forEach((b,i) => { if (i < backs.length-1) b.remove(); });
  });

  // Validaciones (errores) como modal
  const valErrors = @json($errors->any() ? $errors->all() : []);
  if (valErrors && valErrors.length) {
    const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
    Swal.fire({
      icon: 'warning',
      title: @json(__('faq.validation_errors')),
      html: list,
      confirmButtonText: @json(__('faq.ok')),
    });
  }

  // Confirmación CREAR
  document.querySelectorAll('.js-confirm-create').forEach(form => {
    form.addEventListener('submit', (ev) => {
      ev.preventDefault();
      Swal.fire({
        title: @json(__('faq.confirm_create')),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('faq.create')),
        cancelButtonText: @json(__('faq.cancel')),
      }).then((res) => { if (res.isConfirmed) form.submit(); });
    });
  });

  // Confirmación EDITAR
  document.querySelectorAll('.js-confirm-edit').forEach(form => {
    form.addEventListener('submit', (ev) => {
      ev.preventDefault();
      Swal.fire({
        title: @json(__('faq.confirm_edit')),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('faq.edit')),
        cancelButtonText: @json(__('faq.cancel')),
      }).then((res) => { if (res.isConfirmed) form.submit(); });
    });
  });

  // Confirmación ELIMINAR
  document.querySelectorAll('.js-confirm-delete').forEach(form => {
    form.addEventListener('submit', (ev) => {
      ev.preventDefault();
      const msg = form.dataset.message || @json(__('faq.confirm_delete'));
      Swal.fire({
        title: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('faq.delete')),
        cancelButtonText: @json(__('faq.cancel')),
      }).then((res) => { if (res.isConfirmed) form.submit(); });
    });
  });

  // Confirmación ACTIVAR / DESACTIVAR
  document.querySelectorAll('.js-confirm-toggle').forEach(form => {
    form.addEventListener('submit', (ev) => {
      ev.preventDefault();
      const isActive = form.dataset.active === '1';
      Swal.fire({
        title: isActive ? @json(__('faq.confirm_deactivate')) : @json(__('faq.confirm_activate')),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#d33' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: isActive ? @json(__('faq.deactivate')) : @json(__('faq.activate')),
        cancelButtonText: @json(__('faq.cancel')),
      }).then((res) => { if (res.isConfirmed) form.submit(); });
    });
  });

  // Leer más / Leer menos
  document.querySelectorAll('.faq-answer').forEach(container => {
    const content = container.querySelector('.answer-content');
    const toggleBtn = container.parentElement.querySelector('.toggle-answer');
    const fadeOverlay = container.querySelector('.fade-overlay');
    if (!content || !toggleBtn) return;

    const contentHeight = content.scrollHeight;
    const maxHeight = parseFloat(getComputedStyle(container).maxHeight);
    if (contentHeight > maxHeight + 5) toggleBtn.classList.remove('d-none');

    toggleBtn.addEventListener('click', function () {
      const isExpanded = container.getAttribute('data-expanded') === 'true';
      container.style.maxHeight = isExpanded ? '3.5em' : 'none';
      if (fadeOverlay) fadeOverlay.style.display = isExpanded ? '' : 'none';
      container.setAttribute('data-expanded', (!isExpanded).toString());
      this.textContent = isExpanded ? @json(__('faq.read_more')) : @json(__('faq.read_less'));
    });
  });

  // Rellenar modal de EDICIÓN
  const editModal = document.getElementById('editFaqModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const action   = button.getAttribute('data-action')   || '#';
      const question = button.getAttribute('data-question') || '';
      const answer   = button.getAttribute('data-answer')   || '';

      const form = document.getElementById('editFaqForm');
      const qInp = document.getElementById('editQuestion');
      const aTxt = document.getElementById('editAnswer');

      if (form && qInp && aTxt) {
        form.action = action;
        qInp.value  = question;
        aTxt.value  = answer;
      }
    });
  }
});
</script>
@endpush
