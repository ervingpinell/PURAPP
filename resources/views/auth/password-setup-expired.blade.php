@extends('layouts.app')

@section('title', __('password_setup.token_expired'))

@push('styles')
<style>
    .expired-container {
        max-width: 500px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .expired-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
        text-align: center;
    }

    .expired-icon {
        width: 80px;
        height: 80px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-center;
        margin: 0 auto 1.5rem;
        color: #ef4444;
        font-size: 2.5rem;
    }

    .expired-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .expired-message {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .btn-home {
        display: inline-block;
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, var(--primary-color, #60a862), var(--primary-dark, #256d1b));
        color: #fff;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-home:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(96, 168, 98, 0.3);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="expired-container">
    <div class="expired-card">
        <div class="expired-icon">
            <i class="fas fa-clock"></i>
        </div>
        <h1 class="expired-title">{{ __('password_setup.token_expired') }}</h1>
        <p class="expired-message">
            {{ __('password_setup.token_expired') }}
        </p>
        <a href="{{ route(app()->getLocale() . '.home') }}" class="btn-home">
            {{ __('Volver al inicio') }}
        </a>
    </div>
</div>
@endsection