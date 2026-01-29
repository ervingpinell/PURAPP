{{-- resources/views/admin/tours/wizard/partials/stepper.blade.php --}}

@php
$stepNames = [
1 => __('m_tours.product.wizard.steps.details'),
2 => __('m_tours.product.wizard.steps.itinerary'),
3 => __('m_tours.product.wizard.steps.schedules'),
4 => __('m_tours.product.wizard.steps.amenities'),
5 => __('m_tours.product.wizard.steps.prices'),
6 => __('m_tours.product.wizard.steps.summary'),
];

$stepIcons = [
1 => 'fas fa-info-circle',
2 => 'fas fa-route',
3 => 'fas fa-clock',
4 => 'fas fa-check-circle',
5 => 'fas fa-dollar-sign',
6 => 'fas fa-eye',
];

$currentStep = $step ?? ($product->current_step ?? 1);
$isDraft = isset($product) ? (bool) $product->is_draft : true;
@endphp

<div class="product-wizard-stepper">
    <div class="stepper-container">
        @foreach($stepNames as $stepNum => $stepName)
        @php
        $isCurrent = $stepNum == $currentStep;
        $isCompleted = $stepNum < $currentStep;

            if (isset($product)) {
            $isAccessible=$isDraft
            ? $stepNum <=($product->current_step ?? 1)
            : true;
            } else {
            $isAccessible = $stepNum <= $currentStep;
                }

                $stepClass='' ;
                if ($isCurrent) {
                $stepClass='current' ;
                } elseif ($isCompleted) {
                $stepClass='completed' ;
                } elseif (!$isAccessible) {
                $stepClass='disabled' ;
                }
                @endphp

                <div class="stepper-step {{ $stepClass }}" data-step="{{ $stepNum }}">
                <div class="step-connector" @if($stepNum===1) style="visibility:hidden" @endif></div>

                <div class="step-main">
                    <div class="step-circle">
                        @if($isCompleted)
                        <i class="fas fa-check"></i>
                        @else
                        <i class="{{ $stepIcons[$stepNum] }}"></i>
                        @endif
                    </div>

                    <div class="step-label">
                        <span class="step-number">
                            {{ __('m_tours.product.wizard.step_number', ['number' => $stepNum]) }}
                        </span>
                        <span class="step-name">{{ $stepName }}</span>
                    </div>
                </div>

                @if(isset($product) && $isAccessible && !$isCurrent)
                <a href="{{ route('admin.products.product-wizard.step', ['product' => $product, 'step' => $stepNum]) }}"
                    class="step-link">
                    {{ __('m_tours.product.wizard.edit_step') }}
                </a>
                @endif
    </div>
    @endforeach
</div>
</div>

<style>
    .product-wizard-stepper {
        background: #1f2933;
        padding: 2rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
    }

    .stepper-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
    }

    /* Paso base */
    .stepper-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        text-align: center;
    }

    /* Línea horizontal entre pasos (desktop + mobile) */
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

    /* Contenido del paso */
    .step-main {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .5rem;
        position: relative;
        z-index: 1;
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
        transition: all .25s ease;
    }

    .stepper-step.completed .step-circle {
        background: #48bb78;
        color: #fff;
    }

    .stepper-step.current .step-circle {
        background: #667eea;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.4);
        transform: scale(1.08);
    }

    .stepper-step.disabled .step-circle {
        opacity: .45;
    }

    .step-label {
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }

    .step-number {
        font-size: .75rem;
        color: #a0aec0;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: .04em;
    }

    .step-name {
        font-size: .875rem;
        color: #cbd5e0;
        font-weight: 500;
    }

    .stepper-step.current .step-name {
        color: #a5b4fc;
        font-weight: 600;
    }

    .stepper-step.completed .step-name {
        color: #48bb78;
    }

    .step-link {
        margin-top: .5rem;
        font-size: .75rem;
        color: #63b3ed;
        text-decoration: none;
        padding: .2rem .6rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(99, 179, 237, 0.4);
        transition: all .2s;
    }

    .step-link:hover {
        color: #bee3f8;
        background: rgba(37, 99, 235, 0.25);
        border-color: rgba(129, 140, 248, 0.7);
    }

    /* ==================== TABLET / MOBILE ==================== */
    @media (max-width: 992px) {
        .product-wizard-stepper {
            padding: 1.1rem .75rem;
            margin-top: .75rem;
            margin-bottom: 1rem;
        }

        /* Hacemos el stepper horizontal con scroll,
       más tipo “serpiente” pero compacto */
        .stepper-container {
            justify-content: flex-start;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: .3rem;
            -webkit-overflow-scrolling: touch;
        }

        .stepper-container::-webkit-scrollbar {
            height: 4px;
        }

        .stepper-container::-webkit-scrollbar-track {
            background: #111827;
            border-radius: 4px;
        }

        .stepper-container::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        .stepper-step {
            flex: 0 0 auto;
            min-width: 120px;
            padding: 0 .25rem;
        }

        .step-main {
            gap: .35rem;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            font-size: 1rem;
            box-shadow: none;
        }

        .stepper-step.current .step-circle {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.35);
        }

        .step-number {
            font-size: .7rem;
        }

        .step-name {
            font-size: .75rem;
            line-height: 1.2;
        }

        .step-link {
            font-size: .7rem;
            margin-top: .35rem;
            padding: .15rem .5rem;
        }
    }

    @media (max-width: 576px) {
        .product-wizard-stepper {
            padding: .9rem .55rem;
        }

        .stepper-step {
            min-width: 105px;
        }

        .step-circle {
            width: 34px;
            height: 34px;
            font-size: .9rem;
        }

        .step-number {
            font-size: .65rem;
        }

        .step-name {
            font-size: .72rem;
        }

        .step-link {
            font-size: .65rem;
            padding: .12rem .45rem;
        }
    }
</style>