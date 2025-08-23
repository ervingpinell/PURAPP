@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', __('adminlte::adminlte.reset_password'))

@section('auth_body')
    <form method="POST" action="{{ route('password.update') }}" id="resetForm" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   value="{{ old('email', $email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.email') }}"
                   required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-1">
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}"
                   autocomplete="new-password"
                   required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password" aria-label="{{ __('Mostrar u ocultar contraseña') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="small mb-3" id="pwRulesWrap" aria-live="polite">
            <ul class="list-unstyled mb-0 pw-rules">
                <li class="pw-rule" data-rule="length">
                    <i class="far fa-circle me-1 rule-icon"></i>
                    <span>{{ __('adminlte::validation.password_requirements.length') }}</span>
                </li>
                <li class="pw-rule" data-rule="special">
                    <i class="far fa-circle me-1 rule-icon"></i>
                    <span>{{ __('adminlte::validation.password_requirements.special') }}</span>
                </li>
                <li class="pw-rule" data-rule="number">
                    <i class="far fa-circle me-1 rule-icon"></i>
                    <span>{{ __('adminlte::validation.password_requirements.number') }}</span>
                </li>
                <li class="pw-rule" data-rule="match">
                    <i class="far fa-circle me-1 rule-icon"></i>
                    <span>{{ __('adminlte::adminlte.passwords_match') ?? 'Las contraseñas coinciden' }}</span>
                </li>
            </ul>
        </div>

        {{-- Confirm password --}}
        <div class="input-group mb-3">
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   class="form-control"
                   placeholder="{{ __('adminlte::adminlte.retype_password') }}"
                   autocomplete="new-password"
                   required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" class="text-reset toggle-password" data-target="password_confirmation" aria-label="{{ __('Mostrar u ocultar contraseña') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary btn-block" disabled>
            {{ __('adminlte::adminlte.reset_password') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <div class="d-flex flex-column text-center gap-2">
        <div>
            <a href="{{ route('login') }}">
                {{ __('adminlte::adminlte.back_to_login') ?? __('Volver al login') }}
            </a>
        </div>
        <div>
            @include('partials.language-switcher')
        </div>
    </div>
@stop

@push('css')
<style>
    .pw-rules .pw-rule { color: #6c757d; }
    .pw-rules .pw-rule.valid { color: #198754; }
    .pw-rules .pw-rule.invalid { color: #dc3545; }
    .pw-rules .rule-icon { width: 1rem; text-align: center; }
</style>
@endpush

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.dataset.target;
            const input    = document.getElementById(targetId);
            if (!input) return;

            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    const pass  = document.getElementById('password');
    const pass2 = document.getElementById('password_confirmation');
    const btn   = document.getElementById('submitBtn');

    const rulesEls = {
        length: document.querySelector('.pw-rule[data-rule="length"]'),
        special: document.querySelector('.pw-rule[data-rule="special"]'),
        number: document.querySelector('.pw-rule[data-rule="number"]'),
        match: document.querySelector('.pw-rule[data-rule="match"]'),
    };

    const setRuleState = (el, ok) => {
        if (!el) return;
        el.classList.remove('valid', 'invalid');
        el.classList.add(ok ? 'valid' : 'invalid');
        const icon = el.querySelector('.rule-icon');
        if (icon) {
            icon.className = 'rule-icon ' + (ok ? 'fas fa-check-circle' : 'far fa-circle');
        }
    };

    const meetsLength  = (v) => (v || '').length >= 8;

    const meetsSpecial = (v) => /[.\u00A1!@#$%^&*()_+\-]/.test(v || '');
    const meetsNumber  = (v) => /[0-9]/.test(v || '');
    const matchesBoth  = (a, b) => (a || '') === (b || '') && a.length > 0;

    function evaluate() {
        const v1 = pass.value || '';
        const v2 = pass2.value || '';

        const okLen  = meetsLength(v1);
        const okSpec = meetsSpecial(v1);
        const okNum  = meetsNumber(v1);
        const okMat  = matchesBoth(v1, v2);

        setRuleState(rulesEls.length, okLen);
        setRuleState(rulesEls.special, okSpec);
        setRuleState(rulesEls.number, okNum);

        if (v2.length === 0) {
            if (rulesEls.match) {
                rulesEls.match.classList.remove('valid','invalid');
                const icon = rulesEls.match.querySelector('.rule-icon');
                if (icon) icon.className = 'rule-icon far fa-circle';
            }
        } else {
            setRuleState(rulesEls.match, okMat);
        }
        const allGood = okLen && okSpec && okNum && okMat;
        btn.disabled = !allGood;
    }

    ['input','change','keyup','blur'].forEach(evt => {
        pass.addEventListener(evt, evaluate);
        pass2.addEventListener(evt, evaluate);
    });

    evaluate();
});
</script>
@stop
