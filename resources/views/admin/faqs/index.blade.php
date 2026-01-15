@extends('adminlte::page')

@section('title', __('m_config.faq.title'))

@section('content_header')
<h1><i class="fas fa-question-circle"></i> {{ __('m_config.faq.title') }}</h1>
@stop

@section('content')
<div class="p-3 table-responsive">

  {{-- Tabs: Activos / Papelera --}}
  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" href="{{ route('admin.faqs.index') }}" role="tab">
        {{ __('m_config.faq.active_tab') }}
      </a>
    </li>
    @can('restore-faqs')
    <li class="nav-item" role="presentation">
      <a class="nav-link" href="{{ route('admin.faqs.trash') }}" role="tab">
        {{ __('m_config.faq.trash_tab') }}
        @if(isset($trashedCount) && $trashedCount > 0)
        <span class="badge badge-danger ml-1">{{ $trashedCount }}</span>
        @endif
      </a>
    </li>
    @endcan
  </ul>

  {{-- Botón Crear --}}
  @can('create-faqs')
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createFaqModal">
    <i class="fas fa-plus"></i> {{ __('m_config.faq.new') }}
  </button>
  @endcan

  <table class="table table-bordered table-striped table-hover align-middle">
    <thead class="bg-primary text-white">
      <tr>
        <th>{{ __('m_config.faq.question') }}</th>
        <th>{{ __('m_config.faq.answer') }}</th>
        <th>{{ __('m_config.faq.status') }}</th>
        <th style="width: 160px;">{{ __('m_config.faq.actions') }}</th>
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
            {{ __('m_config.faq.read_more') }}
          </button>
        </td>
        <td>
          <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
            {{ $faq->is_active ? __('m_config.faq.active') : __('m_config.faq.inactive') }}
          </span>
        </td>
        <td>
          {{-- EDITAR --}}
          @can('edit-faqs')
          <button
            class="btn btn-edit btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#editFaqModal{{ $faq->faq_id }}"
            title="{{ __('m_config.faq.edit') }}">
            <i class="fas fa-edit"></i>
          </button>
          @endcan

          {{-- TOGGLE --}}
          @can('publish-faqs')
          <form action="{{ route('admin.faqs.toggleStatus', $faq) }}" method="POST"
            class="d-inline js-confirm-toggle" data-active="{{ $faq->is_active ? 1 : 0 }}">
            @csrf
            @method('PATCH')
            <button type="submit"
              class="btn btn-sm {{ $faq->is_active ? 'btn-toggle' : 'btn-secondary' }}"
              title="{{ $faq->is_active ? __('m_config.faq.deactivate') : __('m_config.faq.activate') }}">
              <i class="fas fa-toggle-{{ $faq->is_active ? 'on' : 'off' }}"></i>
            </button>
          </form>
          @endcan

          {{-- ELIMINAR (SOFT DELETE) --}}
          @can('delete-faqs')
          <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST"
            class="d-inline js-confirm-delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-delete" title="{{ __('m_config.faq.delete') }}">
              <i class="fas fa-trash"></i>
            </button>
          </form>
          @endcan
        </td>
      </tr>

      {{-- Modal EDITAR con tabs por idioma --}}
      <div class="modal fade" id="editFaqModal{{ $faq->faq_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{{ __('m_config.faq.edit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_config.faq.close') }}"></button>
              </div>
              <div class="modal-body">
                @php
                $locales = supported_locales();
                @endphp

                <ul class="nav nav-tabs mb-3" id="faqTabs{{ $faq->faq_id }}" role="tablist">
                  @foreach($locales as $locale)
                  <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                      id="tab-{{ $faq->faq_id }}-{{ $locale }}"
                      data-bs-toggle="tab"
                      data-bs-target="#content-{{ $faq->faq_id }}-{{ $locale }}"
                      type="button"
                      role="tab">
                      {{ strtoupper($locale) }}
                    </button>
                  </li>
                  @endforeach
                </ul>

                <div class="tab-content" id="faqTabsContent{{ $faq->faq_id }}">
                  @foreach($locales as $locale)
                  @php
                  $translation = $faq->translations->firstWhere('locale', $locale);
                  $questionValue = $translation ? $translation->question : ($locale === 'es' ? $faq->question : '');
                  $answerValue = $translation ? $translation->answer : ($locale === 'es' ? $faq->answer : '');
                  @endphp
                  <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                    id="content-{{ $faq->faq_id }}-{{ $locale }}"
                    role="tabpanel">
                    <div class="mb-3">
                      <label class="form-label">
                        {{ __('m_config.faq.question') }} ({{ strtoupper($locale) }})
                      </label>
                      <input type="text"
                        name="translations[{{ $locale }}][question]"
                        class="form-control"
                        value="{{ $questionValue }}"
                        required
                        maxlength="500">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">
                        {{ __('m_config.faq.answer') }} ({{ strtoupper($locale) }})
                      </label>
                      <textarea
                        name="translations[{{ $locale }}][answer]"
                        class="form-control"
                        rows="5"
                        required>{{ $answerValue }}</textarea>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  {{ __('m_config.faq.cancel') }}
                </button>
                <button type="submit" class="btn btn-warning">
                  {{ __('m_config.faq.save') }}
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

