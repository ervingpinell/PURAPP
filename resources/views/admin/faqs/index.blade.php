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

  <div class="d-flex align-items-center justify-content-between mb-3">
    {{-- Botón Crear --}}
    @can('create-faqs')
    <button class="btn btn-success" data-toggle="modal" data-target="#createFaqModal">
      <i class="fas fa-plus"></i> {{ __('m_config.faq.new') }}
    </button>
    @endcan

    {{-- Controles de Ordenamiento (Actions) --}}
    <div class="btn-group" role="group">
      {{-- Custom Order (Default) --}}
      <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary" title="Recargar orden actual (Arrastrar y Soltar para modificar)">
         <i class="fas fa-th-list"></i> Personalizado
      </a>
      
      <div class="btn-group" role="group">
        <button id="sortDropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-sort"></i> Aplicar orden...
        </button>
        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
            {{-- ID DESC (Newest First) --}}
            <li>
                <form action="{{ route('admin.faqs.reorderBulk') }}" method="POST" class="d-inline js-confirm-reorder">
                    @csrf
                    <input type="hidden" name="type" value="id">
                    <input type="hidden" name="direction" value="desc">
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-arrow-down"></i> Por ID (Más recientes)
                    </button>
                </form>
            </li>
            {{-- ID ASC (Oldest First) --}}
            <li>
                <form action="{{ route('admin.faqs.reorderBulk') }}" method="POST" class="d-inline js-confirm-reorder">
                    @csrf
                    <input type="hidden" name="type" value="id">
                    <input type="hidden" name="direction" value="asc">
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-arrow-up"></i> Por ID (Más antiguos)
                    </button>
                </form>
            </li>
            <li><hr class="dropdown-divider"></li>
            {{-- Alpha ASC (A-Z) --}}
            <li>
                <form action="{{ route('admin.faqs.reorderBulk') }}" method="POST" class="d-inline js-confirm-reorder">
                    @csrf
                    <input type="hidden" name="type" value="alpha">
                    <input type="hidden" name="direction" value="asc">
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sort-alpha-down"></i> Alfabético (A-Z)
                    </button>
                </form>
            </li>
            {{-- Alpha DESC (Z-A) --}}
            <li>
                <form action="{{ route('admin.faqs.reorderBulk') }}" method="POST" class="d-inline js-confirm-reorder">
                    @csrf
                    <input type="hidden" name="type" value="alpha">
                    <input type="hidden" name="direction" value="desc">
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sort-alpha-up"></i> Alfabético (Z-A)
                    </button>
                </form>
            </li>
        </ul>
      </div>
    </div>
  </div>

  <table class="table table-bordered table-striped table-hover align-middle">
    <thead class="bg-primary text-white">
      <tr>
        <th style="width: 50px;"></th> <!-- Drag Handle -->
        <th style="width: 60px;">ID</th>
        <th>{{ __('m_config.faq.question') }}</th>
        <th>{{ __('m_config.faq.answer') }}</th>
        <th>{{ __('m_config.faq.status') }}</th>
        <th style="width: 160px;">{{ __('m_config.faq.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($faqs as $faq)
      <tr data-id="{{ $faq->faq_id }}">
        <td class="align-middle text-center sort-handle" style="cursor: move;">
            <i class="fas fa-bars text-secondary"></i>
        </td>
        <td class="text-center font-monospace small">
            {{ $faq->faq_id }}
        </td>
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
            data-toggle="modal"
            data-target="#editFaqModal{{ $faq->faq_id }}"
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
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_config.faq.close') }}">
                  <span aria-hidden="true">&times;</span>
                </button>
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
                  // Spatie Translatable: get specific locale value
                  $questionValue = $faq->getTranslation('question', $locale, false);
                  $answerValue   = $faq->getTranslation('answer', $locale, false);
                  @endphp
                  <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                    id="content-{{ $faq->faq_id }}-{{ $locale }}"
                    role="tabpanel">
                    <div class="mb-3">
                      <label class="form-label">
                        {{ __('m_config.faq.question') }} ({{ strtoupper($locale) }})
                      </label>
                      <textarea
                        name="translations[{{ $locale }}][question]"
                        class="form-control"
                        rows="2"
                        required
                        maxlength="500">{{ $questionValue }}</textarea>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
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
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_config.faq.close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">{{ __('m_config.faq.question') }}</label>
            <textarea name="question" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('m_config.faq.answer') }}</label>
            <textarea name="answer" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_config.faq.close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('m_config.faq.create') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(($sortMode ?? 'order') === 'order')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endif

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
    // Sortable (Only if loaded and handle exists)
    const tbody = document.querySelector('tbody');
    const handle = document.querySelector('.sort-handle');
    
    if (tbody && handle && typeof Sortable !== 'undefined') {
        new Sortable(tbody, {
            handle: '.sort-handle',
            animation: 150,
            onEnd: function (evt) {
                const order = [];
                document.querySelectorAll('tbody tr').forEach(row => {
                    order.push(row.dataset.id);
                });

                fetch("{{ route('admin.faqs.reorder') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        toast.fire({
                            icon: 'success',
                            title: 'Orden actualizado'
                        });
                    } else {
                        Swal.fire('Error', 'No se pudo actualizar el orden', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Hubo un error de conexión', 'error');
                });
            }
        });
    }

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

    // Confirmación REORDENAR MASIVO
    document.querySelectorAll('.js-confirm-reorder').forEach(form => {
      form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        Swal.fire({
          title: '¿Estás seguro?',
          text: "Esto sobrescribirá el orden personalizado actual.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, aplicar orden',
          cancelButtonText: 'Cancelar'
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
