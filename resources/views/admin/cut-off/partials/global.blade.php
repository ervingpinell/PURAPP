<div class="card shadow-sm">
  <div class="card-body">
    @if($errors->any())
    <div class="alert alert-danger">
      <strong>Errores de validación:</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form method="POST" action="{{ url('admin/tours/cutoff') }}" id="form-global">
      @csrf
      <input type="hidden" name="_method" value="PUT">

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('m_config.cut-off.fields.cutoff_hour') }}</label>
          <input type="time" name="cutoff_hour"
            class="form-control @error('cutoff_hour') is-invalid @enderror"
            value="{{ old('cutoff_hour', $cutoff) }}" required
            pattern="^(?:[01]\d|2[0-3]):[0-5]\d$">
          @error('cutoff_hour') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          <div class="form-hint mt-1">
            {!! __('m_config.cut-off.hints.cutoff_example', ['ex'=>'<span class="kbd">18:00</span>']) !!}
          </div>
          <div class="form-hint">{{ __('m_config.cut-off.hints.pattern_24h') }}</div>
          <div class="form-hint">{{ __('m_config.cut-off.hints.cutoff_behavior') }}</div>
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('m_config.cut-off.fields.lead_days') }}</label>
          <input type="number" name="lead_days"
            class="form-control @error('lead_days') is-invalid @enderror"
            value="{{ old('lead_days', $lead) }}" min="0" max="30" required>
          @error('lead_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.lead_days') }}</div>
          <div class="form-hint">{{ __('m_config.cut-off.hints.lead_days_detail') }}</div>
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('m_config.cut-off.fields.timezone') }}</label>
          <input type="text" class="form-control" value="{{ $tz }}" disabled>
          <div class="form-hint mt-1"><code>config('app.timezone')</code></div>
          <div class="form-hint">{{ __('m_config.cut-off.hints.timezone_source') }}</div>
        </div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save me-1"></i> {{ __('m_config.cut-off.actions.save_global') }}
        </button>
      </div>
    </form>
  </div>
</div>