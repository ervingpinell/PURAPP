@php
  // $policy puede venir null en "create"
  // $t es la traducción activa (puede ser null)
  // $mode = 'create' | 'edit' (opcional)
  $mode = $mode ?? (isset($policy) ? 'edit' : 'create');
  $currentLocale = app()->getLocale();
@endphp

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nombre interno</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $policy->name ?? '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Tipo</label>
    <input type="text" name="type" class="form-control"
           placeholder="cancelacion, reembolso, terminos, privacidad..."
           value="{{ old('type', $policy->type ?? '') }}" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Vigente desde</label>
    <input type="date" name="effective_from" class="form-control"
           value="{{ old('effective_from', $policy->effective_from ?? '') }}">
  </div>

  <div class="col-md-3">
    <label class="form-label">Vigente hasta</label>
    <input type="date" name="effective_to" class="form-control"
           value="{{ old('effective_to', $policy->effective_to ?? '') }}">
  </div>

  <div class="col-md-4">
    <div class="form-check mt-4">
      <input class="form-check-input" type="checkbox" name="is_default" id="is_default_{{ $policy->policy_id ?? 'create' }}"
             {{ old('is_default', $policy->is_default ?? false) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_default_{{ $policy->policy_id ?? 'create' }}">Marcar como default</label>
    </div>
  </div>
</div>

<hr>

<input type="hidden" name="locale" value="{{ $currentLocale }}">

<div class="mb-3">
  <label class="form-label">Título ({{ strtoupper($currentLocale) }})</label>
  <input type="text" name="title" class="form-control"
         value="{{ old('title', $t?->title ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">Contenido ({{ strtoupper($currentLocale) }})</label>
  <textarea name="content" class="form-control" rows="8">{{ old('content', $t?->content ?? '') }}</textarea>
</div>
