{{-- resources/views/admin/tours/partials/tab-schedules.blade.php --}}

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">{{ __('m_tours.tour.schedules_form.available_title') }}</h3>
        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCreateSchedule">
          <i class="fas fa-plus"></i> {{ __('m_tours.schedule.ui.create_quick') }}
        </button>
      </div>

      <div class="card-body">
        <div class="form-group">
          <label>{{ __('m_tours.tour.schedules_form.select_hint') }}</label>

          @php
            $existingSchedules = $tour ? $tour->schedules->pluck('schedule_id')->toArray() : [];
          @endphp

          @forelse($schedules ?? [] as $schedule)
            <div class="custom-control custom-checkbox mb-2">
              <input
                type="checkbox"
                class="custom-control-input"
                id="schedule_{{ $schedule->schedule_id }}"
                name="schedules[]"
                value="{{ $schedule->schedule_id }}"
                {{ in_array($schedule->schedule_id, old('schedules', $existingSchedules)) ? 'checked' : '' }}
              >
              <label class="custom-control-label" for="schedule_{{ $schedule->schedule_id }}">
                <strong>
                  {{ date('g:i A', strtotime($schedule->start_time)) }}
                  -
                  {{ date('g:i A', strtotime($schedule->end_time)) }}
                </strong>
                @if($schedule->label)
                  <span class="badge badge-info">{{ $schedule->label }}</span>
                @endif
              </label>
            </div>
          @empty
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('m_tours.tour.schedules_form.no_schedules') }}
              <a href="{{ route('admin.tours.schedule.index') }}" target="_blank" class="alert-link">
                {{ __('m_tours.tour.schedules_form.create_schedules_link') }}
              </a>
            </div>
          @endforelse
        </div>

        @error('schedules')
          <div class="alert alert-danger">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Crear horario nuevo - SIEMPRE VISIBLE --}}
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-plus"></i>
          {{ __('m_tours.tour.schedules_form.create_new_title') }}
        </h3>
      </div>

      <div class="card-body">
        <div class="row">
          {{-- Hora inicio --}}
          <div class="col-md-4">
            <div class="form-group">
              <label for="new_schedule_start">{{ __('m_tours.schedule.fields.start_time') }}</label>
              <input
                type="time"
                name="new_schedule[start_time]"
                id="new_schedule_start"
                class="form-control"
                value="{{ old('new_schedule.start_time') }}"
              >
            </div>
          </div>

          {{-- Hora fin --}}
          <div class="col-md-4">
            <div class="form-group">
              <label for="new_schedule_end">{{ __('m_tours.schedule.fields.end_time') }}</label>
              <input
                type="time"
                name="new_schedule[end_time]"
                id="new_schedule_end"
                class="form-control"
                value="{{ old('new_schedule.end_time') }}"
              >
            </div>
          </div>

          {{-- Etiqueta opcional --}}
          <div class="col-md-4">
            <div class="form-group">
              <label for="new_schedule_label">{{ __('m_tours.schedule.fields.label_optional') }}</label>
              <input
                type="text"
                name="new_schedule[label]"
                id="new_schedule_label"
                class="form-control"
                placeholder="{{ __('m_tours.tour.schedules_form.label_placeholder') }}"
                value="{{ old('new_schedule.label') }}"
              >
            </div>
          </div>
        </div>

        <div class="custom-control custom-checkbox">
          <input
            type="checkbox"
            class="custom-control-input"
            id="new_schedule_create"
            name="new_schedule[create]"
            value="1"
            {{ old('new_schedule.create') ? 'checked' : '' }}
          >
          <label class="custom-control-label" for="new_schedule_create">
            <strong>{{ __('m_tours.tour.schedules_form.create_and_assign') }}</strong>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- Columna lateral --}}
  <div class="col-md-4">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle"></i> {{ __('m_tours.tour.schedules_form.info_title') }}
        </h3>
      </div>
      <div class="card-body">
        <h5>{{ __('m_tours.tour.schedules_form.schedules_title') }}</h5>
        <p class="small">
          {{ __('m_tours.tour.schedules_form.schedules_text') }}
        </p>
        <hr>
        <h5>{{ __('m_tours.tour.schedules_form.create_block_title') }}</h5>
        <p class="small mb-0">
          {{ __('m_tours.tour.schedules_form.create_block_text') }}
        </p>
      </div>
    </div>

    @if($tour ?? false)
      <div class="card card-secondary">
        <div class="card-header">
          <h3 class="card-title">{{ __('m_tours.tour.schedules_form.current_title') }}</h3>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($tour->schedules as $schedule)
              <li class="list-group-item">
                <strong>
                  {{ date('g:i A', strtotime($schedule->start_time)) }}
                  -
                  {{ date('g:i A', strtotime($schedule->end_time)) }}
                </strong>
                @if($schedule->label)
                  <br><small class="text-muted">{{ $schedule->label }}</small>
                @endif
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('m_tours.tour.schedules_form.none_assigned') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    @endif
  </div>
</div>
