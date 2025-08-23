<div class="modal fade" id="travelerModal" tabindex="-1" aria-labelledby="travelerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('adminlte::adminlte.select_travelers') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="small text-muted mb-3">{{ __('adminlte::adminlte.max_travelers_info') }}</p>

        <!-- Adult row -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong>{{ __('adminlte::adminlte.adult') }}</strong> <small>({{ __('adminlte::adminlte.age_10_plus') }})</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary traveler-btn" data-type="adult" data-action="decrease">-</button>
            <span class="mx-2" id="adult-count">2</span>
            <button class="btn btn-outline-secondary traveler-btn" data-type="adult" data-action="increase">+</button>
          </div>
        </div>

        <!-- Kid row -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong>{{ __('adminlte::adminlte.kid') }}</strong> <small>({{ __('adminlte::adminlte.age_4_to_9') }})</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary traveler-btn" data-type="kid" data-action="decrease">-</button>
            <span class="mx-2" id="kid-count">0</span>
            <button class="btn btn-outline-secondary traveler-btn" data-type="kid" data-action="increase">+</button>
          </div>
        </div>

        <!-- Hidden Inputs -->
        <input type="hidden" name="adult_count" id="adult_count" value="2">
        <input type="hidden" name="kid_count" id="kid_count" value="0">

        <!-- Total -->
        <p class="fw-bold mt-3" id="modal-total-price">{{ __('adminlte::adminlte.total') }}: $0.00</p>

        <p class="small text-muted mb-0">{{ __('adminlte::adminlte.max_limits_info') }}</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">
          {{ __('adminlte::adminlte.apply') }}
        </button>
      </div>
    </div>
  </div>
</div>
