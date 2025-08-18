@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@section('title', __('adminlte::auth.verify.verify_email_title'))
@section('auth_header', __('adminlte::auth.verify.verify_email_header'))

@section('auth_body')

  @if (session('status'))
    <div class="alert alert-success" role="alert">
      <i class="fas fa-check-circle me-1"></i>
      {{ session('status') }}
    </div>
  @endif

  @if (!empty($email))
    <p class="mb-2">
      {{ __('adminlte::auth.verify.verify_email_sent_to') }}
      <strong>{{ $email }}</strong>
    </p>
  @else
    <p class="mb-2">
      {{ __('adminlte::auth.verify.verify_email_generic') }}
    </p>
  @endif

  <p class="text-muted">
    {{ __('adminlte::auth.verify.verify_email_instructions') }}
  </p>

  <div class="mt-4 d-grid gap-2">
    <a href="{{ url('/login') }}" class="btn btn-primary">
      <i class="fas fa-sign-in-alt me-1"></i> {{ __('adminlte::auth.verify.back_to_login') }}
    </a>
  </div>
@endsection

@section('auth_footer')
  <div class="text-center mt-3">
    <a href="{{ url('/') }}" class="text-muted">
      <i class="fas fa-home me-1"></i> {{ __('adminlte::auth.verify.back_to_home') }}
    </a>
  </div>
@endsection
