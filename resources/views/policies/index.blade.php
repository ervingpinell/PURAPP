@extends('layouts.app')

@section('title', __('adminlte::adminlte.policies'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<div class="container py-4">
    <h1 class="mb-3 big-title text-center">{{ __('adminlte::adminlte.policies') }}</h1>

    @if($policies->isEmpty())
        <p class="text-muted">{{ __('No hay políticas disponibles por el momento.') }}</p>
    @else
        <div class="accordion" id="allPoliciesAccordion">
            @foreach($policies as $policy)
                @php
                    $t        = $policy->translation($locale) ?? $policy->translation('es');
                    $pid      = "policy-{$policy->policy_id}";
                    $sections = $policy->sections ?? collect();
                @endphp

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="heading-{{ $pid }}">
                        <button
                            class="accordion-button bg-white px-0 shadow-none collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $pid }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $pid }}"
                        >
                            <i class="fas fa-plus fa-fw me-2 toggle-icon" aria-hidden="true"></i>
                            {{ $t?->title ?? $policy->name }}
                        </button>
                    </h2>

                    <div id="collapse-{{ $pid }}" class="accordion-collapse collapse"
                         data-bs-parent="#allPoliciesAccordion">
                        <div class="accordion-body px-0">
                            {{-- Descripción/intro --}}
                            @if(filled($t?->content))
                                <div class="mb-3">{!! nl2br(e($t->content)) !!}</div>
                            @endif

                            {{-- Secciones (si existen) --}}
                            @if($sections->isNotEmpty())
                                <div class="accordion" id="inner-{{ $pid }}">
                                    @foreach($sections as $section)
                                        @php
                                            $st  = $section->translation($locale) ?? $section->translation('es');
                                            $sid = "sec-{$policy->policy_id}-{$section->section_id}";
                                        @endphp

                                        <div class="accordion-item border-0 border-top">
                                            <h2 class="accordion-header" id="heading-{{ $sid }}">
                                                <button
                                                    class="accordion-button bg-white px-0 shadow-none collapsed"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ $sid }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse-{{ $sid }}"
                                                >
                                                    <i class="fas fa-plus fa-fw me-2 toggle-icon" aria-hidden="true"></i>
                                                    {{ $st?->title ?? __('Sección') }}
                                                </button>
                                            </h2>

                                            <div id="collapse-{{ $sid }}" class="accordion-collapse collapse"
                                                 data-bs-parent="#inner-{{ $pid }}">
                                                <div class="accordion-body px-0">
                                                    {!! nl2br(e($st?->content ?? '')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('css')
<style>
  /* Quitar chevron por defecto de Bootstrap */
  .accordion-button::after { content: none !important; }

  /* Estética */
  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }
</style>
@endpush

@push('js')
<script>
(function () {
  // Devuelve el botón que controla un collapse por ID
  function getButtonFor(id) {
    return document.querySelector(`[data-bs-target="#${id}"]`);
  }

  // Cambia el ícono del botón a + o −
  function setIcon(btn, isOpen) {
    if (!btn) return;
    const icon = btn.querySelector('.toggle-icon');
    if (!icon) return;
    icon.classList.toggle('fa-plus', !isOpen);
    icon.classList.toggle('fa-minus', isOpen);
  }

  // Inicializa todos los collapses del documento (nivel 1 y anidados)
  document.querySelectorAll('.accordion-collapse').forEach((col) => {
    const btn = getButtonFor(col.id);
    // Estado inicial por si alguno viene abierto (clase .show)
    setIcon(btn, col.classList.contains('show'));

    // Al EMPEZAR a abrir/cerrar, alterna icono inmediatamente…
    col.addEventListener('show.bs.collapse', () => setIcon(btn, true));
    col.addEventListener('hide.bs.collapse', () => setIcon(btn, false));

    // …y al TERMINAR la transición, refuerza estado final (anti carreras)
    col.addEventListener('shown.bs.collapse', () => setIcon(btn, true));
    col.addEventListener('hidden.bs.collapse', () => setIcon(btn, false));

    // Si el acordeón cierra automáticamente hermanos, ponles '+'
    col.addEventListener('show.bs.collapse', () => {
      const parent = col.closest('.accordion');
      if (!parent) return;
      parent.querySelectorAll('.accordion-collapse.show').forEach((openCol) => {
        if (openCol === col) return;
        const otherBtn = getButtonFor(openCol.id);
        setIcon(otherBtn, false);
      });
    });
  });
})();
</script>
@endpush
