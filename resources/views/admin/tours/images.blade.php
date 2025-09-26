@extends('adminlte::page')

@section('title', __('m_tours.image.ui.manage_images'))

@push('css')
<style>
  /* Tarjeta flexible con footer abajo */
  .image-card { display:flex; flex-direction:column; height:100%; }
  .image-card .caption-header {
    padding:.5rem; background:#2f3640; /* ajusta si usas tema claro */
    border-bottom:1px solid rgba(0,0,0,.1);
  }
  .image-card .caption-header .form-control { height: 34px; }
  .image-card .caption-header .status {
    font-size: .75rem; opacity:.8; margin-top:.25rem; min-height: 1rem;
  }
  .image-card .ratio { border-bottom: 1px solid rgba(0,0,0,.06); }
  .image-card .card-body { flex:1 1 auto; display:flex; flex-direction:column; padding:.5rem; }
  .image-card .meta-zone { min-height: 28px; }
  .image-card .card-footer { margin-top:auto; padding:.9rem; background:transparent; border-top:0; }
  .image-card .actions-stack { display:flex; flex-direction:column; gap: 1rem; }
  .image-card .actions-stack .btn { width:100%; }
  .image-card .actions-stack form { margin:0; }
</style>
@endpush

@php
  use Illuminate\Support\Facades\Route;
  $backText = (__('common.back') !== 'common.back') ? __('common.back') : 'Volver';
  $fallback = Route::has('admin.tours.index') ? route('admin.tours.index') : url('/admin');
  $backUrl  = url()->previous();
  if (empty($backUrl) || $backUrl === url()->current()) { $backUrl = $fallback; }
@endphp

@section('content_header')
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">
      {{ __('m_tours.image.ui.manage_images') }}
      <small class="text-muted">— {{ $tour->getTranslatedName() }}</small>
    </h1>

    <a href="{{ $backUrl }}" class="btn btn-warning">
      <i class="fas fa-arrow-left me-1"></i> {{ $backText }}
    </a>
  </div>
@stop

@section('content')
@php
    $imagesRel = $tour->getRelation('images') ?? collect();
    $countRel  = $imagesRel->count();
    $isFull    = $countRel >= $max;
@endphp

