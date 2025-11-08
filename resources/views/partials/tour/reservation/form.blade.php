@php
use Carbon\Carbon;
use App\Models\AppSetting;

$tz      = config('app.timezone', 'America/Costa_Rica');
$gCutoff = (string) AppSetting::get('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
$gLead   = (int)    AppSetting::get('booking.lead_days', (int) config('booking.lead_days', 1));

$calc = function (string $cutoff, int $lead) use ($tz) {
    $now = Carbon::now($tz);
    [$hh,$mm] = array_pad(explode(':', $cutoff, 2), 2, '00');
    $cutoffToday = Carbon::create($now->year, $now->month, $now->day, (int)$hh, (int)$mm, 0, $tz);
    $passed = $now->gte($cutoffToday);
    $days   = max(0, (int)$lead) + ($passed ? 1 : 0);
    return [
      'cutoff'       => sprintf('%02d:%02d', (int)$hh, (int)$mm),
      'lead_days'    => (int)$lead,
      'after_cutoff' => $passed,
      'min'          => $now->copy()->addDays($days)->toDateString(),
    ];
};

$tCutoff  = $tour->cutoff_hour ?: $gCutoff;
$tLead    = is_null($tour->lead_days) ? $gLead : (int) $tour->lead_days;
$tourRule = $calc($tCutoff, $tLead);

$scheduleRules = [];
foreach ($tour->schedules->sortBy('start_time') as $s) {
    $pCut = optional($s->pivot)->cutoff_hour;
    $pLd  = optional($s->pivot)->lead_days;
    $sCut = $pCut ?: $tCutoff;
    $sLd  = is_null($pLd) ? $tLead : (int)$pLd;
    $scheduleRules[$s->schedule_id] = $calc($sCut, $sLd);
}

$mins = array_map(fn($r) => $r['min'], $scheduleRules);
$mins[] = $tourRule['min'];
$initialMin = min($mins);

$rulesPayload = [
  'tz'        => $tz,
  'tour'      => $tourRule,
  'schedules' => $scheduleRules,
  'initialMin'=> $initialMin,
];

// Obtener categorías activas para data attributes (con nombre traducido)
$activeCategories = $tour->prices()
    ->where('is_active', true)
    ->whereHas('category', fn($q) => $q->where('is_active', true))
    ->with('category')
    ->orderBy('category_id')
    ->get();

$categoriesDataAttr = $activeCategories->map(function($priceRecord) {
    $cat = $priceRecord->category;
    $translatedName = optional($cat)->getTranslatedName() ?? ($cat->name ?? 'Category');
    return [
        'id'    => (int) $priceRecord->category_id,
        'slug'  => $cat?->slug ?? \Illuminate\Support\Str::slug($translatedName),
        'label' => $translatedName, // 👉 nombre traducido disponible para el JS
        'price' => (float) $priceRecord->price,
        'min'   => (int) $priceRecord->min_quantity,
        'max'   => (int) $priceRecord->max_quantity,
    ];
})->values()->toJson();

$maxPersonsGlobal = (int) config('booking.max_persons_per_booking', 12);
@endphp

<form action="{{ route('public.carts.add') }}" method="POST"
  class="reservation-box gv-ui is-compact is-compact-2 p-3 shadow-sm rounded bg-white mb-4 border"
  data-categories="{{ e($categoriesDataAttr) }}"
  data-max-total="{{ $maxPersonsGlobal }}"
>
  @csrf
  <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

  @include('partials.tour.reservation.header')

  <div class="form-body position-relative">
    <fieldset @guest disabled aria-disabled="true" @endguest>
      @include('partials.tour.reservation.travelers')
      @include('partials.tour.reservation.fields')

      {{-- Hidden fields para otros datos --}}
      <input type="hidden" name="is_other_hotel" id="isOtherHotel" value="0">
      <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint">
      <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint">
    </fieldset>
  </div>

  @include('partials.tour.reservation.cta')
</form>

@include('partials.tour.reservation.assets', ['rulesPayload' => $rulesPayload])
