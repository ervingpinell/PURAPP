<div class="modal fade" id="createSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.policies.sections.store', $policy) }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nueva Sección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Clave interna (opcional)</label>
            <input type="text" name="key" class="form-control" placeholder="p.ej. cookies, licencia, ...">
          </div>
          <div class="col-md-3">
            <label class="form-label">Orden</label>
            <input type="number" name="sort_order" class="form-control" value="0">
          </div>
          <div class="col-md-3">
            <div class="form-check mt-4">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active_create" checked>
              <label class="form-check-label" for="is_active_create">Activa</label>
            </div>
          </div>
        </div>

        <hr>

        <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
        <div class="mb-3">
          <label class="form-label">Título ({{ strtoupper(app()->getLocale()) }})</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Contenido ({{ strtoupper(app()->getLocale()) }})</label>
          <textarea name="content" class="form-control" rows="8" required></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
      </div>
    </form>
  </div>
</div>
