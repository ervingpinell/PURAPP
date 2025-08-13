@php $locale = app()->getLocale(); @endphp
<div class="modal fade" id="editSectionModal{{ $section->section_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.policies.sections.update', [$policy, $section]) }}">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Sección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Clave interna (opcional)</label>
            <input type="text" name="key" class="form-control" value="{{ $section->key }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Orden</label>
            <input type="number" name="sort_order" class="form-control" value="{{ $section->sort_order }}">
          </div>
          <div class="col-md-3">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active_{{ $section->section_id }}" {{ $section->is_active ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active_{{ $section->section_id }}">Activa</label>
            </div>
          </div>
        </div>

        <hr>

        <input type="hidden" name="locale" value="{{ $locale }}">
        <div class="mb-3">
          <label class="form-label">Título ({{ strtoupper($locale) }})</label>
          <input type="text" name="title" class="form-control" value="{{ $st?->title }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Contenido ({{ strtoupper($locale) }})</label>
          <textarea name="content" class="form-control" rows="8">{{ $st?->content }}</textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
