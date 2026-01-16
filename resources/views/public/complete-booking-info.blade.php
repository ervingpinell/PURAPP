@extends('layouts.app')

@section('title', __('payment_link.title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit"></i>
                        {{ __('payment_link.title') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('payment_link.subtitle') }}
                    </div>

                    <form action="{{ route('booking.complete-info', $token) }}" method="POST" id="complete-info-form">
                        @csrf

                        {{-- Name (Editable) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('payment_link.first_name') }} <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror" 
                                    value="{{ old('first_name', $user->first_name) }}"
                                    required
                                >
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('payment_link.last_name') }} <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror" 
                                    value="{{ old('last_name', $user->last_name) }}"
                                    required
                                >
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email (Read-only) --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('payment_link.email') }}</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            <small class="text-muted">{{ __('payment_link.email_help') }}</small>
                        </div>

                        {{-- Phone with Country Code --}}
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('payment_link.phone') }} <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="phone" 
                                id="phone" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                value="{{ old('phone', $user->phone) }}"
                                required
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('payment_link.phone_help') }}</small>
                        </div>

                        {{-- Address --}}
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('payment_link.address') }} <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="address" 
                                class="form-control @error('address') is-invalid @enderror" 
                                value="{{ old('address', $user->address) }}"
                                placeholder="{{ __('payment_link.address_placeholder') }}"
                                required
                            >
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- City and State --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('payment_link.city') }} <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    class="form-control @error('city') is-invalid @enderror" 
                                    value="{{ old('city', $user->city) }}"
                                    required
                                >
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('payment_link.state') }} <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="state" 
                                    class="form-control @error('state') is-invalid @enderror" 
                                    value="{{ old('state', $user->state) }}"
                                    required
                                >
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ZIP and Country --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('payment_link.zip') }} <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="zip" 
                                    class="form-control @error('zip') is-invalid @enderror" 
                                    value="{{ old('zip', $user->zip) }}"
                                    required
                                >
                                @error('zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('payment_link.country') }} <span class="text-danger">*</span>
                                </label>
                                <select 
                                    name="country" 
                                    class="form-select @error('country') is-invalid @enderror"
                                    required
                                >
                                    <option value="">{{ __('payment_link.select_country') }}</option>
                                    <option value="CR" {{ old('country', $user->country ?? 'CR') === 'CR' ? 'selected' : '' }}>Costa Rica</option>
                                    <option value="US" {{ old('country', $user->country) === 'US' ? 'selected' : '' }}>United States</option>
                                    <option value="PA" {{ old('country', $user->country) === 'PA' ? 'selected' : '' }}>Panama</option>
                                    <option value="NI" {{ old('country', $user->country) === 'NI' ? 'selected' : '' }}>Nicaragua</option>
                                    <option value="GT" {{ old('country', $user->country) === 'GT' ? 'selected' : '' }}>Guatemala</option>
                                    <option value="MX" {{ old('country', $user->country) === 'MX' ? 'selected' : '' }}>Mexico</option>
                                    <option value="CA" {{ old('country', $user->country) === 'CA' ? 'selected' : '' }}>Canada</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-right"></i>
                                {{ __('payment_link.continue_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector("#phone");
        
        if (phoneInput) {
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "cr",
                preferredCountries: ["cr", "us", "pa", "ni", "gt"],
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
            });

            // Update hidden input with full international number on form submit
            const form = phoneInput.closest('form');
            form.addEventListener('submit', function(e) {
                const fullNumber = iti.getNumber();
                phoneInput.value = fullNumber;
            });
        }
    });
</script>
@endpush

@endsection
