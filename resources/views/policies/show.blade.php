@extends('layouts.app')

@section('title', optional($policy->translation(app()->getLocale()) ?? $policy->translation('es'))->title ?? $policy->name)

@section('content')
@php
    // Traducción principal con fallback a ES
    $t = $policy->translation(app()->getLocale()) ?? $policy->translation('es');
    // Solo secciones activas
    $activeSections = $policy->sections->where('is_active', true);
@endphp

{{-- CSS embebido para asegurar carga en este layout --}}
<style>
  /* Ocultar el chevron por defecto de Bootstrap (BS5) */
  .accordion-button::after { content: none !important; }

  /* Mostrar +/− según estado del botón (sin JS) */
  .accordion-button .icon-plus,
  .accordion-button .icon-minus { display: inline-block; }

  /* Estado cerrado */
  .accordion-button[aria-expanded="false"] .icon-minus { display: none !important; }
  .accordion-button[aria-expanded="false"] .icon-plus  { display: inline-block !important; }

  /* Estado abierto */
  .accordion-button[aria-expanded="true"]  .icon-plus  { display: none !important; }
  .accordion-button[aria-expanded="true"]  .icon-minus { display: inline-block !important; }

  /* Ajustes visuales */
  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }
</style>

<div class="container py-4">
    <h1 class="mb-2 big-title text-center">{{ $t?->title ?? $policy->name }}</h1>

    {{-- Descripción/intro de la categoría --}}
    @if(filled($t?->content))
      <div class="mb-4">{!! nl2br(e($t->content)) !!}</div>
    @endif

    {{-- Secciones (subtítulos) como acordeón --}}
    @if($activeSections->isNotEmpty())
      <div class="accordion" id="policySectionsAccordion">
        @foreach($activeSections as $section)
          @php
              $st  = $section->translation(app()->getLocale()) ?? $section->translation('es');
              $sid = "sec-{$policy->policy_id}-{$section->section_id}";
          @endphp

          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="heading-{{ $sid }}">
              <button
                class="accordion-button bg-white px-0 shadow-none collapsed"
                type="button"
                data-bs-toggle="collapse" data-toggle="collapse"
                data-bs-target="#collapse-{{ $sid }}" data-target="#collapse-{{ $sid }}"
                aria-expanded="false"
                aria-controls="collapse-{{ $sid }}"
              >
                <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                  <i class="fas fa-plus icon-plus"></i>
                  <i class="fas fa-minus icon-minus"></i>
                </span>
                {{ $st?->title ?? __('Sección') }}
              </button>
            </h2>
            <div
              id="collapse-{{ $sid }}"
              class="accordion-collapse collapse"
              data-bs-parent="#policySectionsAccordion" data-parent="#policySectionsAccordion"
              aria-labelledby="heading-{{ $sid }}"
            >
              <div class="accordion-body px-0">
                {!! nl2br(e($st?->content ?? '')) !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
</div>
@endsection
