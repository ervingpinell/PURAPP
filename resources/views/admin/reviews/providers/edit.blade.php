@extends('adminlte::page')

@section('title', 'Editar Proveedor de Reseñas')

@section('content_header')
  <h1><i class="fas fa-plug"></i> Editar Proveedor: {{ $provider->name }}</h1>
@stop

@section('content')
  @include('admin.reviews.providers.flash') {{-- opcional, ver más abajo --}}
  @include('admin.reviews.providers.form', ['provider' => $provider])
@stop
