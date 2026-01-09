@extends('adminlte::page')

@section('title', __('m_tours.image.ui.page_title_pick'))

@push('css')
<style>
  :root {
    --card-radius: .5rem;
    --space-1: .25rem;
    --space-2: .5rem;
    --space-3: .75rem;
    --space-4: 1rem;
    --border: #454d55;
    --wrap-max: 1200px;
    --brand: #007bff;
    --card-bg: #343a40;
    --card-hover-bg: #3d4349;
  }

  /* ====== Tarjeta ====== */
  .image-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    border: 1px solid var(--border);
    border-radius: var(--card-radius);
    overflow: hidden;
    background: var(--card-bg);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }

  .image-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, .3);
    border-color: #6c757d;
    background: var(--card-hover-bg);
  }

  .image-card.selected {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, .25), 0 8px 16px rgba(0, 123, 255, .2);
  }

  .image-card.selected .image-zone::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    box-shadow: inset 0 0 0 3px rgba(0, 123, 255, .3);
    border-radius: inherit;
  }

  /* ====== Selector nuevo (chip) ====== */
  .image-select {
    position: absolute;
    top: .45rem;
    left: .45rem;
    z-index: 15;
  }

  .image-select input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
  }

  .image-check-label {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0, 0, 0, .7);
    border: 2px solid #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    line-height: 1;
    cursor: pointer;
    user-select: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .4);
    transition: transform .12s ease, background .15s ease, box-shadow .15s ease;
  }

  .image-check-label:hover {
    transform: scale(1.05);
  }

  .image-select input[type="checkbox"]:checked+.image-check-label {
    background: var(--brand);
    box-shadow: 0 0 0 3px rgba(0, 123, 255, .3);
  }

  .image-select input[type="checkbox"]:checked+.image-check-label::after {
    content: '✓';
  }

  /* Badge portada */
  .cover-badge {
    position: absolute;
    top: .45rem;
    right: .45rem;
    z-index: 10;
  }

  /* Zona de imagen */
  .image-zone {
    position: relative;
    background: #2c3136;
    cursor: pointer;
  }

  .image-zone img {
    width: 100%;
    height: clamp(140px, 26vw, 200px);
    object-fit: cover;
    display: block;
  }

  /* Caption */
  .caption-zone {
    padding: var(--space-2) var(--space-3);
    background: var(--card-bg);
    border-top: 1px solid var(--border);
  }

  .caption-input {
    border: 1px solid var(--border);
    border-radius: .4rem;
    padding: .4rem .5rem;
    font-size: .85rem;
    width: 100%;
    transition: border-color .15s ease, box-shadow .15s ease;
    background: #2c3136;
    color: #fff;
  }

  .caption-input:focus {
    border-color: #007bff;
    outline: 0;
    box-shadow: 0 0 0 .15rem rgba(0, 123, 255, .25);
  }

  .caption-status {
    font-size: .75rem;
    color: #adb5bd;
    min-height: 1rem;
    margin-top: .15rem;
  }

  /* Acciones por tarjeta */
  .card-actions {
    padding: var(--space-2) var(--space-3);
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
    background: var(--card-bg);
    border-top: 1px solid var(--border);
    margin-top: auto;
  }

  .btn-compact.btn {
    padding: .3rem .55rem;
    font-size: .82rem;
  }

  /* ====== Centrado ====== */
  .wrap-center {
    max-width: var(--wrap-max);
    margin-inline: auto;
  }

  /* ====== Stats ====== */
  .stats-bar {
    display: flex;
    gap: .6rem;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-3);
  }

  .stat-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .35rem .6rem;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: .45rem;
  }

  .stat-item i {
    color: var(--brand);
    font-size: .95rem;
  }

  .stat-value {
    font-weight: 600;
    color: #e9ecef;
    line-height: 1;
  }

  /* ====== Upload ====== */
  .upload-zone {
    background: var(--card-bg);
    border: 2px dashed var(--border);
    border-radius: .75rem;
    padding: 1.25rem;
    margin-bottom: var(--space-3);
    transition: border-color .2s, background .2s, box-shadow .2s;
    min-height: 230px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .upload-inner {
    width: 100%;
    max-width: 900px;
    margin-inline: auto;
  }

  .upload-zone:hover {
    border-color: #007bff;
    background: #2c3136;
    box-shadow: 0 4px 12px rgba(0, 123, 255, .15);
  }

  .upload-zone.dragover {
    border-color: #28a745;
    background: #1e3a28;
  }

  .upload-title {
    font-weight: 600;
    margin-bottom: .25rem;
    color: #e9ecef;
  }

  .upload-help {
    font-size: .95rem;
    color: #adb5bd;
    margin-bottom: .75rem;
  }

  /* ====== Toolbar ====== */
  .toolbar {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: .6rem;
    padding: .6rem;
    margin-bottom: var(--space-3);
    box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
  }

  .toolbar .btn {
    padding: .3rem .6rem;
    font-size: .84rem;
  }

  .toolbar .actions-center {
    display: flex;
    gap: .5rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  .row.g-3 {
    --bs-gutter-x: .75rem;
    --bs-gutter-y: .75rem;
  }

  /* Responsive improvements */
  @media (max-width: 768px) {
    .image-zone img {
      height: clamp(120px, 35vw, 160px);
    }

    .toolbar,
    .upload-zone {
      padding: .6rem;
    }

    .card-actions {
      gap: .35rem;
    }

    .upload-zone {
      min-height: 200px;
    }

    .stats-bar {
      gap: .4rem;
    }

    .stat-item {
      padding: .3rem .5rem;
      font-size: .85rem;
    }
  }

  @media (max-width: 576px) {
    .image-zone img {
      height: clamp(110px, 40vw, 140px);
    }

    .caption-zone {
      padding: var(--space-2);
    }

    .card-actions {
      padding: var(--space-2);
    }

    .btn-compact.btn {
      padding: .25rem .4rem;
      font-size: .75rem;
    }
  }
</style>
@endpush

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 wrap-center">
  <div>
    <h1 class="mb-1" style="font-size:clamp(1.05rem, 2.2vw, 1.4rem)">{{ __('m_tours.image.ui.page_heading') }}</h1>
    <p class="text-muted mb-0" style="font-size:.9rem">{{ $tour->name }}</p>
  </div>
  <a href="{{ route('admin.tours.images.pick') }}" class="btn btn-warning btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('m_tours.image.ui.back_btn') }}
  </a>
</div>
@stop

@section('content')
@php
$images = $tour->getRelation('images') ?? $tour->images()->orderBy('position')->get();
$count = $images->count();
$isFull = $count >= $max;
@endphp

<div class="container-fluid py-3 wrap-center">
  {{-- Stats --}}
  <div class="stats-bar">
    <div class="stat-item">
      <i class="fas fa-images"></i>
      <div>
        <div class="stat-value">{{ $count }} / {{ $max }}</div>
        <small class="text-muted">{{ __('m_tours.image.ui.stats_images') }}</small>
      </div>
    </div>
    <div class="stat-item">
      <i class="fas fa-star"></i>
      <div>
        <div class="stat-value">{{ $tour->coverImage ? '1' : '0' }}</div>
        <small class="text-muted">{{ __('m_tours.image.ui.stats_cover') }}</small>
      </div>
    </div>
    <div class="stat-item">
      <i class="fas fa-check-circle"></i>
      <div>
        <div class="stat-value" id="selectedCount">0</div>
        <small class="text-muted">{{ __('m_tours.image.ui.stats_selected') }}</small>
      </div>
    </div>
  </div>

  {{-- Upload --}}
  @can('create-tour-images')
  <div class="upload-zone" id="uploadZone">
    <form action="{{ route('admin.tours.images.store', $tour) }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="upload-inner">
      @csrf
      <div class="mb-2">
        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
        <div class="upload-title">{{ __('m_tours.image.ui.drag_or_click') }}</div>
        <div class="upload-help">{{ __('m_tours.image.ui.upload_help') }}</div>
      </div>

      <input type="file" name="files[]" id="imageFiles" class="d-none" multiple accept="image/*">

      <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('imageFiles').click()" {{ $isFull ? 'disabled' : '' }}>
        <i class="fas fa-folder-open me-1"></i> {{ __('m_tours.image.ui.select_btn') }}
      </button>

      @if($isFull)
      <div class="alert alert-warning py-1 px-2 d-inline-flex align-items-center ms-2 mb-0" style="font-size:.85rem;">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ __('m_tours.image.ui.limit_badge', ['max' => $max]) }}
      </div>
      @endif

      <div id="filePreview" class="mt-3" style="font-size:.92rem;"></div>

      <button type="submit" id="uploadBtn" class="btn btn-success btn-sm mt-2" style="display:none;">
        <i class="fas fa-upload me-1"></i> {{ __('m_tours.image.ui.upload_btn') }}
      </button>
    </form>
  </div>
  @endcan

  {{-- Toolbar --}}
  @if($count > 0)
  @can('delete-tour-images')
  <div class="toolbar">
    <div class="row align-items-center g-2">
      <div class="col-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
        <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" id="selectAll">
          <label class="form-check-label fw-semibold" for="selectAll">{{ __('m_tours.image.ui.select_all') }}</label>
        </div>
      </div>
      <div class="col-12 col-md-7">
        <div class="actions-center">
          <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" disabled>
            <i class="fas fa-trash me-1"></i> {{ __('m_tours.image.ui.delete_selected') }}
          </button>
          <button type="button" id="deleteAllBtn" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-trash-alt me-1"></i> {{ __('m_tours.image.ui.delete_all') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  @endcan
  @endif

  {{-- Grid --}}
  <div class="row g-3">
    @forelse($images as $image)
    @php $chkId = 'imgsel-'.$image->id; @endphp
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <div class="card shadow-sm image-card">

        {{-- Nuevo selector visible --}}
        <div class="image-select" title="{{ __('m_tours.image.ui.select_image_title') }}">
          <input id="{{ $chkId }}" type="checkbox" class="img-check" value="{{ $image->id }}" aria-label="{{ __('m_tours.image.ui.select_image_aria', ['id' => $image->id]) }}">
          <label for="{{ $chkId }}" class="image-check-label"></label>
        </div>

        @if($image->is_cover)
        <div class="cover-badge">
          <span class="badge bg-success"><i class="fas fa-star me-1"></i>{{ __('m_tours.image.ui.cover_label') }}</span>
        </div>
        @endif

        <div class="image-zone" onclick="preview('{{ $image->url }}', '{{ e($image->caption) }}')">
          <img src="{{ $image->url }}" alt="img-{{ $image->id }}" loading="lazy">
        </div>

        <div class="caption-zone">
          <input
            type="text"
            class="caption-input"
            placeholder="{{ __('m_tours.image.ui.caption_placeholder') }}"
            value="{{ $image->caption }}"
            data-id="{{ $image->id }}"
            data-url="{{ route('admin.tours.images.update', [$tour, $image]) }}">
          <div class="caption-status"></div>
        </div>

        <div class="card-actions">
          <button type="button" class="btn btn-outline-info btn-compact w-auto flex-grow-1"
            onclick="preview('{{ $image->url }}', '{{ e($image->caption) }}')">
            <i class="fas fa-eye me-1"></i> {{ __('m_tours.image.ui.show_btn') }}
          </button>
          @unless($image->is_cover)
          <button type="button" class="btn btn-success btn-compact w-auto flex-grow-1 set-cover"
            data-url="{{ route('admin.tours.images.cover', [$tour, $image]) }}">
            <i class="fas fa-star me-1"></i> {{ __('m_tours.image.ui.cover_btn') }}
          </button>
          @endunless
          @can('delete-tour-images')
          <button type="button" class="btn btn-danger btn-compact w-auto flex-grow-1 delete-img"
            data-url="{{ route('admin.tours.images.destroy', [$tour, $image]) }}">
            <i class="fas fa-trash me-1"></i> {{ __('m_tours.image.ui.delete_btn') }}
          </button>
          @endcan
        </div>
      </div>
    </div>
    @empty
    <div class="col-12">
      <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i> {{ __('m_tours.image.ui.no_images') }}
      </div>
    </div>
    @endforelse
  </div>

