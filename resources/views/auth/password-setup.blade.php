@extends('layouts.app')

@section('title', __('password_setup.title'))

@push('styles')
<style>
    .setup-container {
        max-width: 500px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .setup-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .setup-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .setup-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary-color, #60a862), var(--primary-dark, #256d1b));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: #fff;
        font-size: 2rem;
    }

    .setup-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .setup-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .booking-badge {
        display: inline-block;
        background: #f3f4f6;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        color: var(--primary-color, #60a862);
        margin-top: 1rem;
    }

    .benefits-list {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .benefits-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        color: #4b5563;
        font-size: 0.9rem;
    }

    .benefit-icon {
        width: 20px;
        height: 20px;
        background: var(--primary-color, #60a862);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color, #60a862);
        box-shadow: 0 0 0 3px rgba(96, 168, 98, 0.1);
    }

    .form-input.error {
        border-color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .password-strength {
        margin-top: 0.75rem;
    }

    .strength-bar {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        transition: all 0.3s;
        border-radius: 2px;
    }

    .strength-fill.weak {
        width: 33%;
        background: #ef4444;
    }

    .strength-fill.medium {
        width: 66%;
        background: #f59e0b;
    }

    .strength-fill.strong {
        width: 100%;
        background: #10b981;
    }

    .strength-text {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .requirements-list {
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        font-size: 0.85rem;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0;
        color: #6b7280;
    }

    .requirement.met {
        color: #10b981;
    }

    .requirement-icon {
        font-size: 0.75rem;
    }

    .btn-submit {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(135deg, var(--primary-color, #60a862), var(--primary-dark, #256d1b));
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(96, 168, 98, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .skip-link {
        display: block;
        text-align: center;
        margin-top: 1rem;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .skip-link:hover {
        color: #374151;
        text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .setup-container {
            margin: 1.5rem auto;
            padding: 0 0.75rem;
        }

        .setup-card {
            padding: 1.5rem;
            border-radius: 0.75rem;
        }

        .setup-title {
            font-size: 1.25rem;
        }

        .booking-badge {
            font-size: 0.9rem;
            padding: 0.4rem 0.75rem;
        }

        .setup-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .benefits-list {
            padding: 1rem;
            margin: 1.25rem 0;
        }

        .benefits-title {
            margin-bottom: 0.75rem;
        }
    }

    @media (max-width: 374px) {
        .setup-card {
            padding: 1.25rem 1rem;
        }

        .setup-title {
            font-size: 1.15rem;
        }

        .form-input {
            font-size: 16px;
            /* Prevents iOS zooom */
            padding: 0.65rem 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="setup-container">
    <div class="setup-card">
        <div class="setup-header">
            <div class="setup-icon">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="setup-title">{{ __('password_setup.welcome', ['name' => $user->full_name]) }}</h1>
            @if(session('booking_reference'))
            <div class="booking-badge">
                {{ __('password_setup.booking_confirmed', ['reference' => session('booking_reference')]) }}
            </div>
            @endif
        </div>

        {{-- Payment Success Message --}}
        @if(request('from') === 'payment')
        <div class="alert alert-success mb-4" style="background: #d1fae5; border: 1px solid #10b981; border-radius: 0.75rem; padding: 1rem; color: #065f46;">
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <strong>{{ __('password_setup.payment_success_message') }}</strong>
        </div>
        @endif

        <div class="benefits-list">
            <div class="benefits-title">{{ __('password_setup.create_password') }}</div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-check"></i></div>
                <span>{{ __('password_setup.benefits.view_bookings') }}</span>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-check"></i></div>
                <span>{{ __('password_setup.benefits.manage_profile') }}</span>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-check"></i></div>
                <span>{{ __('password_setup.benefits.exclusive_offers') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('password.setup.process') }}" id="setupForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="password" class="form-label">{{ __('password_setup.password_label') }}</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input @error('password') error @enderror"
                    required
                    autocomplete="new-password">
                @error('password')
                <div class="error-message">{{ $message }}</div>
                @enderror

                <div class="password-strength" id="passwordStrength" style="display: none;">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>

                <div class="requirements-list">
                    <div class="requirement" id="req-length">
                        <span class="requirement-icon">○</span>
                        <span>{{ __('password_setup.password_min_length') }}</span>
                    </div>
                    <div class="requirement" id="req-number">
                        <span class="requirement-icon">○</span>
                        <span>{{ __('password_setup.requirements.one_number') }}</span>
                    </div>
                    <div class="requirement" id="req-special">
                        <span class="requirement-icon">○</span>
                        <span>{{ __('password_setup.requirements.one_special_char') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">{{ __('password_setup.confirm_password_label') }}</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-input"
                    required
                    autocomplete="new-password">
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                {{ __('password_setup.submit_button') }}
            </button>
        </form>

        <a href="{{ route(app()->getLocale() . '.home') }}" class="skip-link">
            {{ __('password_setup.maybe_later') }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthDiv = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const submitBtn = document.getElementById('submitBtn');

        const reqLength = document.getElementById('req-length');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            if (password.length > 0) {
                strengthDiv.style.display = 'block';
            } else {
                strengthDiv.style.display = 'none';
                return;
            }

            // Check requirements
            const hasLength = password.length >= 8;
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[.¡!@#$%^&*()_+\-]/.test(password);

            // Update requirement indicators
            updateRequirement(reqLength, hasLength);
            updateRequirement(reqNumber, hasNumber);
            updateRequirement(reqSpecial, hasSpecial);

            // Calculate strength
            let strength = 0;
            if (hasLength) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            // Update strength bar
            strengthFill.className = 'strength-fill';
            if (strength === 1) {
                strengthFill.classList.add('weak');
                strengthText.textContent = '{{ __("password_setup.strength.weak") }}';
                strengthText.style.color = '#ef4444';
            } else if (strength === 2) {
                strengthFill.classList.add('medium');
                strengthText.textContent = '{{ __("password_setup.strength.medium") }}';
                strengthText.style.color = '#f59e0b';
            } else if (strength === 3) {
                strengthFill.classList.add('strong');
                strengthText.textContent = '{{ __("password_setup.strength.strong") }}';
                strengthText.style.color = '#10b981';
            }
        });

        function updateRequirement(element, met) {
            const icon = element.querySelector('.requirement-icon');
            if (met) {
                element.classList.add('met');
                icon.textContent = '✓';
            } else {
                element.classList.remove('met');
                icon.textContent = '○';
            }
        }

        // Form validation
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (password !== confirm) {
                e.preventDefault();
                alert('{{ __("password_setup.passwords_do_not_match") }}');
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = '{{ __("password_setup.creating_account") }}';
        });
    });
</script>
@endpush