{{-- resources/views/admin/tours/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.product.ui.edit_title', ['name' => $product->name]))

@push('css')
<style>
    /* Permitir scroll vertical como en los steps */
    body.sidebar-mini .content-wrapper {
        overflow-y: auto !important;
    }

    /* Header tipo wizard */
    .product-edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .product-edit-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .product-edit-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .product-edit-header .btn-secondary {
        border-color: rgba(255,255,255,0.5);
        background: rgba(74, 85, 104, 0.9);
        color: #edf2f7;
    }

    .product-edit-header .btn-secondary:hover {
        background: rgba(90, 103, 120, 1);
        border-color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header tipo wizard --}}
    <div class="product-edit-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-route"></i>
                    {{ __('m_tours.product.ui.edit_title') }}: {{ $product->name }}
                </h1>
                <p>
                    {{ __('m_tours.product.wizard.edit_intro') ?? 'Edita tu product usando el mismo flujo por pasos del asistente.' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('m_tours.product.ui.back') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Mensajes de error / éxito (igual que antes) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
            <strong class="d-block mb-1">{{ __('m_tours.common.errors') }}</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
            {{ session('success') }}
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="{{ __('m_tours.common.close') }}"></button>
        </div>
    @endif

    {{-- Stepper superior (mismo que en los steps) --}}
    @include('admin.products.wizard.partials.stepper', [
        'product' => $product,
        'step' => 6, // Siempre mostrar como si estuviera en el último paso (Resumen)
    ])

</div>
@endsection

@push('js')
    {{-- Librerías (si no están ya en el layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush
