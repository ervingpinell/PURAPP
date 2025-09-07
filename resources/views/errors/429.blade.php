{{-- resources/views/errors/429.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
  /** @var \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface|null $exception */

  $serverNowTs    = now()->getTimestamp();
  $retryHeaderSec = (int) ($exception?->getHeaders()['Retry-After'] ?? 0);
  $serverUntilTs  = $retryHeaderSec > 0 ? ($serverNowTs + $retryHeaderSec) : null;

  $queryUntilTs = (int) request()->query('until', 0);

  if ($queryUntilTs > 0 && $serverUntilTs) {
      // No alargamos: usamos el menor de ambos
      $untilTs = min($queryUntilTs, $serverUntilTs);
  } elseif ($queryUntilTs > 0) {
      $untilTs = $queryUntilTs;
  } elseif ($serverUntilTs) {
      $untilTs = $serverUntilTs;
  } else {
      // Fallback: 10 minutos
      $untilTs = $serverNowTs + (int) (session('seconds') ?? 600);
  }

  $total = max(0, $untilTs - $serverNowTs);
  $mins  = intdiv($total, 60);
  $secs  = $total % 60;
@endphp

@section('title', __('auth.throttle_page.title'))

@section('auth_header')
  <h1 class="text-danger text-center">
    <i class="fas fa-exclamation-triangle"></i>
    {{ __('auth.throttle_page.title') }}
  </h1>
@stop

@section('auth_body')
  <div class="alert alert-danger text-center">
    <p class="mb-2">
      {{ __('auth.throttle_page.message') }}
    </p>

    <p class="mt-2" id="retry-block"
       data-until="{{ $untilTs }}"
       data-login-url="{{ route('login') }}"
       data-throttled-url="{{ route('auth.throttled') }}">
      ⏳ {{ __('auth.throttle_page.retry_in') }}
      <strong id="mm">{{ $mins }}</strong>{{ __('auth.throttle_page.minutes_abbr') }}
      <strong id="ss">{{ str_pad($secs, 2, '0', STR_PAD_LEFT) }}</strong>{{ __('auth.throttle_page.seconds_abbr') }}
    </p>
  </div>
@stop

@section('auth_footer')
  <div class="text-center">
    <a href="{{ route('login') }}" class="btn btn-primary">
      <i class="fas fa-sign-in-alt"></i> {{ __('adminlte::auth.back_to_login') }}
    </a>
  </div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const block = document.getElementById('retry-block');
  if (!block) return;

  const KEY          = 'auth.throttle.until';
  const untilAttrSec = parseInt(block.dataset.until || '0', 10);
  const throttledURL = block.dataset.throttledUrl;
  const loginURL     = block.dataset.loginUrl;

  const mmEl = document.getElementById('mm');
  const ssEl = document.getElementById('ss');

  const serverUntilMs = untilAttrSec * 1000;
  const nowMs         = Date.now();

  let storedUntilMs = parseInt(localStorage.getItem(KEY) || '0', 10);
  let untilMs;

  if (!storedUntilMs || storedUntilMs <= nowMs) {
    // No hay contador previo o ya venció → usamos el del servidor
    untilMs = serverUntilMs;
  } else {
    // Hay uno activo: NUNCA lo alargamos; tomamos el menor
    untilMs = Math.min(storedUntilMs, serverUntilMs);
  }

  localStorage.setItem(KEY, String(untilMs));

  // Reescribimos la URL a /auth/throttled?until=...
  if (window.history && window.history.replaceState) {
    const untilParam = Math.floor(untilMs / 1000);
    const url = new URL(throttledURL, window.location.origin);
    url.searchParams.set('until', String(untilParam));
    window.history.replaceState({}, document.title, url.toString());
  }

  function render(remainingSec) {
    const m = Math.floor(remainingSec / 60);
    const s = remainingSec % 60;
    if (mmEl) mmEl.textContent = String(m);
    if (ssEl) ssEl.textContent = String(s).padStart(2, '0');
  }

  function tick() {
    const remainingSec = Math.max(0, Math.round((untilMs - Date.now()) / 1000));
    render(remainingSec);

    if (remainingSec <= 0) {
      localStorage.removeItem(KEY);
      window.location.href = loginURL;
      return;
    }
    setTimeout(tick, 1000);
  }

  tick();
});
</script>
@endpush
