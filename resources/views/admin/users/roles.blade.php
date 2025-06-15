@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Gestión de Roles</h1>
@stop



@section('css')
    {{-- Agrega aquí tus estilos si los necesitas --}}
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@stop
