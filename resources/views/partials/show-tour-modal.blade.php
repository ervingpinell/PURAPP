<div class="modal fade" id="confirmTourModal" tabindex="-1" aria-labelledby="confirmTourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('adminlte::adminlte.redirect_to_tour') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                {{-- ✅ Este span es para el JS de Home --}}
                <span id="confirmTourModalText" class="d-block mb-1">
                    {{ __('adminlte::adminlte.would_you_like_to_visit') }}
                    <strong id="tourModalName">{{ __('adminlte::adminlte.this_tour') }}</strong>?
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('adminlte::adminlte.cancel') }}
                </button>
                <a href="#" id="tourModalConfirm" class="btn btn-success">
                    {{ __('adminlte::adminlte.confirm') }}
                </a>
                {{-- Alias para JS de home --}}
                <a href="#" id="confirmTourModalGo" class="d-none"></a>
            </div>
        </div>
    </div>
</div>
