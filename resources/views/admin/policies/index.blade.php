{{-- resources/views/admin/policies/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Políticas')

@section('content_header')
  <h1><i class="fas fa-file-contract"></i> Políticas</h1>
@stop

@section('content')
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Revisa los errores:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Botón Crear -->
  <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createPolicyModal">
    <i class="fas fa-plus"></i> Nueva Política
  </button>

  <div class="table-responsive">
    <table class="table table-bordered align-middle" id="policiesTable">
      <thead class="table-dark">
        <tr>
          <th style="min-width: 280px;">Título</th>
          <th>Tipo</th>
          <th>Vigencia</th>
          <th>Default</th>
          <th>Estado</th>
          <th style="width: 180px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($policies as $policy)
          @php
            $t = $policy->translation(app()->getLocale()) ?? $policy->translation('es');
            $cid = 'policyBody'.$policy->policy_id;
          @endphp

          {{-- Fila principal --}}
          <tr>
            <td>
              <div class="d-flex justify-content-between align-items-start">
                <div class="font-weight-bold">
                  {{ $t?->title ?? $policy->name }}
                  <small class="text-muted">({{ strtoupper($t->locale ?? app()->getLocale()) }})</small>
                </div>

                {{-- Botón Ver/Ocultar (BS4) --}}
                <button class="btn btn-link btn-sm p-0 collapsed"
                        type="button"
                        data-toggle="collapse"
                        data-target="#{{ $cid }}"
                        aria-expanded="false"
                        aria-controls="{{ $cid }}">
                  <span class="when-closed"><i class="fas fa-eye"></i> Ver</span>
                  <span class="when-open"><i class="fas fa-eye-slash"></i> Ocultar</span>
                </button>
              </div>
            </td>

            <td>
              <span class="badge badge-info">{{ $policy->type ?: 'general' }}</span>
            </td>

            <td>
              <small>
                @if($policy->effective_from)
                  <span class="text-muted">Desde:</span> {{ \Illuminate\Support\Carbon::parse($policy->effective_from)->toDateString() }}
                @endif
                @if($policy->effective_to)
                  <span class="text-muted ml-2">Hasta:</span> {{ \Illuminate\Support\Carbon::parse($policy->effective_to)->toDateString() }}
                @endif
                @if(!$policy->effective_from && !$policy->effective_to)
                  <span class="text-muted">—</span>
                @endif
              </small>
            </td>

            <td>
              <span class="badge {{ $policy->is_default ? 'badge-primary' : 'badge-secondary' }}">
                {{ $policy->is_default ? 'Sí' : 'No' }}
              </span>
            </td>

            <td>
              <span class="badge {{ $policy->is_active ? 'badge-success' : 'badge-secondary' }}">
                {{ $policy->is_active ? 'Activa' : 'Inactiva' }}
              </span>
            </td>

            <td>
              <!-- Editar -->
              <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editPolicyModal{{ $policy->policy_id }}">
                <i class="fas fa-edit"></i>
              </button>

              <!-- Activar/Desactivar -->
              <form action="{{ route('admin.policies.toggleStatus', $policy) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning" title="Activar/Desactivar">
                  <i class="fas fa-toggle-{{ $policy->is_active ? 'off' : 'on' }}"></i>
                </button>
              </form>

              <!-- Eliminar -->
              <form action="{{ route('admin.policies.destroy', $policy) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar esta política? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>

          {{-- Fila colapsable con el contenido --}}
          <tr id="{{ $cid }}" class="collapse">
            <td colspan="6">
              <div class="p-3 border rounded content-box">
                {!! nl2br(e($t?->content)) !!}
              </div>
            </td>
          </tr>

          {{-- Modal Editar (partial) --}}
          @include('admin.policies.edit-modal', ['policy' => $policy, 't' => $t])
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No hay políticas registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Crear (partial) --}}
  @include('admin.policies.create-modal')
@stop

@push('css')
<style>
  /* Alterna el texto del botón sin JS extra */
  .btn[data-toggle="collapse"] .when-open { display: none; }
  .btn[data-toggle="collapse"].collapsed .when-open { display: none; }
  .btn[data-toggle="collapse"].collapsed .when-closed { display: inline; }
  .btn[data-toggle="collapse"]:not(.collapsed) .when-closed { display: none; }
  .btn[data-toggle="collapse"]:not(.collapsed) .when-open { display: inline; }

  /* Caja del contenido (ajusta para dark mode si usas AdminLTE dark) */
  .content-box {
    background: rgba(0,0,0,.03);
  }
  .dark-mode .content-box {
    background: rgba(255,255,255,.03);
  }
</style>
@endpush

@push('js')
<script>
/* Si quieres comportamiento "acordeón" (solo uno abierto a la vez),
   descomenta el bloque y añade data-parent="#policiesTable" en la fila collapse:

   <tr id="{{ $cid }}" class="collapse" data-parent="#policiesTable">
*/
// $(function() {
//   $('#policiesTable .collapse').on('show.bs.collapse', function() {
//     $('#policiesTable .collapse.show').collapse('hide');
//   });
// });
</script>
@endpush
