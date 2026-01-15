@extends('layouts.app')

@section('title', __('adminlte::adminlte.faq_title') ?? 'Preguntas Frecuentes')
@section('meta_description', __('adminlte::adminlte.meta.faq_description'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-5 fw-bold">{{ __('adminlte::adminlte.faq_title') ?? 'Preguntas Frecuentes' }}</h1>

            <div class="accordion" id="faqAccordion">
                {{-- Pregunta 1 --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            {{ __('adminlte::adminlte.faq_q1') ?? '¿Cómo puedo reservar un tour?' }}
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {{ __('adminlte::adminlte.faq_a1') ?? 'Puede reservar directamente en nuestro sitio web seleccionando el tour deseado, eligiendo la fecha y completando el formulario de pago seguro.' }}
                        </div>
                    </div>
                </div>

                {{-- Pregunta 2 --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            {{ __('adminlte::adminlte.faq_q2') ?? '¿Cuál es la política de cancelación?' }}
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {{ __('adminlte::adminlte.faq_a2') ?? 'Ofrecemos reembolso completo si cancela con 24 horas de anticipación para la mayoría de nuestros tours. Consulte los detalles específicos en cada tour.' }}
                        </div>
                    </div>
                </div>

                {{-- Pregunta 3 --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            {{ __('adminlte::adminlte.faq_q3') ?? '¿Incluyen transporte??' }}
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            {{ __('adminlte::adminlte.faq_a3') ?? 'Sí, la mayoría de nuestros tours incluyen transporte ida y vuelta desde hoteles seleccionados en la zona de La Fortuna.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p>{{ __('adminlte::adminlte.faq_more_questions') ?? '¿Tiene más preguntas?' }}</p>
                <a href="{{ route('public.contact') }}" class="btn btn-success">{{ __('adminlte::adminlte.contact_us') ?? 'Contáctenos' }}</a>
            </div>
        </div>
    </div>
</div>

{{-- FAQ Schema --}}
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [{
                "@type": "Question",
                "name": "{{ __('adminlte::adminlte.faq_q1') ?? '¿Cómo puedo reservar un tour?' }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ __('adminlte::adminlte.faq_a1') ?? 'Puede reservar directamente en nuestro sitio web seleccionando el tour deseado, eligiendo la fecha y completando el formulario de pago seguro.' }}"
                }
            },
            {
                "@type": "Question",
                "name": "{{ __('adminlte::adminlte.faq_q2') ?? '¿Cuál es la política de cancelación?' }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ __('adminlte::adminlte.faq_a2') ?? 'Ofrecemos reembolso completo si cancela con 24 horas de anticipación para la mayoría de nuestros tours. Consulte los detalles específicos en cada tour.' }}"
                }
            },
            {
                "@type": "Question",
                "name": "{{ __('adminlte::adminlte.faq_q3') ?? '¿Incluyen transporte??' }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ __('adminlte::adminlte.faq_a3') ?? 'Sí, la mayoría de nuestros tours incluyen transporte ida y vuelta desde hoteles seleccionados en la zona de La Fortuna.' }}"
                }
            }
        ]
    }
</script>
@endsection