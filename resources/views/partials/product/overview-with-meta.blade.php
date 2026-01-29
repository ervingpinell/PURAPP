<div class="tour-header-section mb-4">
  <h1 class="tour-title mb-3">{{ $product->translated_name }}</h1>
  
  {{-- Inline meta badges --}}
  <div class="tour-meta-badges mb-3">
    @if($product->length)
      <span class="badge-meta">
        <i class="fas fa-clock"></i> {{ $product->length }} {{ __('adminlte::adminlte.hours') }}
      </span>
    @endif
    
    @if($product->group_size)
      <span class="badge-meta">
        <i class="fas fa-users"></i> {{ __('adminlte::adminlte.small_groups') }} ({{ $product->group_size }} {{ __('adminlte::adminlte.persons_max') }})
      </span>
    @endif
    
    @if($product->languages->count() > 0)
      <span class="badge-meta">
        <i class="fas fa-language"></i> 
        {{ $product->languages->pluck('name')->join(', ') }}
      </span>
    @endif
    
    @if($product->schedules->count() > 0)
      <span class="badge-meta">
        <i class="fas fa-calendar-check"></i>
        @foreach($product->schedules->sortBy('start_time')->take(2) as $schedule)
          {{ date('g:i A', strtotime($schedule->start_time)) }}@if(!$loop->last), @endif
        @endforeach
        @if($product->schedules->count() > 2)
          <span class="text-muted">+{{ $product->schedules->count() - 2 }}</span>
        @endif
      </span>
    @endif
    
    {{-- Optional highlight badge --}}
    @if($product->featured || $product->best_value)
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
    {!! nl2br(e($product->translated_overview)) !!}
  </div>
</div>
