@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', '403')
@section('auth_header', __('Acceso denegado'))

@section('auth_body')
  <div class="alert alert-danger">
    <i class="fas fa-ban me-1"></i>
    {{ __('No tienes permisos para acceder a esta sección.') }}
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary w-100">
    <i class="fas fa-home me-1"></i> {{ __('Volver al inicio') }}
  </a>
@endsection
