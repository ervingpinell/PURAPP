@extends('adminlte::page')

@section('title', __('reviews.thread.title', ['id' => $review->id]))

@section('content_header')
  <h1><i class="fas fa-comments"></i> {{ __('reviews.thread.header', ['id' => $review->id]) }}</h1>
@stop

@section('content')
  @if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <div>
          <div class="mb-1">
            <span class="badge badge-info text-uppercase">{{ $review->provider }}</span>
            <span class="ml-2">{{ __('reviews.common.tour') }} #{{ $review->product_id }}</span>
            <span class="ml-2">⭐ {{ $review->rating }}</span>
          </div>
          @if($review->title)<div class="mb-2"><strong>{{ $review->title }}</strong></div>@endif
          <div>{{ $review->body }}</div>
          <div class="small text-muted mt-2">
            {{ $review->author_name ?? '—' }}
            @if($review->user) · {{ $review->user->full_name }} ({{ $review->user->email }}) @endif
          </div>
        </div>
        <div class="text-right">
          <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-secondary">{{ __('reviews.common.back') }}</a>
        </div>
      </div>
    </div>
  </div>

  {{-- ==== Form responder ==== --}}
  @php
    // Heurística sencilla para pre-checar el checkbox y mostrar el destinatario
    $to = $review->author_email
        ?? optional($review->user)->email
        ?? optional(optional($review->booking)->user)->email
        ?? ($review->booking->customer_email ?? $review->booking->email ?? null);
  @endphp

  @if(empty($to))
    <div class="alert alert-warning">{{ __('reviews.replies.warn_no_email') }}</div>
  @endif

  <div class="card mb-3">
    <div class="card-header"><i class="fas fa-reply"></i> {{ __('reviews.replies.reply') }}</div>
    <div class="card-body">
      <form method="post" action="{{ route('admin.reviews.replies.store',$review) }}">
        @csrf
        <div class="form-group">
          <textarea name="body" rows="4" class="form-control" placeholder="{{ __('reviews.replies.label_body') }}…" required>{{ old('body') }}</textarea>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" checked>
          <label class="form-check-label" for="is_public">{{ __('reviews.replies.label_is_public') }}</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="notify" id="notify" value="1" {{ !empty($to) ? 'checked' : '' }}>
          <label class="form-check-label" for="notify">{{ __('reviews.replies.label_notify') }}</label>
          @if(!empty($to))
            <div class="small text-muted">{{ __('reviews.replies.notify_to', ['email' => $to]) }}</div>
          @endif
        </div>
        <button class="btn btn-primary">{{ __('reviews.replies.reply') }}</button>
      </form>
    </div>
  </div>

  {{-- ==== Tabla de respuestas ==== --}}
  <div class="card">
    <div class="card-header"><i class="fas fa-stream"></i> {{ __('reviews.thread.replies_header') }}</div>
    <div class="card-body p-0">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th>{{ __('reviews.thread.th_date') }}</th>
            <th>{{ __('reviews.thread.th_admin') }}</th>
            <th>{{ __('reviews.thread.th_visible') }}</th>
            <th>{{ __('reviews.thread.th_body') }}</th>
            <th class="text-right">{{ __('reviews.thread.th_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($review->replies as $rep)
            @php $vis = isset($rep->public) ? (bool)$rep->public : (bool)($rep->is_public ?? true); @endphp
            <tr>
              <td class="align-middle">{{ $rep->created_at->format('d-M-Y H:i') }}</td>
              <td class="align-middle">{{ optional($rep->admin)->full_name ?? '—' }}</td>
              <td class="align-middle">
                <span class="badge {{ $vis ? 'badge-success' : 'badge-secondary' }}">
                  {{ $vis ? __('reviews.common.public') : __('reviews.common.private') }}
                </span>
              </td>
              <td class="align-middle" style="max-width:520px">{{ $rep->body }}</td>
              <td class="align-middle text-right">
                <form method="post" action="{{ route('admin.reviews.replies.toggle',[$review,$rep]) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-xs btn-warning" title="{{ __('reviews.thread.toggle_visibility') }}">
                    <i class="fas fa-eye"></i>
                  </button>
                </form>
                <form method="post" action="{{ route('admin.reviews.replies.destroy',[$review,$rep]) }}" class="d-inline" onsubmit="return confirm(@json(__('reviews.thread.confirm_delete')))">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" title="{{ __('reviews.thread.delete') }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-3">{{ __('reviews.thread.empty') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@stop
