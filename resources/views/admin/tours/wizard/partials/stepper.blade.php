{{-- resources/views/admin/tours/wizard/partials/stepper.blade.php --}}

@php
    $stepNames = [
        1 => __('m_tours.tour.wizard.steps.details'),
        2 => __('m_tours.tour.wizard.steps.itinerary'),
        3 => __('m_tours.tour.wizard.steps.schedules'),
        4 => __('m_tours.tour.wizard.steps.amenities'),
        5 => __('m_tours.tour.wizard.steps.prices'),
        6 => __('m_tours.tour.wizard.steps.summary'),
    ];

    $stepIcons = [
        1 => 'fas fa-info-circle',
        2 => 'fas fa-route',
        3 => 'fas fa-clock',
        4 => 'fas fa-check-circle',
        5 => 'fas fa-dollar-sign',
        6 => 'fas fa-eye',
    ];

    // Paso actual:
    // 1) prioridad a $step (pasado desde la vista del paso)
    // 2) si no viene, usar current_step del tour
    // 3) fallback a 1
    $currentStep = $step ?? ($tour->current_step ?? 1);

    // Es borrador o no (para decidir si bloqueamos pasos)
    $isDraft = isset($tour) ? (bool) $tour->is_draft : true;
@endphp

<div class="tour-wizard-stepper">
    <div class="stepper-container">
        @foreach($stepNames as $stepNum => $stepName)
            @php
                $isCurrent   = $stepNum == $currentStep;
                $isCompleted = $stepNum < $currentStep;

                if (isset($tour)) {
                    if ($isDraft) {
                        // Draft: solo hasta current_step
                        $isAccessible = $stepNum <= ($tour->current_step ?? 1);
                    } else {
                        // Publicado: todos los pasos accesibles
                        $isAccessible = true;
                    }
                } else {
                    // Sin tour (caso edge): usamos currentStep plano
                    $isAccessible = $stepNum <= $currentStep;
                }

                $stepClass = '';
                if ($isCurrent) {
                    $stepClass = 'current';
                } elseif ($isCompleted) {
                    $stepClass = 'completed';
                } elseif (!$isAccessible) {
                    $stepClass = 'disabled';
                }
            @endphp

            <div class="stepper-step {{ $stepClass }}" data-step="{{ $stepNum }}">
                <div class="step-connector" @if($stepNum == 1) style="visibility: hidden;" @endif></div>

                <div class="step-circle">
                    @if($isCompleted)
                        <i class="fas fa-check"></i>
                    @else
                        <i class="{{ $stepIcons[$stepNum] }}"></i>
                    @endif
                </div>

                <div class="step-label">
                    <span class="step-number">
                        {{ __('m_tours.tour.wizard.step_number', ['number' => $stepNum]) }}
                    </span>
                    <span class="step-name">{{ $stepName }}</span>
                </div>

                @if(isset($tour) && $isAccessible && !$isCurrent)
                    <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => $stepNum]) }}"
                       class="step-link">
                        {{ __('m_tours.tour.wizard.edit_step') }}
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</div>

<style>
.tour-wizard-stepper {
    background: #2d3748;
    padding: 2rem;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.stepper-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
}

.stepper-step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    text-align: center;
}

.step-connector {
    position: absolute;
    top: 24px;
    right: 50%;
    width: 100%;
    height: 2px;
    background: #4a5568;
    z-index: 0;
}

.stepper-step.completed .step-connector {
    background: #48bb78;
}

.stepper-step.current .step-connector {
    background: linear-gradient(to right, #48bb78 50%, #4a5568 50%);
}

.step-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #4a5568;
    color: #a0aec0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.stepper-step.completed .step-circle {
    background: #48bb78;
    color: white;
}

.stepper-step.current .step-circle {
    background: #667eea;
    color: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
    transform: scale(1.1);
}

.stepper-step.disabled .step-circle {
    opacity: 0.5;
}

.step-label {
    margin-top: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.step-number {
    font-size: 0.75rem;
    color: #a0aec0;
    text-transform: uppercase;
    font-weight: 600;
}

.step-name {
    font-size: 0.875rem;
    color: #cbd5e0;
    font-weight: 500;
}

.stepper-step.current .step-name {
    color: #667eea;
    font-weight: 600;
}

.stepper-step.completed .step-name {
    color: #48bb78;
}

.step-link {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #667eea;
    text-decoration: none;
    transition: color 0.2s;
}

.step-link:hover {
    color: #5a67d8;
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 992px) {
    .stepper-container {
        flex-wrap: wrap;
    }

    .stepper-step {
        min-width: 33.333%;
        margin-bottom: 2rem;
    }

    .tour-wizard-stepper {
        padding: 1.5rem;
        margin-top: 1rem;
    }
}

@media (max-width: 576px) {
    .stepper-step {
        min-width: 50%;
    }

    .step-name {
        font-size: 0.75rem;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .step-connector {
        top: 20px;
    }

    .tour-wizard-stepper {
        padding: 1rem;
        margin-top: 0.75rem;
    }
}
</style>