</div>

{{-- Modal Preview --}}
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h5 class="modal-title">{{ __('m_tours.image.ui.preview_title') }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_tours.image.ui.close_btn') }}"></button>
      </div>
      <div class="modal-body p-0 text-center">
        <img id="previewImg" src="" alt="" class="img-fluid" style="max-height:80vh;object-fit:contain;">
      </div>
      <div class="modal-footer border-0">
        <p class="text-muted mb-0 me-auto" id="previewCaption"></p>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('m_tours.image.ui.close_btn') }}</button>
      </div>
    </div>
  </div>
</div>

{{-- Forms --}}
<form id="setCoverForm" method="POST" style="display:none;">@csrf</form>
<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="bulkDeleteForm" action="{{ route('admin.tours.images.bulk-destroy', $tour) }}" method="POST" style="display:none;">
  @csrf @method('DELETE')
  <input type="hidden" name="ids" id="bulkIds">
</form>
<form id="deleteAllForm" action="{{ route('admin.tours.images.destroyAll', $tour) }}" method="POST" style="display:none;">
  @csrf @method('DELETE')
</form>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const i18n = {
    toast_success: @json(__('m_tours.image.ui.success_title')),
    toast_error: @json(__('m_tours.image.ui.error_title')),
    toast_warning: @json(__('m_tours.image.ui.warning_title')),

    saving: @json(__('m_tours.image.ui.saving_label', [], false) ?? '...'),
    saved: @json(__('m_tours.image.saved')),
    save_ok: @json(__('m_tours.image.caption_updated')),
    save_error: @json(__('m_tours.image.errors.update_caption')),
    none: @json(__('m_tours.image.ui.none_label', [], false) ?? ''),
    limit_word: @json(__('m_tours.image.ui.limit_word', [], false) ?? 'Límite'),

    // Confirmaciones
    confirm_set_cover_title: @json(__('m_tours.image.ui.confirm_set_cover_title', [], false) ?? '¿Establecer como portada?'),
    confirm_set_cover_text: @json(__('m_tours.image.ui.confirm_set_cover_text', [], false) ?? 'Esta imagen será la principal del tour'),
    confirm_delete_title: @json(__('m_tours.image.ui.confirm_delete_title')),
    confirm_delete_text: @json(__('m_tours.image.ui.confirm_delete_text')),
    confirm_btn: @json(__('m_tours.image.ui.confirm_btn', [], false) ?? 'Sí'),
    cancel_btn: @json(__('m_tours.image.ui.cancel_btn')),
  };

  const toast = (icon, title) => Swal.fire({
    toast: true,
    position: 'top-end',
    icon,
    title,
    showConfirmButton: false,
    timer: 3000
  });
  const confirm = (title, text) => Swal.fire({
    title,
    text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: i18n.confirm_btn,
    cancelButtonText: i18n.cancel_btn,
    confirmButtonColor: '#dc3545'
  });

  let previewModal;
  document.addEventListener('DOMContentLoaded', () => {
    previewModal = new bootstrap.Modal('#previewModal');
    initUpload();
    initCaptions();
    initSelection();
    initActions();
  });

  function preview(url, caption) {
    document.getElementById('previewImg').src = url;
    document.getElementById('previewCaption').textContent = caption || i18n.none;
    previewModal.show();
  }

  // Upload
  function initUpload() {
    const zone = document.getElementById('uploadZone');
    const input = document.getElementById('imageFiles');
    const preview = document.getElementById('filePreview');
    const btn = document.getElementById('uploadBtn');
    const MAX_MB = 100;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => {
      zone.addEventListener(e, ev => {
        ev.preventDefault();
        ev.stopPropagation();
      }, false);
    });
    ['dragenter', 'dragover'].forEach(e => zone.addEventListener(e, () => zone.classList.add('dragover')));
    ['dragleave', 'drop'].forEach(e => zone.addEventListener(e, () => zone.classList.remove('dragover')));

    zone.addEventListener('drop', e => {
      input.files = e.dataTransfer.files;
      handleFiles(input.files);
    });
    input.addEventListener('change', e => handleFiles(e.target.files));

    function handleFiles(files) {
      if (!files.length) {
        preview.innerHTML = '';
        btn?.style && (btn.style.display = 'none');
        return;
      }
      const arr = Array.from(files);
      const totalMB = (arr.reduce((s, f) => s + f.size, 0) / 1024 / 1024).toFixed(2);
      const over = totalMB > MAX_MB;

      preview.innerHTML = `<div class="alert alert-${over?'danger':'light'} py-2 px-3 mb-0 border">
      <i class="fas fa-${over?'exclamation-triangle text-danger':'images text-secondary'} me-2"></i>
      <strong>${arr.length}</strong> ${@json(__('m_tours.image.ui.files_word', [], false) ?? 'archivos')} (<strong>${totalMB} MB</strong> / ${MAX_MB} MB)
      ${over ? '<br><strong>WARNING ' + i18n.limit_word + '</strong>' : ''}
    </div>`;
      if (btn) btn.style.display = over ? 'none' : 'inline-block';
    }
  }

  // Auto-save captions
  function initCaptions() {
    let timer;
    document.querySelectorAll('.caption-input').forEach(input => {
      const status = input.nextElementSibling;
      const url = input.dataset.url;

      input.addEventListener('input', () => {
        clearTimeout(timer);
        status.innerHTML = '<i class="fas fa-clock text-muted"></i> ' + (i18n.saving || '{{ __('
          m_tours.image.ui.saving_fallback ', [], false) ?? '
          Guardando...' }}');
        timer = setTimeout(() => saveCaption(input, url, status), 900);
      });
      input.addEventListener('blur', () => {
        clearTimeout(timer);
        saveCaption(input, url, status);
      });
    });

    function saveCaption(input, url, status) {
      const fd = new FormData();
      fd.append('_token', '{{ csrf_token() }}');
      fd.append('_method', 'PATCH');
      fd.append('caption', input.value);

      fetch(url, {
          method: 'POST',
          body: fd
        })
        .then(r => r.ok ? r.json().catch(() => ({})) : Promise.reject())
        .then(() => {
          status.innerHTML = '<i class="fas fa-check text-success"></i> ' + i18n.saved;
          setTimeout(() => status.innerHTML = '', 1600);
        })
        .catch(() => {
          status.innerHTML = '<i class="fas fa-times text-danger"></i> ' + i18n.toast_error;
          toast('error', i18n.save_error);
        });
    }
  }

  // Selection
  function initSelection() {
    const all = document.getElementById('selectAll');
    const checks = document.querySelectorAll('.img-check');
    const counter = document.getElementById('selectedCount');
    const bulkBtn = document.getElementById('bulkDeleteBtn');

    function update() {
      const selected = Array.from(checks).filter(c => c.checked);
      counter.textContent = selected.length;
      if (bulkBtn) bulkBtn.disabled = selected.length === 0;

      checks.forEach(c => {
        const card = c.closest('.image-card');
        if (!card) return;
        card.classList.toggle('selected', c.checked);
      });

      if (all) {
        if (selected.length === 0) {
          all.checked = false;
          all.indeterminate = false;
        } else if (selected.length === checks.length) {
          all.checked = true;
          all.indeterminate = false;
        } else {
          all.checked = false;
          all.indeterminate = true;
        }
      }
    }
    all?.addEventListener('change', () => {
      checks.forEach(c => c.checked = all.checked);
      update();
    });
    checks.forEach(c => c.addEventListener('change', update));
    update();
  }

  // Actions
  function initActions() {
    // Set cover
    document.querySelectorAll('.set-cover').forEach(btn => {
      btn.addEventListener('click', function() {
        confirm(i18n.confirm_set_cover_title, i18n.confirm_set_cover_text).then(r => {
          if (r.isConfirmed) {
            document.getElementById('setCoverForm').action = this.dataset.url;
            document.getElementById('setCoverForm').submit();
          }
        });
      });
    });

    // Delete individual
    document.querySelectorAll('.delete-img').forEach(btn => {
      btn.addEventListener('click', function() {
        confirm(i18n.confirm_delete_title, i18n.confirm_delete_text).then(r => {
          if (r.isConfirmed) {
            document.getElementById('deleteForm').action = this.dataset.url;
            document.getElementById('deleteForm').submit();
          }
        });
      });
    });

    // Bulk delete
    document.getElementById('bulkDeleteBtn')?.addEventListener('click', () => {
      const selected = Array.from(document.querySelectorAll('.img-check:checked'));
      confirm(@json(__('m_tours.image.ui.confirm_bulk_delete_title', [], false) ?? '¿Eliminar seleccionadas?'), @json(__('m_tours.image.ui.confirm_bulk_delete_text', [], false) ?? 'Se eliminarán :count imágenes')).then(r => {
        if (r.isConfirmed) {
          document.getElementById('bulkIds').value = selected.map(c => c.value).join(',');
          document.getElementById('bulkDeleteForm').submit();
        }
      });
    });

    // Delete all
    document.getElementById('deleteAllBtn')?.addEventListener('click', () => {
      confirm(@json(__('m_tours.image.ui.confirm_delete_all_title', [], false) ?? '¿Eliminar TODAS?'), @json(__('m_tours.image.ui.confirm_delete_all_text', [], false) ?? 'Se eliminarán todas las imágenes del tour')).then(r => {
        if (r.isConfirmed) document.getElementById('deleteAllForm').submit();
      });
    });
  }

  // Flash
  @if(session('success')) toast('success', @json(__('m_tours.image.done').
    ': '.session('success')));
  @endif
  @if(session('error')) toast('error', @json(__('m_tours.image.ui.error_title').
    ': '.session('error')));
  @endif
  @if($errors-> any())
  Swal.fire({
    icon: 'error',
    title: @json(__('m_tours.image.ui.error_title')),
    html: '<ul style="text-align:left;">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul>'
  });
  @endif
</script>
@endpush
