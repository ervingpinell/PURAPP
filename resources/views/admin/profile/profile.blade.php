@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit_profile'))

@section('content_header')
    <h1 class="text-center">
        <i class="fas fa-user-edit"></i>
        {{ __('adminlte::adminlte.edit_profile_of', ['name' => $user->full_name]) }}
    </h1>
@stop

@section('content')
<div class="d-flex justify-content-center">
    <div class="col-md-6">
        <div class="card card-primary shadow">
            <div class="card-header text-center">
                <h3 class="card-title w-100">
                    <i class="fas fa-user-cog"></i> {{ __('adminlte::adminlte.profile_information') }}
                </h3>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="full_name">
                            <i class="fas fa-user"></i> {{ __('adminlte::adminlte.full_name') }}
                        </label>
                        <input type="text" name="full_name" class="form-control"
                               value="{{ old('full_name', $user->full_name) }}" required>
                        @error('full_name')
                          <span class="text-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> {{ __('adminlte::adminlte.email') }}
                        </label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                          <span class="text-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                          </span>
                        @enderror
                    </div>

                    {{-- Para admins no mostramos teléfono, sólo contraseña opcional --}}
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> {{ __('adminlte::adminlte.new_password_optional') }}
                        </label>
                        <input type="password" name="password" class="form-control"
                               placeholder="{{ __('adminlte::adminlte.leave_blank_if_no_change') }}">
                        @error('password')
                          <span class="text-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i> {{ __('adminlte::adminlte.retype_password') }}
                        </label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="{{ __('adminlte::adminlte.confirm_new_password_placeholder') }}">
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('adminlte::adminlte.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif
@stop
