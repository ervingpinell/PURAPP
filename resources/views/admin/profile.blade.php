@extends('adminlte::page')

@section('title', 'Editar Perfil')

@section('content_header')
    <h1 class="text-center"><i class="fas fa-user-edit"></i> Editar Perfil de: {{$user->full_name}}</h1>
@stop

@section('content')
    <div class="d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary shadow">
                <div class="card-header text-center">
                    <h3 class="card-title w-100"><i class="fas fa-user-cog"></i> Información del Perfil</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Nombre</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->full_name }}" required>
                            @error('name') 
                                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email') 
                                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Nueva contraseña (opcional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco si no desea cambiarla">
                            @error('password') 
                                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif
@stop
