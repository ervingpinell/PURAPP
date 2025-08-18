{{-- resources/views/errors/429.blade.php --}}
@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('title', __('Demasiados intentos'))

@section('auth_header')
    <h1 class="text-danger">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('Demasiados intentos') }}
    </h1>
@stop

@section('auth_body')
    <div class="alert alert-danger text-center">
        <p>
            {{ __('Has realizado demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en unos minutos.') }}
        </p>

        @if(isset($exception) && ($exception->getHeaders()['Retry-After'] ?? false))
            @php
                $seconds = (int) $exception->getHeaders()['Retry-After'];
            @endphp
            <p class="mt-2">
                ⏳ {{ __('Podrás volver a intentarlo en') }}
                <strong id="countdown">{{ $seconds }}</strong> {{ __('segundos.') }}
            </p>
        @endif
    </div>
@stop

@section('auth_footer')
    <div class="text-center">
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
        </a>
    </div>
@stop

@section('adminlte_js')
<script>
  document.addEventListener("DOMContentLoaded", function() {
    let countdownEl = document.getElementById("countdown");
    if (countdownEl) {
      let seconds = parseInt(countdownEl.textContent);

      let timer = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
          clearInterval(timer);
          countdownEl.textContent = "0";
          // 🔄 Opción: mandar directo al login cuando termina
          window.location.href = "{{ route('login') }}";
        } else {
          countdownEl.textContent = seconds;
        }
      }, 1000);
    }
  });
</script>
@stop
