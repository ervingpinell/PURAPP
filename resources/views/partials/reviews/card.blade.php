@php
  /** @var array $r */
  $rating = (int) ($r['rating'] ?? 0);
  $rating = max(0, min(5, $rating));
  $stars  = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

  $author = $r['author_name'] ?? __('reviews.anonymous_guest');
  $date   = !empty($r['date']) ? \Illuminate\Support\Carbon::parse($r['date'])->isoFormat('ll') : '';
  $title  = trim((string)($r['title'] ?? ''));
  $body   = trim((string)($r['body'] ?? ''));

  $provKey   = strtolower((string)($r['provider'] ?? ''));
  $provLabel = $r['provider_name'] ?? match ($provKey) {
      'local'        => config('app.name', 'Green Vacations CR'),
      'viator'       => 'Viator',
      'google'       => 'Google',
      'gyg'          => 'GetYourGuide',
      'getyourguide' => 'GetYourGuide',
      'tripadvisor', 'ta' => 'Tripadvisor',
      default        => ($provKey ? ucfirst($provKey) : '—'),
  };
@endphp

<div class="review-item"
     data-prov-key="{{ $provKey }}"
     data-prov-label="{{ $provLabel }}"
     style="display: {{ !empty($active) ? '' : 'none' }};">

  <div class="review-body-wrapper">
    <div class="review-header">
      <div class="review-meta">
        <div class="review-author">{{ $author }}</div>
        @if($date)<div class="review-date">{{ $date }}</div>@endif
      </div>
    </div>

    <div class="review-stars" aria-label="{{ $rating }}/5">
      {!! $stars !!} <span class="rating-number">({{ $rating }}/5)</span>
    </div>

    @if($title !== '')
      <div class="review-label">{{ $title }}</div>
    @endif

    @if($body !== '')
      <div class="review-content">
        <p>{{ $body }}</p>
      </div>
    @endif
  </div>
</div>
