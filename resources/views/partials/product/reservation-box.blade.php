@include('partials.product.reservation.form', [
  'tour'              => $product,
  'hotels'            => $hotels,
  'meetingPoints'     => $meetingPoints,
  'blockedGeneral'    => $blockedGeneral ?? [],
  'blockedBySchedule' => $blockedBySchedule ?? [],
  'fullyBlockedDates' => $fullyBlockedDates ?? [],
])
