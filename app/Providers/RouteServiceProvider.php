<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

RateLimiter::for('capacity-admin', function (Request $r) {
    $who = optional($r->user())->user_id ?? $r->ip();
    return Limit::perMinute(120)->by($who);
});

RateLimiter::for('capacity-details', function (Request $r) {
    $who = optional($r->user())->user_id ?? $r->ip();
    return Limit::perMinute(240)->by($who);
});

RateLimiter::for('tours-admin', fn(Request $r) =>
    Limit::perMinute(120)->by(optional($r->user())->user_id ?? $r->ip())
);

RateLimiter::for('admin-light', fn(Request $r) =>
    Limit::perMinute(120)->by(optional($r->user())->user_id ?? $r->ip())
);
