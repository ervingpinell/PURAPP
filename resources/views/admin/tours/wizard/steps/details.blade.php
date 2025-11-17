{{-- resources/views/admin/tours/wizard/steps/details.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.details'))

@push('css')
<style>
    body.sidebar-mini .content-wrapper {
        overflow-y: auto !important;
    }

    /* Header del wizard, integrado con AdminLTE */
    .details-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: .5rem;
        margin-bottom: 1.5rem;
    }
    .details-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .details-header p {
        margin: .25rem 0 0;
        opacity: .9;
    }

    /* Card principal (dejamos la card de AdminLTE y sólo ajustamos detalles) */
    .details-card .card-header {
        border-bottom: 0;
        background: #111827;
        color: #e5e7eb;
    }
    .details-card .card-body {
        background: #0b1120;
        color: #e5e7eb;
    }
    .details-card .card-footer {
        background: #020617;
        border-top: 1px solid #1f2937;
    }

    /* Inputs / selects en dark */
    .details-card .form-control,
    .details-card textarea.form-control {
        background: #020617;
        border: 1px solid #374151;
        color: #e5e7eb;
    }
    .details-card .form-control:focus,
    .details-card textarea.form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.15rem rgba(99,102,241,.25);
        background: #020617;
        color: #e5e7eb;
    }

    label {
        font-weight: 600;
    }

    .form-text.text-muted,
    small.text-muted {
        color: #9ca3af !important;
    }

    .invalid-feedback {
        display: block;
        font-size: .85rem;
    }

    /* Contenedor de idiomas */
    #languages-container {
        max-height: 280px;
        overflow-y: auto;
        background: #020617;
        border-color: #374151 !important;
    }
    #languages-container::-webkit-scrollbar {
        width: 8px;
    }
    #languages-container::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 4px;
    }

    /* Contador de caracteres */
    .char-counter {
        font-size: .75rem;
        color: #9ca3af;
        margin-top: .25rem;
        display: block;
    }
    .char-counter.near-limit {
        color: #fbbf24;
    }
    .char-counter.at-limit {
        color: #f87171;
    }

    /* Alert info borrador */
    .alert-draft-info {
        background: #0f172a;
        border-color: #1d4ed8;
        color: #e5e7eb;
    }

    /* Modal drafts (colores a juego con dark) */
    #draftsModal .modal-content {
        background: #020617;
        color: #e5e7eb;
        border: 1px solid #1f2937;
    }
    #draftsModal .modal-header {
        background: #facc15;
        color: #111827;
        border-bottom: 1px solid #f59e0b;
    }
    #draftsModal .modal-body {
        background: #020617;
    }
    #draftsModal .modal-footer {
        background: #020617;
        border-top: 1px solid #1f2937;
    }
    #draftsModal table thead {
        background:#111827;
    }
    #draftsModal table tbody tr:nth-child(even) {
        background:#020617;
    }

    @media (max-width: 768px) {
        .details-header {
            padding: 1.2rem 1rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $isEditing   = isset($tour) && $tour && $tour->exists;
    $currentStep = $step ?? 1;
@endphp

<div class="container-fluid">

    {{-- Header del paso --}}
    <div class="details-header">
        <h1>
            <i class="fas fa-info-circle"></i>
            @if($isEditing)
                {{ __('m_tours.tour.wizard.edit_tour') }}
            @else
                {{ __('m_tours.tour.wizard.create_new_tour') }}
            @endif
        </h1>
        <p>{{ __('m_tours.tour.wizard.steps.details') }}</p>
    </div>

    {{-- Stepper superior --}}
    @include('admin.tours.wizard.partials.stepper', [
        'currentStep' => $currentStep,
        'steps'       => $steps ?? [],
        'tour'        => $tour ?? null,
    ])

    {{-- Errores / mensajes --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>{{ __('m_tours.common.form_errors_title') }}</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    @if(isset($limitWarning) && $limitWarning)
        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
            {!! $limitWarning !!}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    {{-- FORM DETALLES --}}
    <form
        id="tour-details-form"
        method="POST"
        action="{{ $isEditing
            ? route('admin.tours.wizard.update.details', $tour)
            : route('admin.tours.wizard.store.details') }}"
        novalidate
    >
        @csrf

        <div class="card details-card mt-3">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i>
                    {{ __('m_tours.tour.wizard.basic_info') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Columna principal --}}
                    <div class="col-md-8">
                        {{-- Nombre --}}
                        <div class="form-group">
                            <label for="name">
                                {{ __('m_tours.tour.fields.name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $tour->name ?? '') }}"
                                required
                                autofocus
                                maxlength="255"
                                data-validate="required|min:3|max:255">
                            <small class="char-counter" id="name-counter">0 / 255</small>
                            <div class="invalid-feedback" id="name-error"></div>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $tour->slug ?? '') }}"
                                placeholder="{{ __('m_tours.tour.ui.generate_auto') }}"
                                maxlength="255"
                                pattern="[a-z0-9-]+"
                                data-validate="slug|max:255">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.ui.slug_help') }}
                            </small>
                            <div class="invalid-feedback" id="slug-error"></div>
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Overview --}}
                        <div class="form-group">
                            <label for="overview">{{ __('m_tours.tour.fields.overview') }}</label>
                            <textarea
                                name="overview"
                                id="overview"
                                class="form-control @error('overview') is-invalid @enderror"
                                rows="5"
                                maxlength="1000"
                                data-validate="max:1000">{{ old('overview', $tour->overview ?? '') }}</textarea>
                            <small class="char-counter" id="overview-counter">0 / 1000</small>
                            <div class="invalid-feedback" id="overview-error"></div>
                            @error('overview')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Duración --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="length">{{ __('m_tours.tour.fields.length_hours') }}</label>
                                    <input
                                        type="number"
                                        name="length"
                                        id="length"
                                        class="form-control @error('length') is-invalid @enderror"
                                        value="{{ old('length', $tour->length ?? '') }}"
                                        step="0.5"
                                        min="0"
                                        max="240"
                                        data-validate="number|min:0|max:240">
                                    <small class="form-text text-muted">
                                        {{ __('m_tours.tour.validation.length_in_hours') }}
                                    </small>
                                    <div class="invalid-feedback" id="length-error"></div>
                                    @error('length')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Capacidad máxima --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_capacity">
                                        {{ __('m_tours.tour.fields.max_capacity') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="max_capacity"
                                        id="max_capacity"
                                        class="form-control @error('max_capacity') is-invalid @enderror"
                                        value="{{ old('max_capacity', $tour->max_capacity ?? 12) }}"
                                        min="1"
                                        max="500"
                                        required
                                        data-validate="required|number|min:1|max:500">
                                    <small class="form-text text-muted">
                                        {{ __('m_tours.tour.validation.max_capacity_help') }}
                                    </small>
                                    <div class="invalid-feedback" id="max_capacity-error"></div>
                                    @error('max_capacity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tamaño de grupo --}}
                        <div class="form-group">
                            <label for="group_size">{{ __('m_tours.tour.fields.group_size') }}</label>
                            <input
                                type="number"
                                name="group_size"
                                id="group_size"
                                class="form-control @error('group_size') is-invalid @enderror"
                                value="{{ old('group_size', $tour->group_size ?? '') }}"
                                min="1"
                                max="500"
                                data-validate="number|min:1|max:500">
                            <small class="form-text text-muted">
                                {{ __('m_tours.tour.hints.group_size') }}
                            </small>
                            <div class="invalid-feedback" id="group_size-error"></div>
                            @error('group_size')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Idiomas --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">{{ __('m_tours.tour.ui.available_languages') }}</label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCreateLanguage">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <a href="{{ route('admin.languages.index') }}"
                                       target="_blank"
                                       class="btn btn-info"
                                       title="{{ __('m_tours.tour.ui.manage_languages') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="border rounded p-3" id="languages-container">
                                @php
                                    $oldLanguages = old('languages', $isEditing
                                        ? $tour->languages->pluck('tour_language_id')->toArray()
                                        : []);
                                @endphp

                                @forelse($languages ?? [] as $language)
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="language_{{ $language->tour_language_id }}"
                                            name="languages[]"
                                            value="{{ $language->tour_language_id }}"
                                            {{ in_array($language->tour_language_id, $oldLanguages, true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="language_{{ $language->tour_language_id }}">
                                            <i class="fas fa-language"></i> {{ $language->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0" id="languages-empty-state">
                                        {{ __('m_tours.tour.ui.no_languages_defined') }}
                                    </p>
                                @endforelse
                            </div>

                            <div class="invalid-feedback" id="languages-error"></div>
                            @error('languages')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @error('languages.*')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Columna lateral --}}
                    <div class="col-md-4">
                        {{-- Color --}}
                        <div class="form-group">
                            <label for="color">{{ __('m_tours.tour.ui.color') }}</label>
                            <input
                                type="color"
                                name="color"
                                id="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror"
                                value="{{ old('color', $tour->color ?? '#3490dc') }}"
                                data-validate="color">
                            <div class="invalid-feedback" id="color-error"></div>
                            @error('color')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tipo de tour --}}
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="tour_type_id" class="mb-0">
                                    {{ __('m_tours.tour.fields.type') }}
                                </label>

                                <div class="btn-group btn-group-sm" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCreateTourType">
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <a href="{{ route('admin.tourtypes.index') }}"
                                       target="_blank"
                                       class="btn btn-info"
                                       title="{{ __('m_tours.tour.ui.manage_tour_types') }}">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <select
                                name="tour_type_id"
                                id="tour_type_id"
                                class="form-control @error('tour_type_id') is-invalid @enderror"
                                data-validate="select">
                                <option value="">{{ '-- ' . __('m_tours.tour.ui.select_type') . ' --' }}</option>
                                @foreach($tourTypes ?? [] as $type)
                                    <option value="{{ $type->tour_type_id }}"
                                            {{ old('tour_type_id', $tour->tour_type_id ?? '') == $type->tour_type_id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="tour_type_id-error"></div>
                            @error('tour_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Estado (oculto, siempre borrador en este paso) --}}
                        <input type="hidden" name="is_active" value="0">

                        {{-- Info modo borrador --}}
                        <div class="alert alert-draft-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('m_tours.tour.wizard.draft_mode') }}</strong>
                            <p class="mb-0 small">{{ __('m_tours.tour.wizard.draft_explanation') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> {{ __('m_tours.common.cancel') }}
                        </a>

                        <button type="submit" class="btn btn-primary ml-2" id="submit-btn">
                            {{ __('m_tours.tour.wizard.save_and_continue') }}
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal: quick create tipo de tour --}}
<div class="modal fade" id="modalCreateTourType" tabindex="-1" aria-labelledby="modalCreateTourTypeLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('admin.tours.wizard.quick.tour-type') }}"
          class="modal-content"
          id="formCreateTourType">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCreateTourTypeLabel">
          <i class="fas fa-tags"></i> {{ __('m_tours.tour.ui.add_tour_type') }}
        </h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="tourTypeModalErrors" class="alert alert-danger d-none"></div>

        <div class="form-group">
          <label for="new_tour_type_name">{{ __('m_tours.tour_type.fields.name') ?? 'Nombre' }}</label>
          <input type="text" name="name" id="new_tour_type_name" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="new_tour_type_description">{{ __('m_tours.tour_type.fields.description') ?? 'Descripción' }}</label>
          <textarea name="description" id="new_tour_type_description" class="form-control" rows="3"></textarea>
        </div>

        <div class="custom-control custom-switch">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" class="custom-control-input" id="new_tour_type_is_active" name="is_active" value="1" checked>
          <label class="custom-control-label" for="new_tour_type_is_active">
            {{ __('m_tours.tour_type.fields.status') ?? 'Activo' }}
          </label>
        </div>

        <small class="form-text text-muted mt-2">
          {{ __('m_tours.tour.ui.quick_create_type_hint') ?? 'Creación rápida. Para configuraciones avanzadas usa el módulo de tipos de tour.' }}
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          {{ __('m_tours.common.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: quick create idioma --}}
<div class="modal fade" id="modalCreateLanguage" tabindex="-1" aria-labelledby="modalCreateLanguageLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST"
          action="{{ route('admin.tours.wizard.quick.language') }}"
          class="modal-content"
          id="formCreateLanguage">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalCreateLanguageLabel">
          <i class="fas fa-language"></i> {{ __('m_tours.tour.ui.add_language') }}
        </h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.close') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="languageModalErrors" class="alert alert-danger d-none"></div>

        <div class="form-group">
          <label for="new_language_name">{{ __('m_tours.language.fields.name') ?? 'Nombre' }}</label>
          <input type="text" name="name" id="new_language_name" class="form-control" required>
        </div>

        <small class="form-text text-muted mt-2">
          {{ __('m_tours.tour.ui.quick_create_language_hint') ?? 'Creación rápida. Para configuraciones avanzadas usa el módulo de idiomas.' }}
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          {{ __('m_tours.common.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> {{ __('m_tours.common.save') }}
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal de borradores existentes --}}
@if(isset($existingDrafts) && $existingDrafts->count() > 0)
  @php
    $mainDraft = $existingDrafts->sortByDesc('updated_at')->first();
  @endphp

  <div class="modal fade" id="draftsModal" tabindex="-1" aria-labelledby="draftsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="draftsModalLabel">
            <i class="fas fa-exclamation-triangle"></i>
            {{ __('m_tours.tour.wizard.existing_drafts_title') }}
          </h5>
        </div>

        <div class="modal-body">
          <p class="lead mb-3">
            {{ __('m_tours.tour.wizard.existing_drafts_message', ['count' => $existingDrafts->count()]) }}
          </p>

          <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
              <thead>
                <tr>
                  <th>{{ __('m_tours.tour.fields.name') }}</th>
                  <th>{{ __('m_tours.tour.fields.type') }}</th>
                  <th class="text-center">{{ __('m_tours.tour.wizard.current_step') }}</th>
                  <th>{{ __('m_tours.common.updated_at') }}</th>
                </tr>
              </thead>
              <tbody>
              @foreach($existingDrafts as $draft)
                <tr>
                  <td>
                    <strong>{{ $draft->name ?: __('m_tours.tour.wizard.unnamed_draft') }}</strong><br>
                    <small class="text-muted">{{ $draft->slug }}</small>
                  </td>
                  <td>
                    @if($draft->tourType)
                      <span class="badge bg-info">{{ $draft->tourType->name }}</span>
                    @else
                      <span class="text-muted">{{ __('m_tours.common.not_set') }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary">
                      {{ __('m_tours.tour.wizard.step') }} {{ $draft->current_step ?? 1 }}/6
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

          <div class="alert alert-info mt-3 mb-0">
            <i class="fas fa-info-circle"></i>
            {{ __('m_tours.tour.wizard.drafts_info') }}
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-around">
          <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary flex-fill mx-2">
            <i class="fas fa-arrow-left"></i>
            {{ __('m_tours.tour.ui.back') }}
          </a>

          @if($mainDraft)
            <button type="button"
                    class="btn btn-danger flex-fill mx-2"
                    id="deleteMainDraft"
                    data-delete-url="{{ route('admin.tours.wizard.delete-draft', $mainDraft) }}"
                    data-draft-name="{{ $mainDraft->name }}">
              <i class="fas fa-trash-alt"></i>
              {{ __('m_tours.tour.wizard.delete_draft') }}
            </button>

            <a href="{{ route('admin.tours.wizard.continue', $mainDraft) }}"
               class="btn btn-success flex-fill mx-2">
              <i class="fas fa-play"></i>
              {{ __('m_tours.tour.wizard.continue_draft') }}
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>
@endif

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const IS_EDITING = @json($isEditing);

    const validationMessages = {
        required: @json(__('m_tours.tour.validation.required')),
        min: @json(__('m_tours.tour.validation.min')),
        max: @json(__('m_tours.tour.validation.max')),
        number: @json(__('m_tours.tour.validation.number')),
        slug: @json(__('m_tours.tour.validation.slug')),
        color: @json(__('m_tours.tour.validation.color')),
        select: @json(__('m_tours.tour.validation.select')),
    };

    function validateField(field) {
        const rules = field.dataset.validate;
        if (!rules) return true;

        const value = field.value.trim();
        const fieldName = field.name;
        const errorDiv = document.getElementById(fieldName + '-error');

        let isValid = true;
        let errorMessage = '';
        const ruleList = rules.split('|');

        for (let rule of ruleList) {
            const [ruleName, ruleValue] = rule.split(':');

            switch (ruleName) {
                case 'required':
                    if (!value) {
                        isValid = false;
                        errorMessage = validationMessages.required;
                    }
                    break;
                case 'min': {
                    const minLength = parseInt(ruleValue);
                    if (field.type === 'number') {
                        if (value && parseFloat(value) < minLength) {
                            isValid = false;
                            errorMessage = validationMessages.min.replace(':min', minLength);
                        }
                    } else if (value && value.length < minLength) {
                        isValid = false;
                        errorMessage = validationMessages.min.replace(':min', minLength);
                    }
                    break;
                }
                case 'max': {
                    const maxLength = parseInt(ruleValue);
                    if (field.type === 'number') {
                        if (value && parseFloat(value) > maxLength) {
                            isValid = false;
                            errorMessage = validationMessages.max.replace(':max', maxLength);
                        }
                    } else if (value && value.length > maxLength) {
                        isValid = false;
                        errorMessage = validationMessages.max.replace(':max', maxLength);
                    }
                    break;
                }
                case 'number':
                    if (value && isNaN(value)) {
                        isValid = false;
                        errorMessage = validationMessages.number;
                    }
                    break;
                case 'slug':
                    if (value && !/^[a-z0-9-]*$/.test(value)) {
                        isValid = false;
                        errorMessage = validationMessages.slug;
                    }
                    break;
                case 'color':
                    if (value && !/^#[0-9A-F]{6}$/i.test(value)) {
                        isValid = false;
                        errorMessage = validationMessages.color;
                    }
                    break;
                case 'select':
                    break;
            }
            if (!isValid) break;
        }

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            }
        }

        return isValid;
    }

    function updateCharCounter(field, counter, max) {
        const current = field.value.length;
        counter.textContent = `${current} / ${max}`;
        counter.classList.remove('near-limit', 'at-limit');
        if (current >= max) {
            counter.classList.add('at-limit');
        } else if (current >= max * 0.9) {
            counter.classList.add('near-limit');
        }
    }

    const nameField = document.getElementById('name');
    const slugField = document.getElementById('slug');
    const nameCounter = document.getElementById('name-counter');
    const overviewField = document.getElementById('overview');
    const overviewCounter = document.getElementById('overview-counter');

    if (nameField && slugField && !IS_EDITING) {
        nameField.addEventListener('input', function (e) {
            if (!slugField.value || slugField.dataset.autogenerated === 'true') {
                const slug = e.target.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugField.value = slug;
                slugField.dataset.autogenerated = 'true';
            }
        });

        slugField.addEventListener('input', function () {
            if (this.value) {
                this.dataset.autogenerated = 'false';
            }
        });
    }

    if (nameField && nameCounter) {
        updateCharCounter(nameField, nameCounter, 255);
        nameField.addEventListener('input', () => updateCharCounter(nameField, nameCounter, 255));
    }
    if (overviewField && overviewCounter) {
        updateCharCounter(overviewField, overviewCounter, 1000);
        overviewField.addEventListener('input', () => updateCharCounter(overviewField, overviewCounter, 1000));
    }

    const formFields = document.querySelectorAll('[data-validate]');
    formFields.forEach(field => {
        field.addEventListener('blur', () => validateField(field));
        let timeout;
        field.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => validateField(field), 400);
        });
    });

    const form = document.getElementById('tour-details-form');
    const submitBtn = document.getElementById('submit-btn');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            let isFormValid = true;
            formFields.forEach(field => {
                if (!validateField(field)) isFormValid = false;
            });

            if (!isFormValid) {
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }

                Swal.fire({
                    icon: 'error',
                    title: @json(__('m_tours.tour.validation.form_error_title')),
                    text: @json(__('m_tours.tour.validation.form_error_message')),
                    confirmButtonColor: '#6366f1',
                });

                return false;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + @json(__('m_tours.tour.validation.saving'));
            }

            form.submit();
        });
    }

    function closeModalAndCleanup(modalId) {
        const modalEl = document.getElementById(modalId);
        if (modalEl && window.bootstrap && bootstrap.Modal) {
            const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
            instance.hide();
        }
    }

    function ajaxForm(formEl, onSuccess, onError) {
        if (!formEl) return;
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = formEl.action;
            const formData = new FormData(formEl);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            })
            .then(async response => {
                if (response.ok) return response.json();
                if (response.status === 422) {
                    const data = await response.json();
                    if (onError) onError(data);
                    return null;
                }
                const text = await response.text();
                if (onError) onError({ message: text });
                return null;
            })
            .then(data => { if (data && onSuccess) onSuccess(data); })
            .catch(err => { if (onError) onError({ message: err.message || 'Network error' }); });
        });
    }

    // Quick create tipo
    const tourTypeForm = document.getElementById('formCreateTourType');
    const tourTypeSelect = document.getElementById('tour_type_id');
    const tourTypeErrors = document.getElementById('tourTypeModalErrors');

    ajaxForm(tourTypeForm, function (data) {
        if (tourTypeErrors) {
            tourTypeErrors.classList.add('d-none');
            tourTypeErrors.innerHTML = '';
        }
        if (!data || !data.id || !data.name) return;

        if (tourTypeSelect) {
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = data.name;
            option.selected = true;
            tourTypeSelect.appendChild(option);
        }

        tourTypeForm.reset();
        closeModalAndCleanup('modalCreateTourType');

        Swal.fire({
            icon: 'success',
            title: @json(__('m_tours.tour.validation.success')),
            text: @json(__('m_tours.tour.validation.tour_type_created')),
            timer: 2000,
            showConfirmButton: false,
        });
    }, function (errorData) {
        if (!tourTypeErrors) return;
        tourTypeErrors.classList.remove('d-none');

        if (errorData && errorData.errors) {
            const msgs = Object.values(errorData.errors).flat();
            tourTypeErrors.innerHTML = msgs.map(m => `<div>${m}</div>`).join('');
        } else if (errorData && errorData.message) {
            tourTypeErrors.innerHTML = `<div>${errorData.message}</div>`;
        } else {
            tourTypeErrors.innerHTML = `<div>{{ __('m_tours.tour.validation.tour_type_error') }}</div>`;
        }
    });

    // Quick create idioma
    const languageForm = document.getElementById('formCreateLanguage');
    const languagesContainer = document.getElementById('languages-container');
    const languagesEmptyState = document.getElementById('languages-empty-state');
    const languageErrors = document.getElementById('languageModalErrors');

    ajaxForm(languageForm, function (data) {
        if (languageErrors) {
            languageErrors.classList.add('d-none');
            languageErrors.innerHTML = '';
        }
        if (!data || !data.id || !data.name) return;

        if (languagesEmptyState) languagesEmptyState.remove();

        if (languagesContainer) {
            const wrapper = document.createElement('div');
            wrapper.className = 'custom-control custom-checkbox mb-2';
            const checkboxId = 'language_' + data.id;

            wrapper.innerHTML = `
                <input type="checkbox"
                       class="custom-control-input"
                       id="${checkboxId}"
                       name="languages[]"
                       value="${data.id}"
                       checked>
                <label class="custom-control-label" for="${checkboxId}">
                    <i class="fas fa-language"></i> ${data.name}
                </label>
            `;
            languagesContainer.appendChild(wrapper);
        }

        languageForm.reset();
        closeModalAndCleanup('modalCreateLanguage');

        Swal.fire({
            icon: 'success',
            title: @json(__('m_tours.tour.validation.success')),
            text: @json(__('m_tours.tour.validation.language_created')),
            timer: 2000,
            showConfirmButton: false,
        });
    }, function (errorData) {
        if (!languageErrors) return;
        languageErrors.classList.remove('d-none');

        if (errorData && errorData.errors) {
            const msgs = Object.values(errorData.errors).flat();
            languageErrors.innerHTML = msgs.map(m => `<div>${m}</div>`).join('');
        } else if (errorData && errorData.message) {
            languageErrors.innerHTML = `<div>${errorData.message}</div>`;
        } else {
            languageErrors.innerHTML = `<div>{{ __('m_tours.tour.validation.language_error') }}</div>`;
        }
    });

    // Modal de borradores
    @if(isset($existingDrafts) && $existingDrafts->count() > 0)
        const draftsModalEl = document.getElementById('draftsModal');
        if (draftsModalEl && window.bootstrap && bootstrap.Modal) {
            const draftsModal = new bootstrap.Modal(draftsModalEl);
            draftsModal.show();
        }

        const deleteMainDraftBtn = document.getElementById('deleteMainDraft');
        if (deleteMainDraftBtn) {
            deleteMainDraftBtn.addEventListener('click', function() {
                const deleteUrl = this.dataset.deleteUrl;
                const draftName = this.dataset.draftName || '';

                Swal.fire({
                    title: @json(__('m_tours.tour.wizard.confirm_delete_title')),
                    html: '<p>{{ __("m_tours.tour.wizard.confirm_delete_message") }}</p>'
                          + (draftName ? '<p class="font-weight-bold">' + draftName + '</p>' : ''),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#4b5563',
                    confirmButtonText: @json(__('m_tours.common.delete')),
                    cancelButtonText: @json(__('m_tours.common.cancel')),
                }).then((result) => {
                    if (result.isConfirmed && deleteUrl) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = @json(csrf_token());

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
    @endif
});
</script>
@endpush
