@if (session('success') || session('error') || $errors->any())
  @once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @endonce

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      @if (session('success'))
        Swal.fire({
          icon: 'success',
          title: @json(__('adminlte::adminlte.success') ?? 'Éxito'),
          text:  @json(session('success')),
          confirmButtonColor: '#198754',
          allowOutsideClick: false
        });
      @endif

      @if (session('error'))
        Swal.fire({
          icon: 'error',
          title: @json(__('adminlte::adminlte.error') ?? 'Error'),
          text:  @json(session('error')),
          confirmButtonColor: '#dc3545',
          allowOutsideClick: false
        });
      @endif

      @if ($errors->any())
        Swal.fire({
          icon: 'error',
          title: @json(__('adminlte::adminlte.validation_error') ?? 'Datos incompletos'),
          html: `{!! '<ul style="text-align:left;margin:0;padding-left:1rem;">'.collect($errors->all())->map(fn($e)=>'<li>'.e($e).'</li>')->implode('').'</ul>' !!}`,
          confirmButtonColor: '#dc3545',
          allowOutsideClick: false
        });
      @endif
    });
  </script>
@endif
