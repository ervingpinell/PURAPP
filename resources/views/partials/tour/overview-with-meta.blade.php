<div class="tour-header-section mb-4">
  <h1 class="tour-title mb-3">{{ $tour->translated_name }}</h1>
  
  {{-- Inline meta badges --}}
  <div class="tour-meta-badges mb-3">
    @if($tour->length)
      <span class="badge-meta">
        <i class="fas fa-clock"></i> {{ $tour->length }} {{ __('adminlte::adminlte.hours') }}
      </span>
    @endif
    
    @if($tour->group_size)
      <span class="badge-meta">
        <i class="fas fa-users"></i> {{ __('adminlte::adminlte.small_groups') }} ({{ $tour->group_size }} {{ __('adminlte::adminlte.persons_max') }})
      </span>
    @endif
    
    @if($tour->languages->count() > 0)
      <span class="badge-meta">
        <i class="fas fa-language"></i> 
        {{ $tour->languages->pluck('name')->join(', ') }}
      </span>
    @endif
    
    @if($tour->schedules->count() > 0)
      <span class="badge-meta">
        <i class="fas fa-calendar-check"></i>
        @foreach($tour->schedules->sortBy('start_time')->take(2) as $schedule)
          {{ date('g:i A', strtotime($schedule->start_time)) }}@if(!$loop->last), @endif
        @endforeach
        @if($tour->schedules->count() > 2)
          <span class="text-muted">+{{ $tour->schedules->count() - 2 }}</span>
        @endif
      </span>
    @endif
    
    {{-- Optional highlight badge --}}
    @if($tour->featured || $tour->best_value)
      <span class="badge-meta badge-highlight">
        <i class="fas fa-star"></i> {{ __('adminlte::adminlte.best_value') }}
      </span>
    @endif
  </div>

  {{-- Large group contact message --}}
  @php
    $maxPersonsGlobal = $maxPersonsGlobal ?? 12;
  @endphp
  <div class="alert alert-warning mt-3 mb-3 small">
    <i class="fas fa-info-circle me-2"></i>
    {{ __('m_bookings.large_group_message', ['max' => $maxPersonsGlobal]) }}
    <a href="{{ localized_route('contact') }}" class="alert-link fw-bold small">
      {{ __('m_bookings.contact_us_link') }}
    </a>
  </div>

  {{-- Overview section --}}
  <h2 class="section-subtitle mb-2">{{ __('adminlte::adminlte.overview') }}</h2>
  <div class="tour-overview-text">
    {!! nl2br(e($tour->translated_overview)) !!}
  </div>
</div>
