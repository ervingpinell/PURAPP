<!-- resources/views/partials/bookmodal.blade.php -->
<div class="modal fade" id="travelerModal" tabindex="-1" aria-labelledby="travelerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Select Travelers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="small text-muted mb-3">You can select up to 12 travelers in total.</p>

        <!-- Adult row -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong>Adult</strong> <small>(Age 10+)</small>
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
            <strong>Child</strong> <small>(Age 4-9)</small>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary traveler-btn" data-type="kid" data-action="decrease">-</button>
            <span class="mx-2" id="kid-count">0</span>
            <button class="btn btn-outline-secondary traveler-btn" data-type="kid" data-action="increase">+</button>
          </div>
        </div>

        <!-- Inputs ocultos -->
        <input type="hidden" name="adult_count" id="adult_count" value="2">
        <input type="hidden" name="kid_count" id="kid_count" value="0">

        <!-- Total SOLO del modal -->
        <p class="fw-bold mt-3" id="modal-total-price">Total: $0.00</p>

        <p class="small text-muted mb-0">Max 12 travelers, Max 2 kids.</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">Apply</button>
      </div>
    </div>
  </div>
</div>
