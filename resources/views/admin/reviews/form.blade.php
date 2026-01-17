@extends('adminlte::page')

@section('title', $review->exists ? __('reviews.form.title_edit') : __('reviews.form.title_new'))

@section('content_header')
  <h1><i class="fas fa-pen"></i> {{ $review->exists ? __('reviews.form.title_edit') : __('reviews.form.title_new') }}</h1>
@stop

@section('content')
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif
  @if (session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  <div class="card">
    <div class="card-body">
      <form method="post" action="{{ $review->exists ? route('admin.reviews.update',$review) : route('admin.reviews.store') }}">
        @csrf
        @if($review->exists) @method('PUT') @endif

        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>{{ __('reviews.admin.filters.tour_id') }}</label>
              <input type="number" name="tour_id" class="form-control"
                     value="{{ old('tour_id',$review->tour_id) }}"
                     {{ $review->exists ? 'disabled' : 'required' }}>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label>{{ __('reviews.common.rating') }}</label>
              <input type="number" name="rating" min="1" max="5" required class="form-control"
                     value="{{ old('rating',$review->rating) }}">
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label>{{ __('reviews.common.language') }}</label>
              <input type="text" name="language" class="form-control" placeholder="es"
                     value="{{ old('language',$review->language) }}">
            </div>
          </div>

          <div class="col-md-5">
            <div class="form-group">
              <label>{{ __('reviews.common.author') }}</label>
              <input type="text" name="author_name" class="form-control"
                     value="{{ old('author_name',$review->author_name) }}">
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label>{{ __('reviews.common.title') }}</label>
              <input type="text" name="title" class="form-control" value="{{ old('title',$review->title) }}">
            </div>
          </div>

          <div class="col-12">
            <div class="form-group">
              <label>{{ __('reviews.common.body') }}</label>
              <textarea name="body" rows="6" class="form-control" required>{{ old('body',$review->body) }}</textarea>
            </div>
          </div>



          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary">{{ $review->exists ? __('reviews.common.save') : __('reviews.common.create') }}</button>
          <a class="btn btn-secondary" href="{{ route('admin.reviews.index') }}">{{ __('reviews.common.back') }}</a>
        </div>
      </form>
    </div>
  </div>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @if (session('ok'))
    <script>Swal.fire({icon:'success', title:@json(session('ok'))});</script>
  @endif
@stop
