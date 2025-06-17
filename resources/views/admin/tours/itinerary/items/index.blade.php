@extends('adminlte::page')

@section('title', 'Ítems de Itinerario')

@section('content_header')
    <h1>Gestión de Ítems de Itinerario</h1>
@stop

@section('content')
    <div class="p-3 table-responsive">
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="fas fa-plus"></i> Nuevo Ítem
        </a>

        <table class="table table-bordered table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Orden</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->item_id }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ Str::limit($item->description, 60) }}</td>
                        <td>{{ $item->order }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                               data-bs-target="#modalEditar{{ $item->item_id }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tours.itinerary.items.destroy', $item) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Deseas eliminar este ítem?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal editar --}}
                    <x-adminlte-modal id="modalEditar{{ $item->item_id }}" title="Editar Ítem" size="lg" theme="warning" icon="fas fa-edit">
                        <form action="{{ route('admin.tours.itinerary.items.update', $item) }}" method="POST">
                            @csrf @method('PUT')

                            <x-adminlte-input name="title" label="Título" value="{{ old('title', $item->title) }}" required />
                            <x-adminlte-textarea name="description" label="Descripción">{{ old('description', $item->description) }}</x-adminlte-textarea>
                            <x-adminlte-select name="is_active" label="Estado">
                                <option value="1" {{ $item->is_active ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !$item->is_active ? 'selected' : '' }}>Inactivo</option>
                            </x-adminlte-select>

                            <x-slot name="footerSlot">
                                <button type="submit" class="btn btn-warning">Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </x-slot>
                        </form>
                    </x-adminlte-modal>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal crear --}}
    <x-adminlte-modal id="modalCrear" title="Nuevo Ítem de Itinerario" size="lg" theme="primary" icon="fas fa-plus">
        <form action="{{ route('admin.tours.itinerary.items.store') }}" method="POST">
            @csrf

            <x-adminlte-input name="title" label="Título" value="{{ old('title') }}" required />
            <x-adminlte-textarea name="description" label="Descripción">{{ old('description') }}</x-adminlte-textarea>

            <x-slot name="footerSlot">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </x-slot>
        </form>
    </x-adminlte-modal>
@endsection

@section('plugins.Sweetalert2', true)
@section('js')
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 2000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '{{ session("error") }}', showConfirmButton: false, timer: 2000 });
        @endif
    </script>
@endsection
