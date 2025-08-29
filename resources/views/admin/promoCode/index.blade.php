@extends('adminlte::page')

@section('title', __('m_config.promocode.title'))

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: @json(__('m_config.promocode.success_title')),
            text: @json(session('success')),
            confirmButtonText: @json(__('m_config.translations.ok')),
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: @json(__('m_config.promocode.error_title')),
            text: @json(session('error')),
            confirmButtonText: @json(__('m_config.translations.ok')),
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endpush

@section('content')
<div class="container-fluid">

    {{-- 🧾 FORMULARIO PARA CREAR CÓDIGO --}}
    <h2 class="mb-4">{{ __('m_config.promocode.create_title') }}</h2>

    <form method="POST" action="{{ route('admin.promoCode.store') }}">
        @csrf
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="code">{{ __('m_config.promocode.fields.code') }}</label>
                <input type="text" id="code" name="code" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="discount">{{ __('m_config.promocode.fields.discount') }}</label>
                <input type="number" step="0.01" id="discount" name="discount" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="type">{{ __('m_config.promocode.fields.type') }}</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="percent">{{ __('m_config.promocode.types.percent') }}</option>
                    <option value="amount">{{ __('m_config.promocode.types.amount') }}</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    {{ __('m_config.promocode.actions.generate') }}
                </button>
            </div>
        </div>
    </form>

    {{-- 📋 LISTADO DE CÓDIGOS --}}
    <h3 class="mt-5">{{ __('m_config.promocode.list_title') }}</h3>

    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th>{{ __('m_config.promocode.table.code') }}</th>
                <th>{{ __('m_config.promocode.table.discount') }}</th>
                <th>{{ __('m_config.promocode.table.status') }}</th>
                <th>{{ __('m_config.promocode.table.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($promoCodes as $promo)
                <tr>
                    <td>{{ $promo->code }}</td>
                    <td>
                        @if ($promo->discount_percent)
                            {{ number_format($promo->discount_percent, 2) }}{{ __('m_config.promocode.symbols.percent') }}
                        @elseif ($promo->discount_amount)
                            {{ __('m_config.promocode.symbols.currency') }}{{ number_format($promo->discount_amount, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($promo->is_used)
                            <span class="badge bg-danger">{{ __('m_config.promocode.status.used') }}</span>
                        @else
                            <span class="badge bg-success">{{ __('m_config.promocode.status.available') }}</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.promoCode.destroy', $promo) }}" method="POST"
                              onsubmit="return confirm(@json(__('m_config.promocode.confirm_delete')))">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">
                                {{ __('m_config.promocode.actions.delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        {{ __('m_config.promocode.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
