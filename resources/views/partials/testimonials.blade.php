<h2 class="big-title text-center" style="color: var(--primary-dark);">
    {{ __('adminlte::adminlte.what_visitors_say') }}
</h2>

<div id="viator-carousel" class="carousel slide mt-4" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="review-item card shadow-sm border-0 mx-auto w-100">
                <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px;">
                    <p class="text-muted text-center mb-0">{{ __('adminlte::adminlte.loading_reviews') }}</p>
                </div>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#viator-carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#viator-carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>
