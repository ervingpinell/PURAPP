{{-- resources/views/admin/reviews/providers/form.blade.php --}}
@extends('adminlte::page')

@section('title', $provider->exists ? __('provider.edit_provider') : __('provider.new_provider'))

@section('content_header')
<h1>{{ $provider->exists ? __('provider.edit_provider') : __('provider.new_provider') }}</h1>
@stop

@section('content')
@if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
@if ($errors->any())
<div class="alert alert-danger">
  <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$isLocal = ($provider->slug === 'local') || ($provider->driver === 'local') || (! $provider->exists && old('driver') === 'local');
$isExternal = !$isLocal && $provider->exists;
$minStars = (int) old('min_stars', data_get($provider->settings, 'min_stars', 0));
@endphp

<div class="card">
  {{-- ======= FORM PRINCIPAL (GUARDAR) ======= --}}
  <form method="POST" action="{{ $provider->exists ? route('admin.review-providers.update',$provider) : route('admin.review-providers.store') }}">
    @csrf
    @if($provider->exists) @method('PUT') @endif

    <div class="card-body">
      {{-- Info para proveedores externos --}}
      @if($isExternal)
      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>{{ __('provider.external_provider') }}:</strong> {!! __('provider.external_provider_info') !!}
        <pre class="mb-0 mt-2"><code>php artisan reviews:sync-providers --force</code></pre>
      </div>
      @endif

      <div class="form-row">
        <div class="col-md-4 mb-3">
          <label>{{ __('provider.name') }}</label>
          <input type="text" name="name" class="form-control" value="{{ old('name',$provider->name) }}"
            {{ $isExternal ? 'readonly' : 'required' }}>
          @if($isExternal)
          <small class="text-muted">{{ __('provider.configured_from_env') }}</small>
          @endif
        </div>

        <div class="col-md-4 mb-3">
          <label>{{ __('provider.slug') }}</label>
          <input type="text" name="slug" class="form-control"
            value="{{ old('slug',$provider->slug) }}"
            readonly>
          <small class="text-muted">{{ __('provider.not_editable') }}</small>
        </div>

        <div class="col-md-4 mb-3">
          <label>{{ __('provider.driver') }}</label>
          <input type="text" class="form-control" value="{{ $provider->driver }}" readonly>
          <input type="hidden" name="driver" value="{{ $provider->driver }}">
          <small class="text-muted">{{ $isLocal ? __('provider.local_provider') : __('provider.external_provider_http') }}</small>
        </div>
      </div>

      <div class="form-row">
        <div class="col-md-3 mb-3">
          <label>{{ __('provider.active') }}</label>
          <select name="is_active" class="form-control" {{ ($provider->slug==='local'||$provider->is_system)?'disabled':'' }}>
            <option value="1" @selected(old('is_active',$provider->is_active))>{{ __('provider.yes') }}</option>
            <option value="0" @selected(!old('is_active',$provider->is_active))>{{ __('provider.no') }}</option>
          </select>
          @if($provider->slug==='local' || $provider->is_system)
          <input type="hidden" name="is_active" value="1">
          @endif
        </div>

        <div class="col-md-3 mb-3">
          <label>{{ __('provider.indexable') }}</label>
          <select name="indexable" class="form-control">
            <option value="1" @selected(old('indexable',$provider->indexable))>{{ __('provider.yes') }}</option>
            <option value="0" @selected(!old('indexable',$provider->indexable))>{{ __('provider.no') }}</option>
          </select>
          <small class="text-muted">{{ __('provider.indexable_help') }}</small>
        </div>

        <div class="col-md-3 mb-3">
          <label>{{ __('provider.cache_ttl') }}</label>
          <input type="number" name="cache_ttl_sec" class="form-control" min="60" max="86400"
            value="{{ old('cache_ttl_sec',$provider->cache_ttl_sec ?? 3600) }}">
        </div>

        <div class="col-md-3 mb-3">
          <label>{{ __('provider.min_stars') }}</label>
          <select name="min_stars" class="form-control">
            @for($i=0;$i<=5;$i++)
              <option value="{{ $i }}" @selected($minStars===$i)>
              {{ $i === 0 ? 'Todas (0+)' : "{$i}+" }}
              </option>
              @endfor
          </select>
          <small class="text-muted">{{ __('provider.min_stars_help') }}</small>
        </div>
      </div>

      {{-- === LOCAL === --}}
      @if($isLocal)
      <hr>
      <h5>Opciones de proveedor local</h5>
      {{-- (Empty if we moved everything out, but leaving header just in case other local fields exist or will exist) --}}
      @endif

      {{-- === EXTERNOS === --}}
      @if($isExternal)
      <hr>
      <h5>{{ __('provider.env_configuration') }}</h5>

      <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {!! __('provider.env_warning') !!}
        <pre class="mb-0 mt-2"><code>php artisan reviews:sync-providers --force</code></pre>
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="showAdvancedSettings">
          <label class="custom-control-label" for="showAdvancedSettings">
            {{ __('provider.show_advanced_json') }}
          </label>
        </div>
      </div>

      <div id="advancedSettings" style="display: none;">
        <div class="form-group">
          <label>{{ __('provider.settings_json') }}</label>
          <textarea name="settings_json" class="form-control" rows="20" spellcheck="false" style="font-family: monospace; font-size: 12px;">{{ json_encode($provider->settings ?? [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) }}</textarea>
          <small class="text-muted">
            {!! __('provider.settings_json_help') !!}
          </small>
        </div>
      </div>
      @endif
    </div>

    <div class="card-footer d-flex justify-content-between">
      <a href="{{ route('admin.review-providers.index') }}" class="btn btn-secondary">{{ __('provider.cancel') }}</a>
      <button type="submit" class="btn btn-primary">{{ __('provider.save') }}</button>
    </div>
  </form>

  {{-- ======= ACCIONES SECUNDARIAS ======= --}}
  @if($provider->exists)
  <div class="card-footer">
    <div class="d-flex justify-content-between align-items-center">
      {{-- Product Mapping (only for external providers) --}}
      @if($provider->driver === 'http_json')
      <a href="{{ route('admin.review-providers.product-map.index', $provider) }}" class="btn btn-success">
        <i class="fas fa-map"></i> {{ __('provider.product_mapping') }}
      </a>
      @else
      <div></div>
      @endif

      <div class="d-flex" style="gap:.5rem">
        {{-- Flush cache --}}
        <form method="POST" action="{{ route('admin.review-providers.flush', $provider) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-warning">{{ __('provider.flush_cache') }}</button>
        </form>

        {{-- Test --}}
        <form method="POST" action="{{ route('admin.review-providers.test', $provider) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-info">{{ __('provider.test') }}</button>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>

@section('js')
<script>
  // Toggle advanced settings visibility
  document.getElementById('showAdvancedSettings')?.addEventListener('change', function() {
    const advancedDiv = document.getElementById('advancedSettings');
    if (advancedDiv) {
      advancedDiv.style.display = this.checked ? 'block' : 'none';
    }
  });
</script>
@stop
@stop