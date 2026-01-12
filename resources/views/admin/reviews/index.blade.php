{{-- resources/views/admin/reviews/index.blade.php --}}
@extends('adminlte::page')

@php use Illuminate\Support\Str; @endphp

@section('title', __('reviews.admin.index_title'))

@section('content_header')
<h1><i class="fas fa-star"></i> {{ __('reviews.admin.index_title') }}</h1>
@stop

@section('content')
@if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="card">
  <div class="card-body">
    {{-- Filtros --}}
    <form class="row g-2 mb-3">
      {{-- Provider Filter --}}
      <div class="col-md-2">
        <select name="provider" class="form-control">
          @foreach($providers as $p)
          <option value="{{ $p->slug }}" @selected(($selectedProvider ?? 'local' )===$p->slug)>
            {{ $p->name }}
          </option>
          @endforeach
        </select>
      </div>

      {{-- Only show these filters for local provider --}}
      @if(($selectedProvider ?? 'local') === 'local')
      <div class="col-md-2">
        <select name="status" class="form-control">
          <option value="">{{ __('reviews.admin.filters.status') }}</option>
          @foreach(['pending','published','hidden','flagged'] as $st)
          <option value="{{ $st }}" @selected(request('status')===$st)>{{ __('reviews.status.'.$st) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <input type="number" name="tour_id" class="form-control" value="{{ request('tour_id') }}" placeholder="{{ __('reviews.admin.filters.tour_id') }}">
      </div>

      <div class="col-md-1">
        <select name="stars" class="form-control">
          <option value="">{{ __('reviews.admin.filters.stars') }}</option>
          @for($i=5;$i>=1;$i--)
          <option @selected(request('stars')==$i)>{{ $i }}</option>
          @endfor
        </select>
      </div>

      {{-- Filtro: respondido --}}
      <div class="col-md-2">
        <select name="responded" class="form-control">
          <option value="">{{ __('reviews.admin.filters.responded') }}</option>
          <option value="yes" @selected(request('responded')==='yes' )>{{ __('reviews.common.yes') }}</option>
          <option value="no" @selected(request('responded')==='no' )>{{ __('reviews.common.no') }}</option>
        </select>
      </div>

      <div class="col-md-3">
        <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('reviews.admin.filters.q') }}">
      </div>

      <div class="col-md-2">
        <button class="btn btn-primary btn-block">{{ __('reviews.common.filter') }}</button>
      </div>
      @else
      {{-- For external providers, just show the filter button --}}
      <div class="col-md-10">
        <div class="alert alert-info mb-0">
          <i class="fas fa-info-circle"></i>
          {{ __('reviews.admin.external_provider_note') }}
        </div>
      </div>
      @endif
    </form>

    <div class="d-flex justify-content-between mb-2">
      <div>
        @can('moderate-reviews')
        <a class="btn btn-success" href="{{ route('admin.reviews.create') }}">
          <i class="fa fa-plus mr-1"></i> {{ __('reviews.admin.new_local') }}
        </a>
        @endcan
      </div>

      <form method="post" action="{{ route('admin.reviews.bulk') }}" id="bulkForm" class="d-flex gap-2">
        @csrf
        <select name="action" class="form-control form-control-sm mr-2" style="max-width:220px">
          {{-- Opciones protegidas visualmente, aunque controlador debe validar también --}}
          <option value="publish">{{ __('reviews.common.publish') }}</option>
          <option value="hide">{{ __('reviews.common.hide') }}</option>
          <option value="flag">{{ __('reviews.common.flag') }}</option>
          <option value="delete">{{ __('reviews.common.delete') }}</option>
        </select>
        @can('moderate-reviews')
        <button class="btn btn-primary btn-sm">{{ __('reviews.admin.bulk_apply') }}</button>
        @endcan
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr class="text-nowrap">
            @if(($selectedProvider ?? 'local') === 'local')
            <th width="25"><input type="checkbox" id="chkAll"></th>
            @endif
            <th>{{ __('reviews.common.id') }}</th>
            <th>{{ __('reviews.common.provider') }}</th>
            <th>{{ __('reviews.common.tour') }}</th>
            @if(($selectedProvider ?? 'local') === 'local')
            <th>{{ __('reviews.requests.table.reference') }}</th>
            @endif
            <th>⭐</th>
            <th style="min-width:420px;">{{ __('reviews.common.title') }} / {{ __('reviews.common.body') }}</th>
            <th>{{ __('reviews.common.author') }}</th>
            @if(($selectedProvider ?? 'local') === 'local')
            <th>{{ __('reviews.common.status') }}</th>
            <th>{{ __('reviews.thread.replies_header') }}</th>
            @endif
            <th>{{ __('reviews.common.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reviews as $r)
          @php
          $isExternal = isset($r->is_external) && $r->is_external;
          $ref = !$isExternal ? (optional($r->booking)->booking_reference ?? optional($r->booking)->reference ?? null) : null;
          $bkid = !$isExternal ? (optional($r->booking)->booking_id ?? null) : null;
          @endphp
          <tr>
            @if(!$isExternal && ($selectedProvider ?? 'local') === 'local')
            <td><input class="chk" type="checkbox" form="bulkForm" name="ids[]" value="{{ $r->id }}"></td>
            @endif
            <td>{{ $r->id ?? $r->provider_review_id ?? '—' }}</td>
            <td>
              <span class="badge badge-{{ $isExternal ? 'info' : 'primary' }}">
                {{ ucfirst($r->provider ?? 'local') }}
              </span>
            </td>
            <td>{{ $r->tour_id ?? $r->tour_name ?? '—' }}</td>
            @if(!$isExternal && ($selectedProvider ?? 'local') === 'local')
            <td>
              @if($ref)
              <code>{{ $ref }}</code>
              @elseif($bkid)
              #{{ $bkid }}
              @else
              —
              @endif
              @endif
            <td>{{ $r->rating }}</td>
            <td class="text-truncate" style="max-width:520px">
              {{ $r->title ?: Str::limit($r->body, 140) }}
            </td>
            <td>{{ $r->author_name ?? '—' }}</td>
            @if(!$isExternal && ($selectedProvider ?? 'local') === 'local')
            <td>
              <span class="badge badge-secondary text-uppercase">{{ __('reviews.status.'.$r->status) }}</span>
              @if(isset($r->is_public) && !$r->is_public)
              <span class="badge badge-warning ml-1">{{ __('reviews.common.private') }}</span>
              @endif
            </td>

            {{-- Columna: respuestas --}}
            <td>
              @if(($r->replies_count ?? 0) > 0)
              <span class="badge badge-success">{{ __('reviews.common.yes') }} ({{ $r->replies_count }})</span>
              @php $last = $r->replies_max_created_at ?? null; @endphp
              @if($last)
              <div class="small text-muted">{{ __('reviews.replies.last_reply') }} {{ \Illuminate\Support\Carbon::parse($last)->diffForHumans() }}</div>
              @endif
              @else
              <span class="badge badge-secondary">{{ __('reviews.common.no') }}</span>
              @endif
            </td>
            @endif

            <td class="text-nowrap">
              @if($isExternal)
              <span class="text-muted small"><i class="fas fa-lock"></i> Solo lectura</span>
              @else
              @can('moderate-reviews')
              <a class="btn btn-xs btn-edit" href="{{ route('admin.reviews.edit',$r) }}" title="{{ __('reviews.common.edit') }}"><i class="fa fa-edit"></i></a>

              @if($r->status!=='published')
              <form method="post" action="{{ route('admin.reviews.publish',$r) }}" class="d-inline">@csrf
                <button class="btn btn-xs btn-success" title="{{ __('reviews.common.publish') }}"><i class="fa fa-upload"></i></button>
              </form>
              @else
              <form method="post" action="{{ route('admin.reviews.hide',$r) }}" class="d-inline">@csrf
                <button class="btn btn-xs btn-warning" title="{{ __('reviews.common.hide') }}"><i class="fa fa-eye-slash"></i></button>
              </form>
              @endif
              @endcan

              {{-- Responder --}}
              @can('reply-reviews')
              <a href="{{ route('admin.reviews.replies.create', $r) }}" class="btn btn-xs btn-primary" title="{{ __('reviews.replies.reply') }}">
                <i class="fa fa-reply"></i>
              </a>
              @endcan

              {{-- Ver hilo (si hay) -- Visibilidad pública o básica --}}
              @if(($r->replies_count ?? 0) > 0)
              <a class="btn btn-xs btn-secondary"
                href="{{ route('admin.reviews.replies.thread', $r) }}"
                title="{{ __('reviews.thread.replies_header') }}">
                <i class="fa fa-comments"></i>
              </a>
              @endif

              @can('moderate-reviews')
              <form method="post" action="{{ route('admin.reviews.flag',$r) }}" class="d-inline">@csrf
                <button class="btn btn-xs btn-info" title="{{ __('reviews.common.flag') }}"><i class="fa fa-flag"></i></button>
              </form>
              @endcan

              @can('delete-reviews')
              <form method="post" action="{{ route('admin.reviews.destroy',$r) }}" class="d-inline" onsubmit="return confirm('{{ __('reviews.common.delete') }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger" title="{{ __('reviews.common.delete') }}"><i class="fa fa-trash"></i></button>
              </form>
              @endcan
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end">
      {{ $reviews->links() }}
    </div>
  </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('chkAll')?.addEventListener('change', e => {
    document.querySelectorAll('.chk').forEach(c => c.checked = e.target.checked);
  });

  @if(session('ok'))
  Swal.fire({
    icon: 'success',
    title: @json(session('ok'))
  });
  @endif
  @if(session('error'))
  Swal.fire({
    icon: 'error',
    title: @json(session('error'))
  });
  @endif
</script>
@stop