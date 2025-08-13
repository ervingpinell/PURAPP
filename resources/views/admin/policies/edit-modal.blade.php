<div class="modal fade" id="editPolicyModal{{ $policy->policy_id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark text-white"><!-- fondo sólido -->
      <form method="POST" action="{{ route('admin.policies.update', $policy) }}">
        @csrf @method('PUT')

        <div class="modal-header border-secondary">
          <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Política</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          @php($t = $t ?? $policy->translation(app()->getLocale()))
          @include('admin.policies.form-fields', ['policy' => $policy, 't' => $t, 'mode' => 'edit'])
        </div>

        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
