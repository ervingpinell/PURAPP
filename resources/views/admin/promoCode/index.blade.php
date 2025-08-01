@extends('adminlte::page')

@section('title', 'Códigos Promocionales')

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endpush

@section('content')
<div class="container-fluid">

    {{-- 🧾 FORMULARIO PARA CREAR CÓDIGO --}}
    <h2 class="mb-4">Generar nuevo código promocional</h2>

    <form method="POST" action="{{ route('admin.promoCode.store') }}">
        @csrf
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="code">Código</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="discount">Descuento</label>
                <input type="number" step="0.01" name="discount" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="type">Tipo</label>
                <select name="type" class="form-control" required>
                    <option value="percent">%</option>
                    <option value="amount">$</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Generar</button>
            </div>
        </div>
    </form>

    {{-- 📋 LISTADO DE CÓDIGOS --}}
    <h3 class="mt-5">Códigos promocionales existentes</h3>

    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descuento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($promoCodes as $promo)
                <tr>
                    <td>{{ $promo->code }}</td>
                    <td>
                        @if ($promo->discount_percent)
                            {{ number_format($promo->discount_percent, 2) }}%
                        @elseif ($promo->discount_amount)
                            ${{ number_format($promo->discount_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($promo->is_used)
                            <span class="badge bg-danger">Usado</span>
                        @else
                            <span class="badge bg-success">Disponible</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.promoCode.destroy', $promo) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este código?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No hay códigos promocionales disponibles.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
