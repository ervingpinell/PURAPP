@include('partials.tour.reservation.form', [
  'tour'              => $tour,
  'hotels'            => $hotels,
  'meetingPoints'     => $meetingPoints,
  'blockedGeneral'    => $blockedGeneral ?? [],
  'blockedBySchedule' => $blockedBySchedule ?? [],
  'fullyBlockedDates' => $fullyBlockedDates ?? [],
])
