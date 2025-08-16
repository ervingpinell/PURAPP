@extends('adminlte::page') {{-- o tu layout admin --}}
@section('title', __('Manage Tour Images'))

@section('content')
@php
    /** @var \Illuminate\Support\Collection $imagesRel */
    $imagesRel = $tour->getRelation('images') ?? collect();
    $countRel  = $imagesRel->count();
    $isFull    = $countRel >= $max;
@endphp

<div class="container py-3">
  <h3 class="mb-1">{{ __('Manage images for') }}: {{ $tour->getTranslatedName() }}</h3>
  <p class="text-muted mb-3">{{ $countRel }} / {{ $max }} {{ __('images') }}</p>

  {{-- Subir imágenes --}}
  <form action="{{ route('admin.tours.images.store', $tour) }}" method="POST" enctype="multipart/form-data" class="mb-4">
    @csrf
    <input type="file"
           name="files[]"
           class="form-control @error('files') is-invalid @enderror"
           multiple
           accept="image/png,image/jpeg,image/webp">
    @error('files')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <button class="btn btn-success mt-2" {{ $isFull ? 'disabled' : '' }}>
      <i class="fas fa-upload me-1"></i> {{ __('Upload') }}
    </button>
    @if($isFull)
      <small class="text-danger ms-2">{{ __('Image limit reached for this tour.') }}</small>
    @endif
  </form>

  {{-- Grid --}}
  <div class="row g-3" id="grid"
       data-reorder-url="{{ route('admin.tours.images.reorder', $tour) }}">
    @forelse($imagesRel as $img)
      <div class="col-6 col-sm-4 col-md-3 col-xl-2" data-id="{{ $img->id }}">
        <div class="card shadow-sm h-100">
          <div class="ratio ratio-1x1">
            <img src="{{ $img->getAttribute('url') }}"
                 alt="img {{ $img->id }}"
                 class="card-img-top"
                 style="object-fit:cover;">
          </div>
          <div class="card-body p-2">
            @if($img->is_cover)
              <span class="badge bg-success">{{ __('Cover') }}</span>
            @endif

            {{-- Caption inline (opcional) --}}
            <form action="{{ route('admin.tours.images.update', [$tour, $img]) }}"
                  method="POST" class="mt-2">
              @csrf @method('PATCH')
              <input type="text" name="caption"
                     value="{{ old('caption', $img->caption) }}"
                     class="form-control form-control-sm"
                     placeholder="{{ __('Caption (optional)') }}">
              <button class="btn btn-outline-secondary btn-sm w-100 mt-1">
                {{ __('Save') }}
              </button>
            </form>

            <div class="d-grid gap-1 mt-2">
              @unless($img->is_cover)
                <form action="{{ route('admin.tours.images.cover', [$tour, $img]) }}"
                      method="POST">
                  @csrf
                  <button class="btn btn-outline-success btn-sm w-100">
                    <i class="fas fa-star me-1"></i> {{ __('Set cover') }}
                  </button>
                </form>
              @endunless

              <form action="{{ route('admin.tours.images.destroy', [$tour, $img]) }}"
                    method="POST"
                    onsubmit="return confirmAdminImageDelete(event, this);">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm w-100">
                  <i class="fas fa-trash me-1"></i> {{ __('Delete') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info mb-0">{{ __('No images yet for this tour.') }}</div>
      </div>
    @endforelse
  </div>
</div>
@endsection

@push('js')
  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Confirmación SweetAlert para eliminar
    function confirmAdminImageDelete(e, formEl) {
      e.preventDefault(); e.stopPropagation();

      Swal.fire({
        title: @json(__('Delete this image?')),
        text:  @json(__('This action cannot be undone.')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: @json(__('Delete')),
        cancelButtonText:  @json(__('Cancel')),
        confirmButtonColor: '#dc3545'
      }).then(res => {
        if (res.isConfirmed) {
          // pequeña animación en el botón
          const btn = formEl.querySelector('button[type="submit"]');
          if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __('Deleting...') }}'; }
          formEl.submit();
        }
      });

      return false;
    }

    // Mostrar SweetAlert por mensajes flash (creado, eliminado, portada, caption)
    document.addEventListener('DOMContentLoaded', () => {
      @if (session('swal'))
        Swal.fire(@json(session('swal')));
      @elseif (session('success'))
        Swal.fire({
          icon: 'success',
          title: @json(session('success')),
          timer: 1800,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      @elseif (session('error'))
        Swal.fire({
          icon: 'error',
          title: @json(session('error')),
          timer: 2200,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });
      @endif
    });
  </script>
@endpush