{{-- Modal CREAR --}}
<div class="modal fade" id="createFaqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.faqs.store') }}" class="js-confirm-create">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_config.faq.new') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('m_config.faq.close') }}"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('m_config.faq.question') }}</label>
            <input type="text" name="question" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('m_config.faq.answer') }}</label>
            <textarea name="answer" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_config.faq.close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('m_config.faq.create') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Éxito / Error --}}
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
    title: @json(__('m_config.faq.error_title')),
    text: @json(__(session('error')))
  });
</script>
@endif

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const valErrors = @json($errors -> any() ? $errors -> all() : []);
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('m_config.faq.validation_errors')),
        html: list,
        confirmButtonText: @json(__('m_config.faq.ok')),
      });
    }

    // Confirmación CREAR
    document.querySelectorAll('.js-confirm-create').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        Swal.fire({
          title: @json(__('m_config.faq.confirm_create')),
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.faq.create')),
          cancelButtonText: @json(__('m_config.faq.cancel')),
        }).then((res) => {
          if (res.isConfirmed) form.submit();
        });
      });
    });

    // Confirmación ELIMINAR
    document.querySelectorAll('.js-confirm-delete').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        Swal.fire({
          title: @json(__('m_config.faq.confirm_delete')),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: @json(__('m_config.faq.delete')),
          cancelButtonText: @json(__('m_config.faq.cancel')),
        }).then((res) => {
          if (res.isConfirmed) form.submit();
        });
      });
    });

    // Confirmación ACTIVAR / DESACTIVAR
    document.querySelectorAll('.js-confirm-toggle').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        const isActive = form.dataset.active === '1';
        Swal.fire({
          title: isActive ? @json(__('m_config.faq.confirm_deactivate')) : @json(__('m_config.faq.confirm_activate')),
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: isActive ? '#d33' : '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: isActive ? @json(__('m_config.faq.deactivate')) : @json(__('m_config.faq.activate')),
          cancelButtonText: @json(__('m_config.faq.cancel')),
        }).then((res) => {
          if (res.isConfirmed) form.submit();
        });
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

      toggleBtn.addEventListener('click', function() {
        const isExpanded = container.getAttribute('data-expanded') === 'true';
        container.style.maxHeight = isExpanded ? '3.5em' : 'none';
        if (fadeOverlay) fadeOverlay.style.display = isExpanded ? '' : 'none';
        container.setAttribute('data-expanded', (!isExpanded).toString());
        this.textContent = isExpanded ? @json(__('m_config.faq.read_more')) : @json(__('m_config.faq.read_less'));
      });
    });
  });
</script>
@stop
