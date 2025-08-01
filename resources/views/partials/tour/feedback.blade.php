@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
  <script>
    Swal.fire({
      icon: 'error',
      title: '{{ __('adminlte::adminlte.access_denied') }}',
      html: `{!! session('error') !!}`,
      confirmButtonText: 'OK'
    });
  </script>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
