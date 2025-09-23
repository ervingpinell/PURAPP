@extends('layouts.app')

@section('title', __('reviews.public.form_title'))

@section('content')
  <div class="container py-4">
    <h1 class="h4 mb-3">
      {{ __('reviews.public.form_title_for', ['tour' => $tour?->name ?? __('reviews.common.tour')]) }}
    </h1>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('reviews.request.submit', $rr->token) }}">
      @csrf

      <div class="form-group">
        <label>{{ __('reviews.public.labels.rating') }}</label>
        <select class="form-control" name="rating" required>
          @for($i=5; $i>=1; $i--)
            <option value="{{ $i }}" {{ old('rating')==$i?'selected':'' }}>{{ $i }} ★</option>
          @endfor
        </select>
      </div>

      <div class="form-group">
        <label>{{ __('reviews.public.labels.title') }}</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" maxlength="120">
      </div>

      <div class="form-group">
        <label>{{ __('reviews.public.labels.body') }}</label>
        <textarea name="body" class="form-control" rows="5" required minlength="5" maxlength="1000">{{ old('body') }}</textarea>
      </div>

      <button class="btn btn-primary">{{ __('reviews.public.labels.submit') }}</button>
    </form>
  </div>
@endsection
