@extends('adminlte::page')

@php
/** @var \App\Models\Tour|\App\Models\Policy|\Illuminate\Database\Eloquent\Model $item */
// idioma de CONTENIDO a editar:
$targetLocale = $locale;

// idioma de la UI (labels, títulos) se mantiene en app()->getLocale()
$availableEditLocales = ['es' => 'Español', 'en' => 'English', 'fr' => 'Français', 'pt' => 'Português', 'de' => 'Deutsch'];
@endphp

@section('title', __('m_config.translations.edit_title'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
  <h1 class="mb-0">
    <i class="fas fa-language"></i>
    {{ __('m_config.translations.edit_title') }}
    <span class="badge bg-dark ml-2" id="current-editing-locale">{{ strtoupper($targetLocale) }}</span>
  </h1>

  {{-- Selector de idioma de edición vía AJAX --}}
  <div class="dropdown">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="editLocaleDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fas fa-globe mr-1"></i>
      <span id="editing-locale-label">{{ __('m_config.translations.editing') }}: {{ $availableEditLocales[$targetLocale] ?? strtoupper($targetLocale) }}</span>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="editLocaleDropdown">
      @foreach($availableEditLocales as $code => $name)
      <a class="dropdown-item change-editing-locale {{ $code === $targetLocale ? 'active' : '' }}"
        href="#"
        data-locale="{{ $code }}"
        data-name="{{ $name }}">
        {{ $name }}
      </a>
      @endforeach
    </div>
  </div>
</div>
@stop

@section('content')
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
  <input type="hidden" name="locale" value="{{ $targetLocale }}">

  {{-- ======= Tarjeta principal ======= --}}
  <div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
      <strong>
        <i class="fas fa-info-circle mr-2"></i>
        {{ __('m_config.translations.main_information') }}
        <span class="ml-2 badge bg-dark">{{ strtoupper($targetLocale) }}</span>
      </strong>
    </div>

    <div class="card-body">
      @foreach ($fields as $field)
      @php
      // Etiquetas (UI) en idioma de la interfaz
      if ($type === 'policies') {
      if ($field === 'title') {
      $label = __('m_config.translations.policy_name');
      } elseif ($field === 'content') {
      $label = __('m_config.translations.policy_content');
      } else {
      $label = ucfirst($field);
      }
      } else {
      $labelKey = 'm_config.translations.' . $field;
      $resolved = __($labelKey);
      $label = ($resolved !== $labelKey) ? $resolved : ucfirst($field);
      }

      $rows = in_array($field, ['content','overview','description']) ? 6 : 3;

      // Contenido (valores) en idioma objetivo de EDICIÓN
      if ($type === 'policies' && $field === 'title') {
      $value = old(
      "translations.$field",
      ($translations[$field] ?? '') !== '' ? $translations[$field] : ($item->name ?? '')
      );
      } else {
      $value = old("translations.$field", $translations[$field] ?? '');
      }
      @endphp

      <div class="form-group mb-3">
        <label for="{{ $field }}">
          <i class="far fa-edit mr-1"></i>
          {{ $label }} ({{ strtoupper($targetLocale) }})
        </label>

        {{-- Para policies: title como <input>, el resto como <textarea> --}}
        @if($type === 'policies' && $field === 'title')
        <input
          type="text"
          name="translations[{{ $field }}]"
          id="{{ $field }}"
          class="form-control"
          value="{{ $value }}">
        @else
        <textarea
          name="translations[{{ $field }}]"
          id="{{ $field }}"
          class="form-control"
          rows="{{ $rows }}">{{ $value }}</textarea>
        @endif
      </div>
      @endforeach
    </div>
  </div>

  {{-- Itinerario / Ítems (solo tours) --}}
  @includeWhen($type === 'tours', 'admin.translations.partials.edit-tour-translations', [
  'item' => $item,
  'locale' => $targetLocale,
  ])

  {{-- Secciones de políticas (solo policies) --}}
  @includeWhen($type === 'policies', 'admin.translations.partials.edit-policy-translations', [
  'item' => $item,
  'locale' => $targetLocale,
  'type' => $type,
  ])

  <div class="text-right mt-4">
    @can('edit-translations')
    <button type="submit" class="btn btn-success">
      <i class="fas fa-save mr-1"></i> {{ __('m_config.translations.save') }}
    </button>
    @else
    <div class="d-inline-block" data-toggle="tooltip" title="{{ __('m_config.translations.no_permission_to_edit') }}">
      <button type="button" class="btn btn-secondary" disabled>
        <i class="fas fa-lock mr-1"></i> {{ __('m_config.translations.read_only') ?? 'Read Only' }}
      </button>
    </div>
    @endcan
  </div>
</form>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const flashSuccess = @json(session('success'));
    const flashError = @json(session('error'));
    const valErrors = @json($errors-> any() ? $errors-> all() : []);

    if (flashSuccess) {
      Swal.fire({
        icon: 'success',
        title: flashSuccess,
        confirmButtonText: @json(__('m_config.translations.ok'))
      });
    }
    if (flashError) {
      Swal.fire({
        icon: 'error',
        title: flashError,
        confirmButtonText: @json(__('m_config.translations.ok'))
      });
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

    // Cambiar locale de edición vía AJAX (sin tocar el UI locale)
    document.querySelectorAll('.change-editing-locale').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const newLocale = this.dataset.locale;

        fetch('{{ route("admin.translations.change-editing-locale") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              locale: newLocale
            })
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) window.location.reload();
          })
          .catch(() => Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cambiar el idioma.'
          }));
      });
    });
  });
</script>
@stop
