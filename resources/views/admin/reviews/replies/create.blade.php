@extends('adminlte::page')

@section('title', __('reviews.replies.title_create', ['id' => $review->id]))

@section('content_header')
  <h1><i class="fa fa-reply"></i> {{ __('reviews.replies.title_create', ['id' => $review->id]) }}</h1>
@stop

@section('content')
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif
  @if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  @if (empty($to))
    <div class="alert alert-warning">
      {{ __('reviews.replies.warn_no_email') }}
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="mb-3 small text-muted">
        <strong>{{ __('reviews.common.tour') }}:</strong> {{ $review->product_id }} |
        <strong>{{ __('reviews.common.rating') }}:</strong> {{ $review->rating }} |
        <strong>{{ __('reviews.common.author') }}:</strong> {{ $review->author_name ?? '—' }}
      </div>

      <form method="post" action="{{ route('admin.reviews.replies.store', $review) }}">
        @csrf

        <div class="form-group">
          <label for="body">{{ __('reviews.replies.label_body') }}</label>
          <textarea id="body" name="body" rows="6" class="form-control" required
                    placeholder="{{ __('reviews.replies.label_body') }}...">{{ old('body') }}</textarea>
        </div>

        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" checked>
          <label for="is_public" class="form-check-label">{{ __('reviews.replies.label_is_public') }}</label>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="notify" id="notify" value="1" {{ !empty($to) ? 'checked' : '' }}>
          <label for="notify" class="form-check-label">{{ __('reviews.replies.label_notify') }}</label>
          @if (!empty($to))
            <div class="small text-muted">{{ __('reviews.replies.notify_to', ['email' => $to]) }}</div>
          @endif
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-primary">{{ __('reviews.replies.reply') }}</button>
          <a class="btn btn-secondary" href="{{ route('admin.reviews.index') }}">{{ __('reviews.common.back') }}</a>
        </div>
      </form>
    </div>
  </div>
@stop
