<template id="itinerary-template">
    <div class="row mb-2 itinerary-item">
        <div class="col-md-4">
            <input type="text" name="__NAME__" class="form-control" placeholder="Título" required>
        </div>
        <div class="col-md-6">
            <input type="text" name="__DESC__" class="form-control" placeholder="Descripción" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm btn-remove-itinerary">&times;</button>
        </div>
    </div>
</template>