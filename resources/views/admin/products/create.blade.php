{{-- resources/views/admin/tours/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.product.ui.create_title'))

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">{{ __('m_tours.product.ui.create_title') }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> {{ __('m_tours.product.ui.cancel') }}
    </a>
  </div>
@stop

@section('content')
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
      <strong class="d-block mb-1">{{ __('m_tours.common.form_errors_title') }}</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('m_tours.common.close') }}"></button>
    </div>
  @endif

  {{-- Stepper / timeline del wizard (paso 1: Detalles) --}}
  @include('admin.products.wizard.partials.stepper', [
      'currentStep' => 1,
      'product'        => null,
  ])

  {{-- Paso 1: Detalles del product (crea draft vía wizard) --}}
  <form action="{{ route('admin.products.wizard.store.details') }}"
        method="POST"
        id="tourWizardForm"
        novalidate>
    @csrf

    <div class="card card-primary card-outline mt-3">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i>
          {{ __('m_tours.product.fields.details') }}
        </h3>
      </div>

      <div class="card-body">
        {{-- Aquí usas el nuevo partial de detalles del wizard --}}
        @include('admin.products.wizard.steps.details', ['product' => null])
      </div>

      <div class="card-footer d-flex gap-2 justify-content-between">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
          <i class="fas fa-times"></i> {{ __('m_tours.product.ui.cancel') }}
        </a>

        <button type="submit" class="btn btn-success">
          <i class="fas fa-arrow-right"></i>
          {{ __('m_tours.product.wizard.next_step') }}
        </button>
      </div>
    </div>
  </form>

  {{-- ========================================
       MODAL PARA MANEJAR DRAFTS EXISTENTES
       ======================================== --}}
  @if(isset($existingDrafts) && $existingDrafts->count() > 0)
    @php
      // Usamos el borrador más reciente como "borrador principal"
      $mainDraft = $existingDrafts->sortByDesc('updated_at')->first();
    @endphp

    <div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header" style="background: #facc15; color:#111827;">
            <h5 class="modal-title" id="draftsModalLabel">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('m_tours.product.wizard.existing_drafts_title') }}
            </h5>
          </div>

          <div class="modal-body" style="background:#111827; color:#e5e7eb;">
            <p class="lead mb-3">
              {{ __('m_tours.product.wizard.existing_drafts_message', ['count' => $existingDrafts->count()]) }}
            </p>

            <div class="table-responsive">
              <table class="table table-hover table-sm mb-0" style="color:#e5e7eb;">
                <thead style="background:#1f2937; color:#e5e7eb;">
                  <tr>
                    <th>{{ __('m_tours.product.fields.name') }}</th>
                    <th>{{ __('m_tours.product.fields.type') }}</th>
                    <th class="text-center">{{ __('m_tours.product.wizard.current_step') }}</th>
                    <th>{{ __('m_tours.common.updated_at') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($existingDrafts as $draft)
                    <tr>
                      <td>
                        <strong>{{ $draft->name }}</strong><br>
                        <small class="text-muted">{{ $draft->slug }}</small>
                      </td>
                      <td>
                        @if($draft->productType)
                          <span class="badge bg-info">{{ $draft->productType->name }}</span>
                        @else
                          <span class="text-muted">{{ __('m_tours.common.not_set') }}</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <span class="badge bg-primary">
                          {{ __('m_tours.product.wizard.step') }} {{ $draft->current_step ?? 1 }}/6
                        </span>
                      </td>
                      <td>
                        <small>{{ $draft->updated_at->diffForHumans() }}</small><br>
                        <small class="text-muted">{{ $draft->updated_at->format('d/m/Y H:i') }}</small>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="alert alert-info mt-3 mb-0" style="background:#1d2433; border-color:#374151; color:#e5e7eb;">
              <i class="fas fa-info-circle"></i>
              {{ __('m_tours.product.wizard.drafts_info') }}
            </div>
          </div>

<div class="modal-footer d-flex justify-content-around" style="background:#020617; border-top:1px solid #1f2937;">
  {{-- Botón Regresar al Index --}}
  <a href="{{ route('admin.products.index') }}" class="btn btn-secondary flex-fill mx-2">
    <i class="fas fa-arrow-left"></i>
    {{ __('m_tours.product.ui.back_to_list') }}
  </a>

  {{-- Botón Eliminar Borrador --}}
  @if($mainDraft)
    <button type="button"
            class="btn btn-danger flex-fill mx-2"
            id="deleteMainDraft"
            data-draft-id="{{ $mainDraft->product_id }}"
            data-draft-name="{{ $mainDraft->name }}">
      <i class="fas fa-trash-alt"></i>
      {{ __('m_tours.product.wizard.delete_draft') }}
    </button>
  @endif

  {{-- Botón Continuar Draft --}}
  @if($mainDraft)
    <a href="{{ route('admin.products.wizard.continue', $mainDraft) }}"
       class="btn btn-success flex-fill mx-2">
      <i class="fas fa-play"></i>
      {{ __('m_tours.product.wizard.continue_draft') }}
    </a>
  @endif
</div>
        </div>
      </div>
    </div>

    {{-- Formularios ocultos para acciones de drafts --}}
    @foreach($existingDrafts as $draft)
      <form id="deleteDraftForm{{ $draft->product_id }}"
            action="{{ route('admin.products.wizard.delete-draft', $draft) }}"
            method="POST"
            style="display: none;">
        @csrf
        @method('DELETE')
      </form>
    @endforeach

    {{-- Form de delete-all, por si lo usas en otro lugar, pero sin botón en el modal --}}
    <form id="deleteAllDraftsForm"
          action="{{ route('admin.products.wizard.delete-all-drafts') }}"
          method="POST"
          style="display: none;">
      @csrf
      @method('DELETE')
    </form>
  @endif

  {{-- Modales reutilizables, si siguen siendo útiles en el wizard --}}
  @includeWhen(View::exists('admin.products.partials.inline-modals'), 'admin.products.partials.inline-modals')
@stop

@push('js')
  {{-- Librerías base (si no están ya en el layout) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Scripts propios del módulo de products (validaciones, toasts, etc.) --}}
  @includeIf('admin.products.partials.scripts')
  @includeIf('admin.products.partials.inline-scripts')

  {{-- ================================================
       SCRIPT PARA MANEJAR DRAFTS EXISTENTES
       ================================================ --}}
  @if(isset($existingDrafts) && $existingDrafts->count() > 0)
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Mostrar el modal automáticamente al cargar la página
        const draftsModalEl = document.getElementById('draftsModal');
        if (draftsModalEl && window.bootstrap && bootstrap.Modal) {
          const draftsModal = new bootstrap.Modal(draftsModalEl);
          draftsModal.show();
        }

        // 🗑️ Eliminar borrador principal (botón del footer)
        const deleteMainDraftBtn = document.getElementById('deleteMainDraft');
        if (deleteMainDraftBtn) {
          deleteMainDraftBtn.addEventListener('click', function() {
            const draftId = this.dataset.draftId;
            const draftName = this.dataset.draftName;

            Swal.fire({
              title: '{{ __("m_tours.product.wizard.confirm_delete_title") }}',
              html: '<p>{{ __("m_tours.product.wizard.confirm_delete_message") }}</p><p class="font-weight-bold">' + draftName + '</p>',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#dc2626',
              cancelButtonColor: '#4b5563',
              confirmButtonText: '{{ __("m_tours.common.delete") }}',
              cancelButtonText: '{{ __("m_tours.common.cancel") }}',
            }).then((result) => {
              if (result.isConfirmed) {
                const form = document.getElementById('deleteDraftForm' + draftId);
                if (form) form.submit();
              }
            });
          });
        }
      });
    </script>
  @endif
@endpush
