<div class="languages-schedules-box p-3 shadow rounded bg-white border">
  <h3 class="mb-3 fw-bold">{{ __('adminlte::adminlte.tour_information') }}</h3>

  <h4>{{ __('adminlte::adminlte.duration') }}</h4>
  <p>{{ $tour->length }} {{ __('adminlte::adminlte.hours') }}</p>

  <h4>{{ __('adminlte::adminlte.group_size') }}</h4>
  <p>{{ $tour->max_capacity }}</p>

  <h4>{{ __('adminlte::adminlte.languages_available') }}</h4>
  <p class="badges-group">
    @foreach($tour->languages as $lang)
      <span class="badge bg-secondary mb-1">{{ $lang->name }}</span>
    @endforeach
  </p>

  <h4>{{ __('adminlte::adminlte.schedules') }}</h4>
  <p class="badges-group">
    @foreach($tour->schedules->sortBy('start_time') as $schedule)
      <span class="badge bg-success mb-1">
        {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
      </span>
    @endforeach
  </p>
</div>
