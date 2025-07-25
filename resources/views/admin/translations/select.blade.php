@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if ($items->isEmpty())
            <p class="text-muted">No hay {{ $labelSingular }}s disponibles para traducir.</p>
        @else
            <ul class="list-group">
                @foreach ($items as $item)
                    @php
                        // Intentar capturar el ID real del modelo, compatible con claves personalizadas como 'tour_id'
                        $itemId = $item->getKey();
                        $hasId = !empty($itemId);

                        // Texto de visualización según tipo
                        $displayText = match($type) {
                            'tours' => $item->name ?? 'Sin nombre',
                            'itineraries' => $item->name ?? 'Sin nombre',
                            'itinerary_items' => $item->title ?? 'Sin título',
                            'amenities' => $item->name ?? 'Sin nombre',
                            'faqs' => Str::limit($item->question ?? 'Sin pregunta', 60),
                            default => 'Elemento'
                        };
                    @endphp

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $displayText }}

                        @if ($hasId)
                            <a href="{{ route('admin.translations.locale', ['type' => $type, 'id' => $itemId]) }}"
                               class="btn btn-sm btn-primary">
                                Seleccionar
                            </a>
                        @else
                            <span class="badge bg-secondary">ID no disponible</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@stop
