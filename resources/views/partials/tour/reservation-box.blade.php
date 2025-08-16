{{-- Este partial es un contenedor sencillo que reenvía las props al form --}}
@include('partials.tour.reservation-form', [
  'tour'              => $tour,
  'hotels'            => $hotels,
  'blockedGeneral'    => $blockedGeneral ?? [],
  'blockedBySchedule' => $blockedBySchedule ?? [],
  'fullyBlockedDates' => $fullyBlockedDates ?? [],
])
