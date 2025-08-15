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
          <th style="min-width: 300px;">Título</th>
          <th>Tipo</th>
          <th>Vigencia</th>
          <th>Default</th>
          <th>Estado</th>
          <th style="width: 220px;">Acciones</th>
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

          {{-- Fila colapsable con descripción + SECCIONES --}}
          <tr id="{{ $cid }}" class="collapse">
            <td colspan="6">
              <div class="p-3 border rounded content-box">
                {{-- Descripción de la política (categoría) --}}
                @if(filled($t?->content))
                  <h6 class="mb-2"><i class="far fa-file-alt mr-1"></i> Descripción</h6>
                  <div class="mb-3">{!! nl2br(e($t->content)) !!}</div>
                @endif

                {{-- Secciones (solo vista resumida) --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0"><i class="fas fa-list-ul mr-1"></i> Secciones</h6>
                  <a href="{{ route('admin.policies.sections.index', $policy) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-sliders-h"></i> Gestionar secciones
                  </a>
                </div>

                @if($policy->sections->isNotEmpty())
                  <ul class="list-group">
                    @foreach($policy->sections as $section)
                      @php
                        $st = $section->translation(app()->getLocale()) ?? $section->translation('es');
                        $sid = 'secPreview'.$section->section_id;
                      @endphp
                      <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <strong>{{ $st?->title ?? '—' }}</strong>
                            <span class="ml-2 badge badge-{{ $section->is_active ? 'success' : 'secondary' }}">
                              {{ $section->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                          </div>
                          <button class="btn btn-link btn-xs p-0 collapsed" type="button"
                                  data-toggle="collapse" data-target="#{{ $sid }}">
                            <span class="when-closed"><i class="fas fa-eye"></i> Ver</span>
                            <span class="when-open"><i class="fas fa-eye-slash"></i> Ocultar</span>
                          </button>
                        </div>

                        <div id="{{ $sid }}" class="collapse mt-2">
                          <div class="small text-muted" style="white-space:pre-line;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($st?->content ?? ''), 600) }}
                          </div>
                        </div>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <div class="text-muted"><em>No hay secciones registradas.</em></div>
                @endif
              </div>
            </td>
          </tr>

          {{-- Modal Editar (policy) --}}
          @include('admin.policies.edit-modal', ['policy' => $policy, 't' => $t])
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No hay políticas registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Crear (policy) --}}
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

  .content-box { background: rgba(0,0,0,.03); }
  .dark-mode .content-box { background: rgba(255,255,255,.03); }
</style>
@endpush
