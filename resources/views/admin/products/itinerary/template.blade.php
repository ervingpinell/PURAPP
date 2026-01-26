<template id="itinerary-template">
  <div class="row g-2 itinerary-item mb-2">
    <div class="col-md-5">
      <input type="text" name="__NAME__" class="form-control" placeholder="Título" required>
    </div>
    <div class="col-md-5">
      <input type="text" name="__DESC__" class="form-control" placeholder="Descripción" required>
    </div>
    <div class="col-md-2 text-end">
      <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">×</button>
    </div>
  </div>
</template>