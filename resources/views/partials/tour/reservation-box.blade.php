@include('partials.tour.reservation-form', [
  'tour'              => $tour,
  'hotels'            => $hotels,
  'blockedGeneral'    => $blockedGeneral ?? [],
  'blockedBySchedule' => $blockedBySchedule ?? [],
  'fullyBlockedDates' => $fullyBlockedDates ?? [],
])
