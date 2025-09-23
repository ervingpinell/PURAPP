{{-- resources/views/admin/reviews/providers/form.blade.php --}}
@extends('adminlte::page')

@section('title', $provider->exists ? __('Editar proveedor') : __('Nuevo proveedor'))

@section('content_header')
  <h1>{{ $provider->exists ? __('Editar proveedor') : __('Nuevo proveedor') }}</h1>
@stop

@section('content')
  @if (session('ok'))    <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  @php
    $isLocal = ($provider->slug === 'local') || ($provider->driver === 'local') || (! $provider->exists && old('driver') === 'local');
    $minStars = (int) old('min_stars', data_get($provider->settings, 'min_stars', 0));
  @endphp

  <div class="card">
    {{-- ======= FORM PRINCIPAL (GUARDAR) ======= --}}
    <form method="POST" action="{{ $provider->exists ? route('admin.review-providers.update',$provider) : route('admin.review-providers.store') }}">
      @csrf
      @if($provider->exists) @method('PUT') @endif

      <div class="card-body">
        <div class="form-row">
          <div class="col-md-4 mb-3">
            <label>Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$provider->name) }}" required>
          </div>

          <div class="col-md-4 mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control"
                   value="{{ old('slug',$provider->slug) }}"
                   {{ ($provider->slug==='local' || $provider->is_system || $provider->exists) ? 'readonly' : '' }}>
            <small class="text-muted">No editable para local.</small>
          </div>

          <div class="col-md-4 mb-3">
            <label>Driver</label>
            <select name="driver" class="form-control" {{ ($provider->slug==='local'||$provider->is_system)?'disabled':'' }}>
              <option value="local" @selected(old('driver',$provider->driver)==='local')>local</option>
              <option value="http_json" @selected(old('driver',$provider->driver)==='http_json')>http_json</option>
            </select>
            @if($provider->slug==='local' || $provider->is_system)
              <input type="hidden" name="driver" value="local">
            @endif
          </div>
        </div>

        <div class="form-row">
          <div class="col-md-3 mb-3">
            <label>Activo</label>
            <select name="is_active" class="form-control" {{ ($provider->slug==='local'||$provider->is_system)?'disabled':'' }}>
              <option value="1" @selected(old('is_active',$provider->is_active))>Sí</option>
              <option value="0" @selected(!old('is_active',$provider->is_active))>No</option>
            </select>
            @if($provider->slug==='local' || $provider->is_system)
              <small class="text-muted d-block">El proveedor local siempre está activo.</small>
              <input type="hidden" name="is_active" value="1">
            @endif
          </div>

          <div class="col-md-3 mb-3">
            <label>Indexable</label>
            <select name="indexable" class="form-control">
              <option value="1" @selected(old('indexable',$provider->indexable))>Sí</option>
              <option value="0" @selected(!old('indexable',$provider->indexable))>No</option>
            </select>
          </div>

          <div class="col-md-3 mb-3">
            <label>Cache TTL (seg)</label>
            <input type="number" name="cache_ttl_sec" class="form-control" min="60" max="86400"
                   value="{{ old('cache_ttl_sec',$provider->cache_ttl_sec ?? 3600) }}">
          </div>
        </div>

        {{-- === LOCAL === --}}
        @if($isLocal)
          <hr>
          <h5>Opciones de proveedor local</h5>
          <div class="form-row">
            <div class="col-md-3 mb-3">
              <label>Mínimo de estrellas a mostrar</label>
              <select name="min_stars" class="form-control">
                @for($i=0;$i<=5;$i++)
                  <option value="{{ $i }}" @selected($minStars === $i)>
                    {{ $i === 0 ? 'Todas (0+)' : "{$i}+" }}
                  </option>
                @endfor
              </select>
              <small class="text-muted">Solo se mostrarán reseñas locales con rating ≥ este valor.</small>
            </div>
          </div>
        @else
          {{-- === EXTERNOS (HTTP JSON) === --}}
          <hr>
          <h5>Settings (JSON) del proveedor externo</h5>
          <div class="form-group">
            <label>Settings</label>
            <textarea name="settings" rows="12" class="form-control" spellcheck="false">{{ old('settings', json_encode($provider->settings ?? [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)) }}</textarea>
            <small class="text-muted">
              Formato del driver <code>http_json</code>: <code>url</code>, <code>list_path</code>, <code>map</code>, <code>product_map</code>, etc.
            </small>
          </div>
        @endif
      </div>

      <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('admin.review-providers.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>

    {{-- ======= ACCIONES SECUNDARIAS (FUERA DEL FORM PRINCIPAL) ======= --}}
    @if($provider->exists)
      <div class="card-footer d-flex justify-content-end gap-2" style="gap:.5rem">
        {{-- Flush cache (POST) --}}
        <form method="POST" action="{{ route('admin.review-providers.flush', $provider) }}" class="d-inline" novalidate>
          @csrf
          <button type="submit" class="btn btn-warning">Flush cache</button>
        </form>

        {{-- Test (POST) --}}
        <form method="POST" action="{{ route('admin.review-providers.test', $provider) }}" class="d-inline" novalidate>
          @csrf
          <button type="submit" class="btn btn-info">Probar</button>
        </form>
      </div>
    @endif
  </div>
@stop
