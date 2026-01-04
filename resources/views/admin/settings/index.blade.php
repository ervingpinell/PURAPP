@extends('adminlte::page')

@section('title', 'Configuraciones del Sistema')

@section('content_header')
<h1>
    <i class="fas fa-cog"></i> Configuraciones del Sistema
</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestión de Configuraciones</h3>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                        @endif

                        @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('warning') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @foreach($settings as $category => $categorySettings)
                        <div class="mb-4">
                            <h4 class="text-primary border-bottom pb-2">
                                <i class="fas fa-{{ $category === 'cart' ? 'shopping-cart' : ($category === 'booking' ? 'calendar-check' : ($category === 'email' ? 'envelope' : ($category === 'payment' ? 'credit-card' : 'cog'))) }}"></i>
                                {{ $categoryLabels[$category] ?? ucfirst($category) }}
                            </h4>

                            @if($category === 'payment')
                            {{-- Special layout for payment settings --}}
                            <div class="row">
                                {{-- Gateways Column --}}
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fas fa-university"></i> Métodos de Pago</h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($categorySettings as $setting)
                                            @if(str_contains($setting->key, 'gateway.'))
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                                    <input type="checkbox"
                                                        class="custom-control-input"
                                                        id="setting_{{ $setting->key }}"
                                                        name="settings[{{ $setting->key }}]"
                                                        value="1"
                                                        {{ $setting->value ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="setting_{{ $setting->key }}">
                                                        <strong>{{ $setting->label }}</strong>
                                                        @if($setting->description)
                                                        <br><small class="text-muted">{{ $setting->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Currencies Column --}}
                                <div class="col-md-6">
                                    <div class="card card-outline card-success">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> Monedas Disponibles</h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($categorySettings as $setting)
                                            @if(str_contains($setting->key, 'currency.'))
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                                    <input type="checkbox"
                                                        class="custom-control-input currency-toggle"
                                                        id="setting_{{ $setting->key }}"
                                                        name="settings[{{ $setting->key }}]"
                                                        value="1"
                                                        data-currency="{{ str_replace('payment.currency.', '', $setting->key) }}"
                                                        {{ $setting->value ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="setting_{{ $setting->key }}">
                                                        <strong>{{ $setting->label }}</strong>
                                                        @if($setting->description)
                                                        <br><small class="text-muted">{{ $setting->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reserve Now & Pay Later - Accordion Style --}}
                            @php
                            $payLaterEnabled = $categorySettings->firstWhere('key', 'booking.pay_later.enabled')?->value ?? false;
                            @endphp

                            <div class="card card-outline {{ $payLaterEnabled ? 'card-info' : 'card-secondary' }}">
                                <div class="card-header" data-toggle="collapse" data-target="#payLaterSettings" style="cursor: pointer;">
                                    <h5 class="card-title">
                                        <i class="fas fa-clock"></i> Reserva Ahora y Paga Después
                                        <small class="float-right">
                                            @if($payLaterEnabled)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Activo</span>
                                            @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Inactivo</span>
                                            @endif
                                            <i class="fas fa-chevron-down ml-2"></i>
                                        </small>
                                    </h5>
                                </div>

                                <div id="payLaterSettings" class="collapse {{ $payLaterEnabled ? 'show' : '' }}">
                                    <div class="card-body {{ !$payLaterEnabled ? 'bg-light' : '' }}">
                                        {{-- Toggle Principal --}}
                                        @php $mainToggle = $categorySettings->firstWhere('key', 'booking.pay_later.enabled'); @endphp
                                        @if($mainToggle)
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <strong><i class="fas fa-info-circle"></i> Configuración Principal</strong>
                                                    <p class="mb-2">{{ $mainToggle->description }}</p>
                                                    <div class="custom-control custom-switch custom-switch-lg">
                                                        <input type="hidden" name="settings[{{ $mainToggle->key }}]" value="0">
                                                        <input type="checkbox"
                                                            class="custom-control-input"
                                                            id="setting_{{ $mainToggle->key }}"
                                                            name="settings[{{ $mainToggle->key }}]"
                                                            value="1"
                                                            {{ $mainToggle->value ? 'checked' : '' }}
                                                            onchange="togglePayLaterSettings(this.checked)">
                                                        <label class="custom-control-label font-weight-bold" for="setting_{{ $mainToggle->key }}">
                                                            <span class="enabled-text">Habilitar Sistema Pay-Later</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Settings Dependientes --}}
                                        <fieldset id="payLaterFields" {{ !$payLaterEnabled ? 'disabled' : '' }}>
                                            <div class="row">
                                                @foreach($categorySettings as $setting)
                                                @if(str_contains($setting->key, 'pay_later') && $setting->key !== 'booking.pay_later.enabled')
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="setting_{{ $setting->key }}" class="{{ !$payLaterEnabled ? 'text-muted' : '' }}">
                                                            {{ $setting->label }}
                                                            @if($setting->description)
                                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                                            @endif
                                                        </label>

                                                        @if($setting->type === 'integer')
                                                        <input type="number"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}"
                                                            min="{{ $setting->key === 'booking.pay_later.link_expires_hours' ? 24 : 1 }}"
                                                            max="{{ $setting->key === 'booking.pay_later.cancel_hours_before_tour' ? 72 : ($setting->key === 'booking.pay_later.link_expires_hours' ? 168 : 30) }}">
                                                        @elseif($setting->type === 'time')
                                                        <input type="time"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}">
                                                        @else
                                                        <input type="text"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}">
                                                        @endif

                                                        @error("settings.{$setting->key}")
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @endif
                                                @endforeach

                                                {{-- Operations Email & Time --}}
                                                @foreach($categorySettings as $setting)
                                                @if($setting->key === 'booking.operations_email' || $setting->key === 'booking.operations_report_time')
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="setting_{{ $setting->key }}" class="{{ !$payLaterEnabled ? 'text-muted' : '' }}">
                                                            {{ $setting->label }}
                                                            @if($setting->description)
                                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                                            @endif
                                                        </label>

                                                        @if($setting->type === 'email')
                                                        <input type="email"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}">
                                                        @elseif($setting->type === 'time')
                                                        <input type="time"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}">
                                                        @else
                                                        <input type="text"
                                                            class="form-control {{ !$payLaterEnabled ? 'bg-light' : '' }}"
                                                            id="setting_{{ $setting->key }}"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}">
                                                        @endif

                                                        @error("settings.{$setting->key}")
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </fieldset>

                                        @if(!$payLaterEnabled)
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Sistema Desactivado</strong><br>
                                            Activa el sistema "Reserva Ahora y Paga Después" para configurar estas opciones.
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Exchange Rates --}}
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-exchange-alt"></i> Tipos de Cambio</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($categorySettings as $setting)
                                        @if(str_contains($setting->key, 'exchange_rate'))
                                        @php
                                        $showField = false;
                                        if (str_contains($setting->key, 'usd_to_crc')) {
                                        $usdEnabled = $categorySettings->firstWhere('key', 'payment.currency.usd')?->value ?? false;
                                        $crcEnabled = $categorySettings->firstWhere('key', 'payment.currency.crc')?->value ?? false;
                                        $showField = $usdEnabled && $crcEnabled;
                                        } elseif (str_contains($setting->key, 'eur_to_usd')) {
                                        $eurEnabled = $categorySettings->firstWhere('key', 'payment.currency.eur')?->value ?? false;
                                        $usdEnabled = $categorySettings->firstWhere('key', 'payment.currency.usd')?->value ?? false;
                                        $showField = $eurEnabled && $usdEnabled;
                                        }
                                        @endphp
                                        <div class="col-md-6 exchange-rate-field" data-rate="{{ str_replace('payment.exchange_rate.', '', $setting->key) }}" style="{{ $showField ? '' : 'display:none;' }}">
                                            <div class="form-group">
                                                <label for="setting_{{ $setting->key }}">
                                                    {{ $setting->label }}
                                                    @if($setting->description)
                                                    <small class="text-muted d-block">{{ $setting->description }}</small>
                                                    @endif
                                                </label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="setting_{{ $setting->key }}"
                                                    name="settings[{{ $setting->key }}]"
                                                    value="{{ $setting->value }}"
                                                    placeholder="Ej: 520">
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Defaults --}}
                            <div class="row">
                                @foreach($categorySettings as $setting)
                                @if($setting->key === 'payment.default_gateway')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="setting_{{ $setting->key }}">
                                            {{ $setting->label }}
                                            @if($setting->description)
                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                            @endif
                                        </label>
                                        <select class="form-control"
                                            id="setting_{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]">
                                            @foreach($categorySettings as $gatewaySetting)
                                            @if(str_contains($gatewaySetting->key, 'gateway.') && $gatewaySetting->value)
                                            @php
                                            $gatewayValue = str_replace('payment.gateway.', '', $gatewaySetting->key);
                                            @endphp
                                            <option value="{{ $gatewayValue }}" {{ $setting->value === $gatewayValue ? 'selected' : '' }}>
                                                {{ $gatewaySetting->label }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @elseif($setting->key === 'payment.default_currency')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="setting_{{ $setting->key }}">
                                            {{ $setting->label }}
                                            @if($setting->description)
                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                            @endif
                                        </label>
                                        <select class="form-control"
                                            id="setting_{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]">
                                            @foreach($categorySettings as $currencySetting)
                                            @if(str_contains($currencySetting->key, 'currency.') && $currencySetting->value)
                                            @php
                                            $currencyValue = strtoupper(str_replace('payment.currency.', '', $currencySetting->key));
                                            @endphp
                                            <option value="{{ $currencyValue }}" {{ $setting->value === $currencyValue ? 'selected' : '' }}>
                                                {{ $currencySetting->label }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @else
                            {{-- Standard layout for other categories --}}
                            <div class="row">
                                @foreach($categorySettings as $setting)

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="setting_{{ $setting->key }}">
                                            {{ $setting->label }}
                                            @if($setting->description)
                                            <small class="text-muted d-block">{{ $setting->description }}</small>
                                            @endif
                                        </label>

                                        @if($setting->type === 'boolean')
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                            <input type="checkbox"
                                                class="custom-control-input"
                                                id="setting_{{ $setting->key }}"
                                                name="settings[{{ $setting->key }}]"
                                                value="1"
                                                {{ $setting->value ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="setting_{{ $setting->key }}">
                                                {{ $setting->value ? 'Activado' : 'Desactivado' }}
                                            </label>
                                        </div>
                                        @elseif($setting->type === 'integer')
                                        <input type="number"
                                            class="form-control"
                                            id="setting_{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}"
                                            min="0">
                                        @elseif($setting->type === 'email')
                                        <input type="email"
                                            class="form-control"
                                            id="setting_{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}">
                                        @elseif($setting->type === 'json')
                                        {{-- Hide JSON fields from user view --}}
                                        <input type="hidden"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ is_array($setting->value) ? json_encode($setting->value) : $setting->value }}">
                                        @else
                                        <input type="text"
                                            class="form-control"
                                            id="setting_{{ $setting->key }}"
                                            name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}">
                                        @endif

                                        @error("settings.{$setting->key}")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('admin.home') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .form-group label {
        font-weight: 600;
    }

    .custom-control-label {
        font-weight: normal;
    }

    .card-outline {
        border-top: 3px solid;
    }

    .card-outline.card-primary {
        border-top-color: #007bff;
    }

    .card-outline.card-success {
        border-top-color: #28a745;
    }

    .card-outline.card-warning {
        border-top-color: #ffc107;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // ==========================================
        // CHANGE DETECTION - Only submit modified settings
        // ==========================================
        const originalValues = {};

        // Capture all initial values
        $('input[name^="settings["], select[name^="settings["]').each(function() {
            const $input = $(this);
            const name = $input.attr('name');

            // Skip hidden inputs that are paired with checkboxes
            if ($input.attr('type') === 'hidden' && $input.next('input[type="checkbox"]').length) {
                return;
            }

            if ($input.attr('type') === 'checkbox') {
                originalValues[name] = $input.is(':checked') ? $input.val() : '0';
            } else {
                originalValues[name] = $input.val();
            }
        });

        // On form submit, remove unchanged inputs
        $('form').on('submit', function(e) {
            let changedCount = 0;
            const toRemove = [];

            $('input[name^="settings["], select[name^="settings["]').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                let currentValue;

                // Skip hidden inputs paired with checkboxes
                if ($input.attr('type') === 'hidden' && $input.next('input[type="checkbox"]').length) {
                    return;
                }

                if ($input.attr('type') === 'checkbox') {
                    currentValue = $input.is(':checked') ? $input.val() : '0';
                    const $hidden = $input.prev('input[type="hidden"]');

                    if (currentValue !== originalValues[name]) {
                        changedCount++;
                    } else {
                        // Mark for removal
                        toRemove.push($hidden[0]);
                        toRemove.push($input[0]);
                    }
                } else {
                    currentValue = $input.val();

                    if (currentValue !== originalValues[name]) {
                        changedCount++;
                    } else {
                        // Mark for removal
                        toRemove.push($input[0]);
                    }
                }
            });

            // Remove unchanged inputs
            $(toRemove).remove();

            console.log(`✅ Submitting ${changedCount} changed settings (removed ${toRemove.length} unchanged inputs)`);
        });

        // ==========================================
        // CURRENCY & EXCHANGE RATES
        // ==========================================
        // Toggle exchange rate fields based on currency selection
        $('.currency-toggle').on('change', function() {
            updateExchangeRateVisibility();
        });

        function updateExchangeRateVisibility() {
            const usdEnabled = $('#setting_payment\\.currency\\.usd').is(':checked');
            const crcEnabled = $('#setting_payment\\.currency\\.crc').is(':checked');
            const eurEnabled = $('#setting_payment\\.currency\\.eur').is(':checked');

            // USD to CRC
            if (usdEnabled && crcEnabled) {
                $('[data-rate="usd_to_crc"]').slideDown();
            } else {
                $('[data-rate="usd_to_crc"]').slideUp();
            }

            // EUR to USD
            if (eurEnabled && usdEnabled) {
                $('[data-rate="eur_to_usd"]').slideDown();
            } else {
                $('[data-rate="eur_to_usd"]').slideUp();
            }
        }

        // Pay-Later Settings Toggle
        function togglePayLaterSettings(enabled) {
            const fieldset = document.getElementById('payLaterFields');
            const card = document.querySelector('[data-target="#payLaterSettings"]').closest('.card');
            const cardBody = document.getElementById('payLaterSettings').querySelector('.card-body');

            if (enabled) {
                fieldset.removeAttribute('disabled');
                card.classList.remove('card-secondary');
                card.classList.add('card-info');
                cardBody.classList.remove('bg-light');

                // Enable all inputs
                fieldset.querySelectorAll('input, select').forEach(input => {
                    input.classList.remove('bg-light');
                });

                // Update labels
                fieldset.querySelectorAll('label').forEach(label => {
                    label.classList.remove('text-muted');
                });
            } else {
                fieldset.setAttribute('disabled', 'disabled');
                card.classList.remove('card-info');
                card.classList.add('card-secondary');
                cardBody.classList.add('bg-light');

                // Disable styling
                fieldset.querySelectorAll('input, select').forEach(input => {
                    input.classList.add('bg-light');
                });

                // Mute labels
                fieldset.querySelectorAll('label').forEach(label => {
                    label.classList.add('text-muted');
                });
            }
        }

        // Initialize on page load
        updateExchangeRateVisibility();
    });
</script>
@stop
```