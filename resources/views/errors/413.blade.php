@extends('errors::minimal')

@section('title', __('Archivo demasiado grande'))
@section('code', '413')
@section('message')
  <div style="text-align:center;padding:2rem">
    <h1 style="font-size:2rem;color:#2E8B57;">😅 Archivo demasiado grande</h1>
    <p style="font-size:1.1rem;">El archivo que intentas subir excede el tamaño permitido.</p>
    <p style="color:#555;">Por favor reduce el tamaño o contacta al administrador si necesitas subir archivos más grandes.</p>
    <a href="{{ url()->previous() }}" style="display:inline-block;margin-top:1rem;padding:0.6rem 1rem;background:#2E8B57;color:#fff;border-radius:6px;text-decoration:none;">Volver</a>
  </div>
@endsection
