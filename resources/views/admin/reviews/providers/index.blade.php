@extends('adminlte::page')

@section('title', __('reviews.providers.index_title'))

@section('content_header')
<h1><i class="fas fa-plug"></i> {{ __('reviews.providers.index_title') }}</h1>
@stop

@section('content')
@if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

@php
// Helper de traducción con fallback
$t = function (string $key, string $fallback) {
$val = __($key);
return $val === $key ? $fallback : $val;
};
@endphp

<div class="card">
  <div class="card-body">
    <form class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ $t('reviews.common.search', 'Buscar') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary btn-block">{{ $t('reviews.common.filter', 'Filtrar') }}</button>
      </div>
      <div class="col-md-6 text-right">
        @can('create-review-providers')
        <a href="{{ route('admin.review-providers.create') }}" class="btn btn-success">
          <i class="fa fa-plus mr-1"></i> {{ $t('reviews.common.new', 'Nuevo') }}
        </a>
        @endcan
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-sm table-hover">
        <thead>
          <tr>
            <th>Slug</th>
            <th>{{ $t('reviews.common.name', 'Nombre') }}</th>
            <th>Driver</th>
            <th>{{ $t('reviews.common.active', 'Activo') }}</th>
            <th>{{ $t('reviews.common.indexable', 'Indexable') }}</th>
            <th>TTL</th>
            <th>{{ $t('reviews.common.actions', 'Acciones') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($providers as $p)
          @php
          $isActive = (bool) $p->is_active;
          $isSystem = (bool) $p->is_system;
          $isIndexable = (bool) $p->indexable;
          $toggleTitle = $isActive
          ? $t('reviews.common.deactivate', 'Desactivar proveedor')
          : $t('reviews.common.activate', 'Activar proveedor');
          @endphp
          <tr>
            <td><code>{{ $p->slug }}</code></td>
            <td>{{ $p->name }}</td>
            <td><code>{{ $p->driver }}</code></td>

            {{-- Activo --}}
            <td>
              @if($isActive)
              <span class="badge badge-success"
                title="{{ $t('reviews.common.active', 'Activo') }}"
                data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.active', 'Activo') }}">
                {{ $t('reviews.common.yes', 'Sí') }}
              </span>
              @else
              <span class="badge badge-secondary"
                title="{{ $t('reviews.common.inactive', 'Inactivo') }}"
                data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.inactive', 'Inactivo') }}">
                {{ $t('reviews.common.no', 'No') }}
              </span>
              @endif
            </td>

            {{-- Indexable --}}
            <td>
              @if($isIndexable)
              <span class="badge badge-info"
                title="{{ $t('reviews.common.indexable_yes', 'Incluye marcado indexable/JSON-LD') }}"
                data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.indexable_yes', 'Incluye marcado indexable/JSON-LD') }}">
                {{ $t('reviews.common.yes', 'Sí') }}
              </span>
              @else
              <span title="{{ $t('reviews.common.indexable_no', 'No indexable') }}"
                data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.indexable_no', 'No indexable') }}">
                —
              </span>
              @endif
            </td>

            <td>{{ $p->cache_ttl_sec }}s</td>

            {{-- Acciones --}}
            <td class="text-nowrap">
              {{-- Editar --}}
              @can('edit-review-providers')
              <a class="btn btn-xs btn-edit"
                href="{{ route('admin.review-providers.edit',$p) }}"
                title="{{ $t('reviews.common.edit', 'Editar') }}"
                data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.edit', 'Editar') }}">
                <i class="fa fa-edit"></i>
              </a>

              {{-- Toggle: si es de sistema, NO renderizamos form ni clase js-confirm-toggle (evita clic/parpadeo) --}}
              @if($isSystem)
              <span class="d-inline-block" tabindex="0"
                data-bs-toggle="tooltip"
                title="{{ $t('reviews.common.system_locked', 'Proveedor del sistema (bloqueado)') }}">
                <button class="btn btn-xs btn-secondary" type="button" disabled aria-disabled="true" style="pointer-events:none;">
                  <i class="fas {{ $isActive ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                </button>
              </span>
              @else
              @can('publish-review-providers')
              <form class="d-inline me-1 js-confirm-toggle"
                method="POST"
                action="{{ route('admin.review-providers.toggle', $p) }}"
                data-active="{{ $isActive ? 1 : 0 }}">
                @csrf
                <button class="btn btn-xs {{ $isActive ? 'btn-toggle' : 'btn-secondary' }}"
                  title="{{ $toggleTitle }}"
                  data-bs-toggle="tooltip" aria-label="{{ $toggleTitle }}">
                  <i class="fas {{ $isActive ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                </button>
              </form>
              @endcan
              @endif

              {{-- Test --}}
              <form method="post" action="{{ route('admin.review-providers.test',$p) }}" class="d-inline">@csrf
                <button class="btn btn-xs btn-info"
                  title="{{ $t('reviews.common.test', 'Probar conexión') }}"
                  data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.test', 'Probar conexión') }}">
                  <i class="fa fa-vial"></i>
                </button>
              </form>

              {{-- Flush cache --}}
              <form method="post" action="{{ route('admin.review-providers.flush',$p) }}" class="d-inline">@csrf
                <button class="btn btn-xs btn-secondary"
                  title="{{ $t('reviews.common.flush_cache', 'Vaciar caché') }}"
                  data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.flush_cache', 'Vaciar caché') }}">
                  <i class="fa fa-broom"></i>
                </button>
              </form>
              @endcan

              {{-- Eliminar --}}
              @can('delete-review-providers')
              <form method="post" action="{{ route('admin.review-providers.destroy',$p) }}" class="d-inline"
                onsubmit="return confirm('{{ $t('reviews.common.delete_confirm', '¿Eliminar proveedor?') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger"
                  title="{{ $t('reviews.common.delete', 'Eliminar') }}"
                  data-bs-toggle="tooltip" aria-label="{{ $t('reviews.common.delete', 'Eliminar') }}">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
              @endcan
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{ $providers->links() }}
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('ok')) <script>
  Swal.fire({
    icon: 'success',
    title: @json(session('ok'))
  });
