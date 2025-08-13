@extends('adminlte::page')

@section('title', 'Secciones de Política')

@section('content_header')
  @php $pt = $policy->translation(); @endphp
  <h1>
    <i class="fas fa-list-ul"></i> Secciones — {{ $pt?->title ?? $policy->name }}
    <small class="text-muted d-block">{{ $policy->type }}</small>
  </h1>
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

  <div class="d-flex justify-content-between align-items-center mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
      <i class="fas fa-plus"></i> Nueva Sección
    </button>

    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Volver a Políticas
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th style="min-width:240px;">Título</th>
          <th>Clave</th>
          <th>Orden</th>
          <th>Estado</th>
          <th style="width:180px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sections as $section)
          @php $st = $section->translation(); @endphp
          <tr>
            <td>
              <div class="fw-bold">{{ $st?->title ?? '—' }}</div>
              @if(filled($st?->content))
                <div class="policy-excerpt position-relative mt-1" style="max-height: 3.5em; overflow: hidden;" data-expanded="false">
                  <div class="excerpt-content">{{ strip_tags($st->content) }}</div>
                  <div class="fade-overlay position-absolute bottom-0 start-0 w-100" style="height:1.5em;background:linear-gradient(to bottom,transparent,white);"></div>
                </div>
                <button class="btn btn-link p-0 mt-1 toggle-excerpt d-none" style="font-size:.85em;">Leer más</button>
              @endif
            </td>
            <td><code>{{ $section->key ?? '—' }}</code></td>
            <td>{{ $section->sort_order }}</td>
            <td>
              <span class="badge bg-{{ $section->is_active ? 'success' : 'secondary' }}">
                {{ $section->is_active ? 'Activa' : 'Inactiva' }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $section->section_id }}">
                <i class="fas fa-edit"></i>
              </button>

              <form action="{{ route('admin.policies.sections.toggle', [$policy, $section]) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning" title="Activar/Desactivar">
                  <i class="fas fa-toggle-{{ $section->is_active ? 'off' : 'on' }}"></i>
                </button>
              </form>

              <form action="{{ route('admin.policies.sections.destroy', [$policy, $section]) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar esta sección? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>

          {{-- Modal Editar --}}
          @include('admin.policies.sections.edit-modal', ['policy' => $policy, 'section' => $section, 'st' => $st])
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted">No hay secciones registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Crear --}}
  @include('admin.policies.sections.create-modal', ['policy' => $policy])
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.policy-excerpt').forEach(container => {
    const content = container.querySelector('.excerpt-content');
    const toggleBtn = container.parentElement.querySelector('.toggle-excerpt');
    const fadeOverlay = container.querySelector('.fade-overlay');
    if (!content || !toggleBtn) return;

    const contentHeight = content.scrollHeight;
    const maxHeight = parseFloat(getComputedStyle(container).maxHeight);

    if (contentHeight > maxHeight + 5) toggleBtn.classList.remove('d-none');

    toggleBtn.addEventListener('click', function () {
      const isExpanded = container.getAttribute('data-expanded') === 'true';
      container.style.maxHeight = isExpanded ? '3.5em' : 'none';
      if (fadeOverlay) fadeOverlay.style.display = isExpanded ? '' : 'none';
      container.setAttribute('data-expanded', (!isExpanded).toString());
      this.textContent = isExpanded ? 'Leer más' : 'Leer menos';
    });
  });
});
</script>
@endpush
