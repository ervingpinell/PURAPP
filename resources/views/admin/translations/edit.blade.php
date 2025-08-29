@extends('adminlte::page')

@section('title', __('m_config.translations.edit_title'))

@section('content_header')
  <h1 class="mb-2">
    <i class="fas fa-language"></i>
    {{ __('m_config.translations.edit_title') }}
    <span class="badge bg-dark ms-2">{{ strtoupper($locale) }}</span>
  </h1>
@stop

@section('content')
@php
  $entityLabel = match ($type) {
      'tours'           => __('m_config.translations.entities.tours'),
      'itineraries'     => __('m_config.translations.entities.itineraries'),
      'itinerary_items' => __('m_config.translations.entities.itinerary_items'),
      'amenities'       => __('m_config.translations.entities.amenities'),
      'faqs'            => __('m_config.translations.entities.faqs'),
      'policies'        => __('m_config.translations.entities.policies'),
      'tour_types'      => __('m_config.translations.entities.tour_types'),
      default           => __('m_config.translations.select'),
  };
@endphp

<noscript>
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-warning">
      <strong>{{ __('m_config.translations.validation_errors') }}</strong>
      <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
</noscript>

<form action="{{ route('admin.translations.update', [$type, $item->getKey()]) }}" method="POST">
  @csrf
  <input type="hidden" name="locale" value="{{ $locale }}">

  <div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
      <strong>
        <i class="fas fa-info-circle me-2"></i>
        {{ __('m_config.translations.main_information') }}
        <span class="ms-2 badge bg-dark">{{ strtoupper($locale) }}</span>
      </strong>
    </div>

    <div class="card-body">
      @foreach ($fields as $field)
        @php
          $labelKey = 'm_config.translations.' . $field;
          $resolved = __($labelKey);
          $label    = ($resolved !== $labelKey) ? $resolved : ucfirst($field);
          $rows     = in_array($field, ['content','overview','description']) ? 6 : 3;
        @endphp
        <div class="form-group mb-3">
          <label for="{{ $field }}">
            <i class="far fa-edit me-1"></i> {{ $label }} ({{ strtoupper($locale) }})
          </label>
          <textarea
            name="translations[{{ $field }}]"
            class="form-control"
            rows="{{ $rows }}"
          >{{ old("translations.$field", $translations[$field] ?? '') }}</textarea>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Extra para Tours --}}
  @includeWhen($type === 'tours', 'admin.translations.partials.edit-tour-translations', [
      'item'   => $item,
      'locale' => $locale,
  ])

  {{-- Extra para Policies (secciones) --}}
  @includeWhen($type === 'policies', 'admin.translations.partials.edit-policy-translations', [
      'item'   => $item,
      'locale' => $locale,
      'type'   => $type,
  ])

  <div class="text-end mt-4">
    <button type="submit" class="btn btn-success">
      <i class="fas fa-save me-1"></i> {{ __('m_config.translations.save') }}
    </button>
  </div>
</form>

{{-- ==== PARCIAL INLINE: Edición de Itinerario e Ítems de un Tour ==== --}}
@if($item->itinerary)
    <!-- Itinerario -->
    <div class="card mb-3">
        <div
          class="card-header bg-info text-white"
          data-bs-toggle="collapse"
          data-bs-target="#collapseItinerary"
          aria-expanded="true"
          aria-controls="collapseItinerary"
          style="cursor: pointer;"
        >
            <h5 class="mb-0">
                <i class="fas fa-route me-2"></i> {{ __('m_config.translations.itinerary') }}
            </h5>
        </div>
        <div id="collapseItinerary" class="collapse show">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="itinerary_name">
                      <i class="far fa-file-alt me-1"></i>
                      {{ __('m_config.translations.itinerary_name') }} ({{ strtoupper($locale) }})
                    </label>
                    <textarea
                      name="itinerary_translations[name]"
                      class="form-control"
                      rows="2"
                    >{{ old('itinerary_translations.name', $item->itinerary->translate($locale)?->name ?? '') }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="itinerary_description">
                      <i class="far fa-file-alt me-1"></i>
                      {{ __('m_config.translations.itinerary_description') }} ({{ strtoupper($locale) }})
                    </label>
                    <textarea
                      name="itinerary_translations[description]"
                      class="form-control"
                      rows="4"
                    >{{ old('itinerary_translations.description', $item->itinerary->translate($locale)?->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Ítems del Itinerario -->
    @foreach ($item->itinerary->items as $index => $it)
        @php $collapseId = "collapseItem{$it->id}"; @endphp
        <div class="card mb-2">
            <div
              class="card-header bg-secondary text-white"
              data-bs-toggle="collapse"
              data-bs-target="#{{ $collapseId }}"
              aria-expanded="false"
              aria-controls="{{ $collapseId }}"
              style="cursor: pointer;"
            >
                <h6 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ __('m_config.translations.item') }} {{ $index + 1 }}: {{ $it->title }}
                </h6>
            </div>
            <div id="{{ $collapseId }}" class="collapse">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="item_title_{{ $it->id }}">
                          <i class="far fa-edit me-1"></i>
                          {{ __('m_config.translations.item_title') }} ({{ strtoupper($locale) }})
                        </label>
                        <textarea
                          name="item_translations[{{ $it->id }}][title]"
                          class="form-control"
                          rows="2"
                        >{{ old("item_translations.$it->id.title", $it->translate($locale)?->title ?? '') }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="item_description_{{ $it->id }}">
                          <i class="far fa-edit me-1"></i>
                          {{ __('m_config.translations.item_description') }} ({{ strtoupper($locale) }})
                        </label>
                        <textarea
                          name="item_translations[{{ $it->id }}][description]"
                          class="form-control"
                          rows="3"
                        >{{ old("item_translations.$it->id.description", $it->translate($locale)?->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const flashSuccess = @json(session('success'));
    const flashError   = @json(session('error'));
    const valErrors    = @json($errors->any() ? $errors->all() : []);

    if (flashSuccess) {
      Swal.fire({ icon: 'success', title: flashSuccess, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
    if (flashError) {
      Swal.fire({ icon: 'error', title: flashError, confirmButtonText: @json(__('m_config.translations.ok')) });
    }
    if (valErrors && valErrors.length) {
      const list = '<ul class="text-start mb-0">' + valErrors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      Swal.fire({
        icon: 'warning',
        title: @json(__('m_config.translations.validation_errors')),
        html: list,
        confirmButtonText: @json(__('m_config.translations.ok')),
      });
    }
  });
  </script>
@stop
