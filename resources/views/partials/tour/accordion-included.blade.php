<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="headingIncluded">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#collapseIncluded"
      aria-expanded="false"
      aria-controls="collapseIncluded"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('adminlte::adminlte.whats_included') }}
    </button>
  </h2>
  <div id="collapseIncluded" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">
      <div class="row">
        <div class="col-md-6">
          <ul class="list-unstyled">
            @foreach($tour->amenities as $am)
              <li>✔️ {{ $am->translated_name ?? $am->name }}</li>
            @endforeach
          </ul>
        </div>
        <div class="col-md-6">
          <ul class="list-unstyled">
            @foreach($tour->excludedAmenities as $ex)
              <li>❌ {{ $ex->translated_name ?? $ex->name }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
