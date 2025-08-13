<div class="modal fade" id="createPolicyModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark text-white"><!-- fondo sólido -->
      <form method="POST" action="{{ route('admin.policies.store') }}">
        @csrf

        <div class="modal-header border-secondary">
          <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nueva Política</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @include('admin.policies.form-fields', ['policy' => null, 't' => null, 'mode' => 'create'])
        </div>

        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
