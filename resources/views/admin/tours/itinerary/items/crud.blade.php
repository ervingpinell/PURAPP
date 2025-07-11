@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h4>Ítems de Itinerario</h4>
    <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrarItem">
        <i class="fas fa-plus"></i> Añadir Ítem
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="bg-secondary text-white">
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->item_id }}</td>
                <td>{{ $item->title }}</td>
                <td>
                    <div class="desc-wrapper">
                        <div class="desc-truncate" id="desc-{{ $item->item_id }}">
                            {{ $item->description }}
                        </div>
                        <button class="btn-toggle-desc" data-target="desc-{{ $item->item_id }}">Ver más</button>
                    </div>
                </td>
                <td>
                    @if ($item->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarItem{{ $item->item_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    @php
                        $active    = $item->is_active;
                        $btnClass  = $active ? 'btn-danger' : 'btn-success';
                        $icon      = $active ? 'fa-times-circle' : 'fa-check-circle';
                        $confirm   = $active ? '¿Desactivar este ítem?' : '¿Activar este ítem?';
                    @endphp

                    <form action="{{ route('admin.tours.itinerary_items.destroy', $item->item_id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm {{ $btnClass }}"
                                onclick="return confirm('{{ $confirm }}')"
                                title="{{ $active ? 'Desactivar ítem' : 'Activar ítem' }}">
                            <i class="fas {{ $icon }}"></i>
                        </button>
                    </form>

                </td>
            </tr>

            <!-- Modal editar ítem -->
            <div class="modal fade" id="modalEditarItem{{ $item->item_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.tours.itinerary_items.update', $item->item_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Ítem</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Título</label>
                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Descripción</label>
                                    <textarea name="description" class="form-control" required>{{ $item->description }}</textarea>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Actualizar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal registrar ítem -->
<div class="modal fade" id="modalRegistrarItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.tours.itinerary_items.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Ítem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('css')
<style>
    .desc-wrapper {
        max-width: 240px;
        min-width: 100px;
        word-wrap: break-word;
    }

    .desc-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        max-height: 2em;
        transition: all 0.3s ease;
        word-break: break-word;
        white-space: normal;
    }

    .desc-expanded {
        -webkit-line-clamp: unset !important;
        max-height: none !important;
    }

    .btn-toggle-desc {
        font-size: 0.75rem;
        color: #007bff;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .desc-wrapper {
            max-width: 140px;
        }

        .desc-truncate {
            font-size: 0.8rem;
        }

        .btn-toggle-desc {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const buttons = document.querySelectorAll(".btn-toggle-desc");

        buttons.forEach(button => {
            button.addEventListener("click", function () {
                const targetId = this.getAttribute("data-target");
                const desc = document.getElementById(targetId);

                desc.classList.toggle("desc-expanded");
                this.textContent = desc.classList.contains("desc-expanded") ? "Ver menos" : "Ver más";
            });
        });
    });
</script>
@endpush
