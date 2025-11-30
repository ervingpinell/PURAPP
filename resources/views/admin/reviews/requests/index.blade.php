@extends('adminlte::page')

@section('title', __('reviews.requests.index_title'))

@section('content_header')
  <h1 class="mb-0">{{ __('reviews.requests.index_title') }}</h1>
  <small class="text-muted">{{ __('reviews.requests.subtitle') }}</small>
@stop

@section('content')
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @php $tab = request('tab','eligible'); @endphp
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link {{ $tab === 'eligible' ? 'active' : '' }}"
         href="{{ route('admin.review-requests.index', array_merge(request()->query(), ['tab' => 'eligible', 'page' => null])) }}">
        {{ __('reviews.requests.tabs.eligible') }}
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ $tab === 'requested' ? 'active' : '' }}"
         href="{{ route('admin.review-requests.index', array_merge(request()->query(), ['tab' => 'requested', 'page' => null])) }}">
        {{ __('reviews.requests.tabs.requested') }}
      </a>
    </li>
  </ul>

  {{-- Filtros --}}
  <form method="GET" class="mb-3">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="form-row align-items-end">
      <div class="col-sm-3 mb-2">
        <label>{{ __('reviews.common.search') }}</label>
        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('reviews.requests.filters.q_placeholder') }}">
      </div>
      <div class="col-sm-2 mb-2">
        <label>{{ __('reviews.requests.tour_id') }}</label>
        <input type="number" name="tour_id" class="form-control" value="{{ request('tour_id') }}" placeholder="{{ __('reviews.requests.tour_id') }}">
      </div>

      @if($tab === 'eligible')
        <div class="col-sm-2 mb-2">
          <label>{{ __('reviews.requests.window_days') }}</label>
          <input type="number" name="days" class="form-control" value="{{ $daysWindow }}" min="7" max="180" />
        </div>
        <div class="col-sm-2 mb-2">
          <label>{{ __('reviews.requests.date_column') }}</label>
          <input type="text" class="form-control" value="{{ $dateCol }}" disabled>
        </div>
        <div class="col-sm-3 mb-2">
          <label>{{ __('reviews.requests.calculated_range') }}</label>
          <div class="form-control bg-light">{{ $from }} → {{ $to }}</div>
        </div>
      @else
        <div class="col-sm-2 mb-2">
          <label>{{ __('reviews.common.status') }}</label>
          <select name="status" class="form-control">
            <option value="">{{ __('reviews.requests.filters.any_status') }}</option>
            <option value="active"    {{ request('status')==='active'   ? 'selected':'' }}>{{ __('reviews.requests.status.active') }}</option>
            <option value="sent"      {{ request('status')==='sent'     ? 'selected':'' }}>{{ __('reviews.requests.status.sent') }}</option>
            <option value="reminded"  {{ request('status')==='reminded' ? 'selected':'' }}>{{ __('reviews.requests.status.reminded') }}</option>
            <option value="used"      {{ request('status')==='used'     ? 'selected':'' }}>{{ __('reviews.requests.status.used') }}</option>
            <option value="expired"   {{ request('status')==='expired'  ? 'selected':'' }}>{{ __('reviews.requests.status.expired') }}</option>
            <option value="cancelled" {{ request('status')==='cancelled'? 'selected':'' }}>{{ __('reviews.requests.status.cancelled') }}</option>
          </select>
        </div>
        <div class="col-sm-2 mb-2">
          <label>{{ __('reviews.requests.filters.from') }}</label>
          <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-sm-2 mb-2">
          <label>{{ __('reviews.requests.filters.to') }}</label>
          <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
      @endif

      <div class="col-auto mb-2">
        <button class="btn btn-secondary">{{ __('reviews.common.filter') }}</button>
        <a class="btn btn-outline-secondary" href="{{ route('admin.review-requests.index', ['tab'=>$tab]) }}">{{ __('reviews.common.clear') }}</a>
      </div>
    </div>
  </form>

  {{-- Tablas --}}
  @if($tab === 'eligible')
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead>
          <tr>
            <th style="width:110px;">{{ __('reviews.admin.table.date') }}</th>
            <th style="width:160px;">{{ __('reviews.requests.table.reference') }}</th>
            <th>{{ __('reviews.admin.table.client') }}</th>
            <th>{{ __('reviews.admin.table.tour') }}</th>
            <th class="text-right" style="width:200px;">{{ __('reviews.common.actions') }}</th>
          </tr>
          </thead>
          <tbody>
          @forelse($bookings as $b)
            @php
              $ref = \Illuminate\Support\Facades\Schema::hasColumn($b->getTable(),'booking_reference') ? ($b->booking_reference ?? null) : null;
            @endphp
            <tr>
              <td>{{ \Illuminate\Support\Carbon::parse($b->{$dateCol} ?? $b->created_at)->toDateString() }}</td>
              <td>
                @if($ref)
                  <code>{{ $ref }}</code>
                @else
                  #{{ $b->booking_id }}
                @endif
                <small class="text-muted d-block">({{ $b->status }})</small>
              </td>
              <td>
                {{ optional($b->user)->full_name ?? $b->customer_name }}
                <small class="text-muted d-block">{{ optional($b->user)->email ?? $b->customer_email }}</small>
              </td>
              <td>
                {{ optional($b->tour)->name }}
                <small class="text-muted d-block">ID: {{ $b->tour_id }}</small>
              </td>
              <td class="text-right">
                <form method="POST" action="{{ route('admin.review-requests.send', $b) }}">
                  @csrf
                  <div class="input-group input-group-sm" style="max-width: 220px; float:right;">
                    <input type="number" name="expires_in_days" value="30" min="1" max="120" class="form-control" title="{{ __('reviews.requests.labels.expires_in_days') }}">
                    <div class="input-group-append">
                      <button class="btn btn-primary">{{ __('reviews.requests.btn_request') }}</button>
                    </div>
                  </div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-3">{{ __('reviews.requests.no_eligible') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3">{{ $bookings->withQueryString()->links() }}</div>
  @else
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead>
          <tr>
            <th style="width:110px;">{{ __('reviews.requests.table.sent_at') }}</th>
            <th>{{ __('reviews.admin.table.client') }}</th>
            <th>{{ __('reviews.admin.table.tour') }}</th>
            <th style="width:160px;">{{ __('reviews.requests.table.reference') }}</th>
            <th style="width:220px;">{{ __('reviews.requests.table.states') }}</th>
            <th class="text-right" style="width:210px;">{{ __('reviews.common.actions') }}</th>
          </tr>
          </thead>
          <tbody>
          @forelse($requests as $r)
            @php
              $has = fn($col) => \Illuminate\Support\Facades\Schema::hasColumn($r->getTable(), $col);
              $badge = function($txt,$cls){ return '<span class="badge badge-'.$cls.'">'.$txt.'</span>'; };
              $ref = optional($r->booking)->booking_reference ?? null;
              $states = [];
              if ($has('status') && $r->status)    $states[] = $badge(e(__("reviews.requests.status_labels.".$r->status)), 'info');
              if ($has('used_at') && $r->used_at)  $states[] = $badge(__('reviews.requests.status.used'), 'success');
              if ($has('cancelled_at') && $r->cancelled_at) $states[] = $badge(__('reviews.requests.status.cancelled'), 'secondary');
              if ($has('expires_at') && $r->expires_at) {
                  $states[] = $r->expires_at->isPast()
                      ? $badge(__('reviews.requests.status.expired'), 'danger')
                      : $badge(__('reviews.requests.status.active'), 'primary');
              }
              if (empty($states)) $states[] = $badge(__('reviews.requests.status.sent'), 'info');
            @endphp
            <tr>
              <td>
                {{ optional($r->sent_at ?? $r->created_at)->format('d-M-Y') }}
                <div><small class="text-muted">{{ optional($r->sent_at ?? $r->created_at)->format('H:i') }}</small></div>
              </td>
              <td>
                {{ optional($r->user)->full_name ?? $r->customer_name }}
                <small class="text-muted d-block">{{ $r->email }}</small>
              </td>
              <td>
                {{ optional($r->tour)->name }}
                <small class="text-muted d-block">ID: {{ $r->tour_id }}</small>
              </td>
              <td>
                @if($ref)
                  <code>{{ $ref }}</code>
                @else
                  #{{ $r->booking_id }}
                @endif
              </td>
              <td>
                {!! implode(' ', $states) !!}
                @if($has('expires_at') && $r->expires_at)
                  <div><small class="text-muted">{{ __('reviews.requests.labels.expires_at') }}: {{ $r->expires_at->toDateString() }}</small></div>
                @endif
                @if($has('used_at') && $r->used_at)
                  <div><small class="text-muted">{{ __('reviews.requests.labels.used_at') }}: {{ $r->used_at->toDateString() }}</small></div>
                @endif
              </td>
              <td class="text-right">
                <form method="POST" action="{{ route('admin.review-requests.resend', $r) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary"
                          @if(\Illuminate\Support\Facades\Schema::hasColumn($r->getTable(),'expires_at') && $r->expires_at && $r->expires_at->isPast()) disabled @endif>
                    {{ __('reviews.requests.actions.resend') }}
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.review-requests.destroy', $r) }}" class="d-inline" onsubmit="return confirm(@js(__('reviews.requests.actions.confirm_delete')));">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ __('reviews.common.delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-3">{{ __('reviews.requests.none') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3">{{ $requests->withQueryString()->links() }}</div>
  @endif
@stop