</script>@endif
@if (session('error')) <script>
  Swal.fire({
    icon: 'error',
    title: @json(session('error'))
  });
</script>@endif

<script>
  (function() {
    // Inicializar tooltips (BS5 o fallback a jQuery/BS4)
    const triggers = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (window.bootstrap && bootstrap.Tooltip) {
      triggers.forEach(el => new bootstrap.Tooltip(el));
    } else if (window.$ && $.fn.tooltip) {
      $(triggers).tooltip();
    }

    // Textos i18n para el SweetAlert del toggle
    const TXT = {
      deactivate_title: @json(__('reviews.common.deactivate_title') !== 'reviews.common.deactivate_title' ?
        __('reviews.common.deactivate_title') : '¿Desactivar proveedor?'),
      deactivate_text: @json(__('reviews.common.deactivate_text') !== 'reviews.common.deactivate_text' ?
        __('reviews.common.deactivate_text') : 'El proveedor dejará de estar activo.'),
      deactivate_ok: @json(__('reviews.common.deactivate') !== 'reviews.common.deactivate' ?
        __('reviews.common.deactivate') : 'Desactivar'),
      activate_title: @json(__('reviews.common.activate_title') !== 'reviews.common.activate_title' ?
        __('reviews.common.activate_title') : '¿Activar proveedor?'),
      activate_text: @json(__('reviews.common.activate_text') !== 'reviews.common.activate_text' ?
        __('reviews.common.activate_text') : 'El proveedor quedará activo.'),
      activate_ok: @json(__('reviews.common.activate') !== 'reviews.common.activate' ?
        __('reviews.common.activate') : 'Activar'),
      cancel: @json(__('reviews.common.cancel') !== 'reviews.common.cancel' ?
        __('reviews.common.cancel') : 'Cancelar'),
      question_icon: 'question'
    };

    // Confirmación SweetAlert para los toggles (solo forms con .js-confirm-toggle)
    document.addEventListener('click', function(ev) {
      const btn = ev.target.closest('form.js-confirm-toggle button');
      if (!btn) return;

      ev.preventDefault();
      const form = btn.closest('form');
      const active = (form.dataset.active === '1');

      Swal.fire({
        icon: TXT.question_icon,
        title: active ? TXT.deactivate_title : TXT.activate_title,
        text: active ? TXT.deactivate_text : TXT.activate_text,
        showCancelButton: true,
        confirmButtonText: active ? TXT.deactivate_ok : TXT.activate_ok,
        cancelButtonText: TXT.cancel,
      }).then(r => {
        if (r.isConfirmed) form.submit();
      });
    }, {
      passive: false
    });
  })();
</script>
@stop