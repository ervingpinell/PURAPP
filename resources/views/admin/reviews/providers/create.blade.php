@extends('adminlte::page')

@section('title', 'Nuevo Proveedor de Reseñas')

@section('content_header')
  <h1><i class="fas fa-plug"></i> Nuevo Proveedor de Reseñas</h1>
@stop

@section('content')
  @include('admin.reviews.providers.flash') {{-- opcional, ver más abajo --}}
  @include('admin.reviews.providers.form')
@stop
