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
  <li class="nav-item">
    <a class="nav-link {{ $tab === 'trash' ? 'active' : '' }}" href="{{ route('admin.review-requests.index', ['tab'=>'trash']) }}">Papelera</a>
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
      <label>{{ __('reviews.requests.product_id') }}</label>
      <input type="number" name="product_id" class="form-control" value="{{ request('product_id') }}" placeholder="{{ __('reviews.requests.product_id') }}">
    </div>

    @if($tab === 'eligible')
    <div class="col-sm-2 mb-2">
      <label>{{ __('reviews.requests.filters.from') }}</label>
      <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>
    <div class="col-sm-2 mb-2">
      <label>{{ __('reviews.requests.filters.to') }}</label>
      <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>
    <div class="col-sm-2 mb-2">
      <label>{{ __('reviews.requests.date_column') }}</label>
      <select name="date_col" class="form-control">
        <option value="created_at" {{ ($dateCol ?? 'created_at') === 'created_at' ? 'selected' : '' }}>{{ __('reviews.requests.date_options.created_at') }}</option>
        <option value="start_date" {{ ($dateCol ?? '') === 'start_date' ? 'selected' : '' }}>{{ __('reviews.requests.date_options.tour_date') }}</option>
      </select>
    </div>
    @else
    <div class="col-sm-2 mb-2">
      <label>{{ __('reviews.common.status') }}</label>
      <select name="status" class="form-control">
        <option value="">{{ __('reviews.requests.filters.any_status') }}</option>
        <option value="active" {{ request('status')==='active'   ? 'selected':'' }}>{{ __('reviews.requests.status.active') }}</option>
        <option value="sent" {{ request('status')==='sent'     ? 'selected':'' }}>{{ __('reviews.requests.status.sent') }}</option>
        <option value="reminded" {{ request('status')==='reminded' ? 'selected':'' }}>{{ __('reviews.requests.status.reminded') }}</option>
        <option value="used" {{ request('status')==='used'     ? 'selected':'' }}>{{ __('reviews.requests.status.used') }}</option>
        <option value="expired" {{ request('status')==='expired'  ? 'selected':'' }}>{{ __('reviews.requests.status.expired') }}</option>
        <option value="cancelled" {{ request('status')==='cancelled'? 'selected':'' }}>{{ __('reviews.requests.status.cancelled') }}</option>
        <option value="skipped" {{ request('status')==='skipped'? 'selected':'' }}>{{ __('reviews.requests.status.skipped') }}</option>
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
          <th class="text-center" style="width:120px;">{{ __('reviews.requests.table.expires_days') }}</th>
          <th class="text-right" style="width:140px;">{{ __('reviews.common.actions') }}</th>
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
            <small class="text-muted d-block">ID: {{ $b->product_id }}</small>
          </td>
          <td class="text-center align-middle">
            @can('create-review-requests')
            <form method="POST" action="{{ route('admin.review-requests.send', $b) }}" class="d-inline">
              @csrf
              <input type="number" name="expires_in_days" value="30" min="1" max="120" class="form-control form-control-sm" style="width:70px; display:inline-block;" title="{{ __('reviews.requests.labels.expires_in_days') }}">
            @endcan
          </td>
          <td class="text-right align-middle">
            @can('create-review-requests')
              <button class="btn btn-primary btn-sm">{{ __('reviews.requests.btn_request') }}</button>
            </form>

            <form method="POST" action="{{ route('admin.review-requests.discard', $b) }}" class="d-inline ml-1">
                @csrf
                <button class="btn btn-outline-secondary btn-sm" title="{{ __('reviews.requests.actions.discard') }}">
                    {{ __('reviews.requests.actions.discard') }}
                </button>
            </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted p-3">{{ __('reviews.requests.no_eligible') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $bookings->withQueryString()->links() }}</div>
@elseif($tab === 'requested')
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
        if ($has('status') && $r->status) {
           $color = 'info';
           if($r->status === 'skipped') $color = 'secondary';
           $states[] = $badge(e(__("reviews.requests.status_labels.".$r->status)), $color);
        }
        if ($has('used_at') && $r->used_at) $states[] = $badge(__('reviews.requests.status.used'), 'success');
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
            <small class="text-muted d-block">ID: {{ $r->product_id }}</small>
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
          <td class="text-right align-middle">
            <div class="btn-group btn-group-sm">
            @can('edit-review-requests')
              {{-- Actions for Sent/Reminded --}}
              @if(in_array($r->status, ['sent', 'reminded']))
                <form method="POST" action="{{ route('admin.review-requests.resend', $r) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-primary" title="{{ __('reviews.requests.actions.resend') }}">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                {{-- SKIP Action --}}
                <form method="POST" action="{{ route('admin.review-requests.skip', $r) }}" class="d-inline ml-1">
                    @csrf
                    <button class="btn btn-outline-secondary" title="{{ __('reviews.requests.actions.discard') }}">
                        <i class="fas fa-ban"></i>
                    </button>
                </form>
              @endif
            @endcan

            @can('delete-review-requests')
              <form method="POST" action="{{ route('admin.review-requests.destroy', $r) }}" class="d-inline ml-1">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger delete-review-request-btn" title="{{ __('reviews.common.delete') }}">
                      <i class="fas fa-trash"></i>
                  </button>
              </form>
            @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted p-3">{{ __('reviews.requests.no_requests') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>

@elseif($tab === 'trash')
<div class="card">
  <div class="card-body p-0">
    <table class="table table-sm mb-0">
      <thead>
        <tr>
          <th style="width:110px;">{{ __('reviews.requests.table.sent_at') }}</th>
          <th>{{ __('reviews.admin.table.client') }}</th>
          <th>{{ __('reviews.admin.table.tour') }}</th>
          <th style="width:160px;">{{ __('reviews.requests.table.reference') }}</th>
          <th class="text-right" style="width:210px;">{{ __('reviews.common.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $r)
        @php
           $sentAt = $r->sent_at ?? $r->created_at;
        @endphp
        <tr>
          <td>{{ \Illuminate\Support\Carbon::parse($sentAt)->toDateString() }}</td>
          <td>
             {{ optional($r->user)->full_name ?? $r->email }}
             <small class="text-muted d-block">{{ $r->email }}</small>
          </td>
          <td>
             {{ optional($r->tour)->name }}
             <small class="text-muted d-block">ID: {{ $r->product_id }}</small>
          </td>
          <td>
             <code>{{ optional($r->booking)->booking_reference }}</code>
          </td>
          <td class="text-right align-middle">
            @can('edit-review-requests')
              <form method="POST" action="{{ route('admin.review-requests.restore', $r->id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-success" title="Restaurar">
                      <i class="fas fa-trash-restore"></i> Restaurar
                  </button>
              </form>
            @endcan
            @can('delete-review-requests')
              <form method="POST" action="{{ route('admin.review-requests.destroy-perm', $r->id) }}" class="d-inline ml-1">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger delete-perm-review-request-btn" title="Eliminar Permanentemente">
                      <i class="fas fa-times"></i>
                  </button>
              </form>
            @endcan
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted p-3">Papelera vacía.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endif
@stop

@section('js')
<script>
$(document).ready(function() {
    // SweetAlert for delete review request
    $('.delete-review-request-btn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: @js(__('reviews.requests.sweetalert.delete_title')),
            text: @js(__('reviews.requests.sweetalert.delete_text')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: @js(__('reviews.requests.sweetalert.delete_confirm')),
            cancelButtonText: @js(__('reviews.requests.sweetalert.delete_cancel'))
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // SweetAlert for PERMANENT delete review request
    $('.delete-perm-review-request-btn').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@stop