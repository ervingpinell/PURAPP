@include('partials.product.reservation.form', [
  'product'              => $product,
  'hotels'            => $hotels,
  'meetingPoints'     => $meetingPoints,
  'blockedGeneral'    => $blockedGeneral ?? [],
  'blockedBySchedule' => $blockedBySchedule ?? [],
  'fullyBlockedDates' => $fullyBlockedDates ?? [],
])
