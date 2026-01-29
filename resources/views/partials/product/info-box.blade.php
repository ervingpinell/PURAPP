<div class="languages-schedules-box p-3 shadow-sm rounded bg-white border">
  <h3 class="section-subtitle mb-3">{{ __('adminlte::adminlte.product_information') }}</h3>

  <h4 class="mt-3">{{ __('adminlte::adminlte.duration') }}</h4>
  <p>{{ $product->length }} {{ __('adminlte::adminlte.hours') }}</p>

  @if($product->group_size)
    <h4 class="mt-3">{{ __('adminlte::adminlte.group_size') }}</h4>
    <p>{{ $product->group_size }} {{ __('adminlte::adminlte.persons_max') ?? 'personas máximo' }}</p>
  @endif

  <h4 class="mt-3">{{ __('adminlte::adminlte.languages_available') }}</h4>
  <p class="badges-group">
    @forelse($product->languages as $lang)
      <span class="badge bg-success mb-1">{{ $lang->name }}</span>
    @empty
      <span class="text-muted">—</span>
    @endforelse
  </p>

  <h4 class="mt-3">{{ __('adminlte::adminlte.schedules') }}</h4>
  <p class="badges-group">
    @forelse($product->schedules->sortBy('start_time') as $schedule)
      <span class="badge bg-success me-1 mb-1">
        {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
      </span>
    @empty
      <span class="text-muted">—</span>
    @endforelse
  </p>
</div>
