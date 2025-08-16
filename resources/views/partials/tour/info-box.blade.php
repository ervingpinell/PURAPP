<div class="languages-schedules-box p-3 shadow-sm rounded bg-white border" style="min-height: 280px;">

  <h3 class="mb-3 fw-bold border-bottom pb-1 d-inline-block" style="color:#f92526; font-size: 1.2rem;">
    {{ __('adminlte::adminlte.tour_information') }}
  </h3>

  <h4 class="mt-3">{{ __('adminlte::adminlte.duration') }}</h4>
  <p>{{ $tour->length }} {{ __('adminlte::adminlte.hours') }}</p>

  <h4 class="mt-3">{{ __('adminlte::adminlte.group_size') }}</h4>
  <p>{{ $tour->max_capacity }}</p>

  <h4 class="mt-3">{{ __('adminlte::adminlte.languages_available') }}</h4>
  <p class="badges-group">
    @foreach($tour->languages as $lang)
      <span class="badge bg-success mb-1">{{ $lang->name }}</span>
    @endforeach
  </p>

  <h4 class="mt-3">{{ __('adminlte::adminlte.schedules') }}</h4>
  <p class="badges-group">
    @foreach($tour->schedules->sortBy('start_time') as $schedule)
      <span class="badge bg-success me-1 mb-1">
        {{ date('g:i A', strtotime($schedule->start_time)) }} - {{ date('g:i A', strtotime($schedule->end_time)) }}
      </span>
    @endforeach
  </p>

  {{-- 🔗 Enlaces que abren el modal dinámico (sin data-bs-target) --}}
<h4 class="mt-3">{{ __('adminlte::adminlte.policies') }}</h4>
<p class="badges-group mb-0">
  <a href="#" class="fw-semibold policy-link d-block mb-1"
     data-policy="cancelacion"
     style="text-decoration: underline; color: var(--primary-dark) !important;">
    {{ __('Ver política de cancelación') }}
  </a>

  <a href="#" class="fw-semibold policy-link d-block"
     data-policy="reembolso"
     style="text-decoration: underline; color: var(--primary-dark) !important;">
    {{ __('Ver política de reembolsos') }}
  </a>
</p>


</div>

{{-- Modal de políticas (incluye UNA sola vez en la página) --}}
@include('partials.tour.modal-policies')
