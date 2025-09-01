@extends('adminlte::page')

@section('title', 'Booking Settings')

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  @if(session('success'))
    Swal.fire({icon:'success',title:'Success',text:@json(session('success')),timer:2000,showConfirmButton:false});
  @endif
  @if($errors->any())
    Swal.fire({icon:'error',title:'Error',html:@json(implode('<br>', $errors->all()))});
  @endif
</script>
@endpush

@section('content')
<div class="container-fluid">
  <h2 class="mb-4">Booking Settings</h2>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.settings.booking.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Cutoff hour (24h)</label>
            <input type="time" class="form-control" name="cutoff_hour" value="{{ old('cutoff_hour', $cutoff) }}" required>
            <small class="text-muted">Ej: 18:00 (después de esta hora, “mañana” deja de estar disponible).</small>
          </div>

          <div class="col-md-4">
            <label class="form-label">Lead days</label>
            <input type="number" class="form-control" name="lead_days" value="{{ old('lead_days', $lead) }}" min="0" max="30" required>
            <small class="text-muted">Días mínimos de antelación si aún no se pasó el cutoff.</small>
          </div>

          <div class="col-md-4">
            <label class="form-label">Timezone</label>
            <input type="text" class="form-control" value="{{ $tz }}" disabled>
            <small class="text-muted">Se toma de config('app.timezone').</small>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