<div class="container py-3">
  <h3 class="mb-1">{{ __('m_tours.image.ui.manage_images') }}: {{ $tour->getTranslatedName() }}</h3>
  <p class="text-muted mb-3">{{ $countRel }} / {{ $max }} {{ __('m_tours.image.ui.images_label') }}</p>

  <form action="{{ route('admin.tours.images.store', $tour) }}" method="POST" enctype="multipart/form-data" class="mb-4">
    @csrf
    <input type="file" name="files[]" class="form-control @error('files') is-invalid @enderror" multiple accept="image/png,image/jpeg,image/webp">
    @error('files') <div class="invalid-feedback">{{ $message }}</div> @enderror

    <button class="btn btn-success mt-2" {{ $isFull ? 'disabled' : '' }}>
      <i class="fas fa-upload me-1"></i> {{ __('m_tours.image.ui.upload_btn') }}
    </button>
    @if($isFull)
      <small class="text-danger ms-2">{{ __('m_tours.image.limit_reached_text') }}</small>
    @endif
  </form>

  <div class="row g-3" id="grid" data-reorder-url="{{ route('admin.tours.images.reorder', $tour) }}">
    @forelse($imagesRel as $image)
      <div class="col-6 col-sm-4 col-md-3 col-xl-2" data-id="{{ $image->id }}">
        <div class="card shadow-sm image-card">
          <div class="caption-header">
            <input type="text"
                   class="form-control form-control-sm js-caption-input"
                   placeholder="{{ __('m_tours.image.ui.caption_placeholder') }}"
                   value="{{ old("caption.{$image->id}", $image->caption) }}"
                   data-update-url="{{ route('admin.tours.images.update', [$tour, $image]) }}">
            <div class="status text-muted js-caption-status"></div>
          </div>

          <div class="ratio ratio-1x1">
            <img src="{{ $image->getAttribute('url') }}" alt="img {{ $image->id }}" class="card-img-top" style="object-fit:cover;">
          </div>

          <div class="card-body">
            <div class="meta-zone">
              @if($image->is_cover)
                <span class="badge bg-success">{{ __('m_tours.image.ui.cover_alt') }}</span>
              @endif
            </div>
          </div>

          <div class="card-footer">
            <div class="actions-stack">
              <button type="button"
                      class="btn btn-secondary btn-sm"
                      data-img="{{ $image->getAttribute('url') }}"
                      data-caption="{{ e($image->caption) }}"
                      data-bs-toggle="modal"
                      data-bs-target="#imagePreviewModal"
                      onclick="openImagePreview(this)">
                {{ __('m_tours.image.ui.show_btn') }}
              </button>

              @unless($image->is_cover)
                <form action="{{ route('admin.tours.images.cover', [$tour, $image]) }}" method="POST">
                  @csrf
                  <button class="btn btn-success btn-sm">
                    <i class="fas fa-star me-1"></i> {{ __('m_tours.image.ui.set_cover_btn') }}
                  </button>
                </form>
              @endunless

              <form action="{{ route('admin.tours.images.destroy', [$tour, $image]) }}" method="POST"
                    onsubmit="return confirmAdminImageDelete(event, this);">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">
                  <i class="fas fa-trash me-1"></i> {{ __('m_tours.image.ui.delete_btn') }}
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info mb-0">{{ __('m_tours.image.ui.no_images') }}</div>
      </div>
    @endforelse
  </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h5 class="modal-title">{{ __('m_tours.image.ui.preview_title') }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_tours.image.ui.close_btn') }}"></button>
      </div>
      <div class="modal-body p-0">
        <img id="previewModalImg" src="" alt="preview" class="img-fluid w-100" style="max-height:80vh; object-fit:contain;">
      </div>
      <div class="modal-footer border-0">
        <span class="me-auto small" id="previewModalCaption"></span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.image.ui.close_btn') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openImagePreview(btn){
    const url = btn.getAttribute('data-img');
    const caption = btn.getAttribute('data-caption') || '';
    document.getElementById('previewModalImg').src = url;
    document.getElementById('previewModalCaption').textContent = caption;
  }

  function confirmAdminImageDelete(e, formEl) {
    e.preventDefault(); e.stopPropagation();
    Swal.fire({
      title: @json(__('m_tours.image.ui.confirm_delete_title')),
      text:  @json(__('m_tours.image.ui.confirm_delete_text')),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: @json(__('m_tours.image.ui.delete_btn')),
      cancelButtonText:  @json(__('m_tours.image.ui.cancel_btn')),
      confirmButtonColor: '#dc3545'
    }).then(res => {
      if (res.isConfirmed) {
        const btn = formEl.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ' + @json(__('m_tours.image.deleting')); }
        formEl.submit();
      }
    });
    return false;
  }

  // Auto-guardar leyenda (igual que tenías)
  document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = @json(csrf_token());
    function saveCaption(inputEl) {
      const url = inputEl.dataset.updateUrl;
      const statusEl = inputEl.closest('.caption-header').querySelector('.js-caption-status');
      const formData = new FormData();
      formData.append('_token', csrfToken);
      formData.append('_method', 'PATCH');
      formData.append('caption', inputEl.value || '');

      statusEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
      inputEl.disabled = true;

      fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }})
        .then(res => { if (!res.ok) throw new Error('HTTP '+res.status); return res.json().catch(() => ({})); })
        .then(() => { statusEl.textContent = @json(__('m_tours.image.saved')); })
        .catch(() => {
          statusEl.textContent = '';
          Swal.fire({ icon:'error', title:@json(__('m_tours.image.ui.error_title')), text:@json(__('m_tours.image.errors.update_caption')) });
        })
        .finally(() => { inputEl.disabled = false; });
    }

    let debounceTimer = null;
    document.querySelectorAll('.js-caption-input').forEach(el => {
      el.addEventListener('change', () => saveCaption(el));
      el.addEventListener('blur',   () => saveCaption(el));
      el.addEventListener('input',  () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => saveCaption(el), 1200);
      });
    });
  });
</script>
@endpush
