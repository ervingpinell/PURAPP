{{-- resources/views/admin/roles/permissions.blade.php --}}
@extends('adminlte::page')

@section('title', 'Permisos por Rol - Kanban')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="mb-0"><i class="fas fa-columns text-primary"></i> Gestión de Permisos - Vista Kanban</h1>
      <p class="text-muted mb-0">Organiza los permisos visualmente por módulos</p>
    </div>
    <div>
      <button class="btn btn-success" onclick="saveAllPermissions()">
        <i class="fas fa-save"></i> Guardar Todo
      </button>
    </div>
  </div>
@endsection

@section('content')
  @php
    // ====== ROLES DISPONIBLES ======
    $roles = [
      ['id' => 1, 'name' => 'Super Admin', 'color' => 'danger', 'icon' => 'fa-crown'],
      ['id' => 2, 'name' => 'Administrador', 'color' => 'primary', 'icon' => 'fa-user-tie'],
      ['id' => 3, 'name' => 'Operador', 'color' => 'success', 'icon' => 'fa-user-cog'],
      ['id' => 4, 'name' => 'Vendedor', 'color' => 'warning', 'icon' => 'fa-user-tag'],
      ['id' => 5, 'name' => 'Soporte', 'color' => 'info', 'icon' => 'fa-headset'],
    ];

    // ====== MÓDULOS CON PERMISOS ======
    $modules = [
      'Usuarios' => [
        'icon' => 'fa-users',
        'color' => '#667eea',
        'perms' => [
          'users.view' => 'Ver usuarios',
          'users.create' => 'Crear usuarios',
          'users.edit' => 'Editar usuarios',
          'users.delete' => 'Eliminar usuarios',
        ]
      ],
      'Tours' => [
        'icon' => 'fa-map-marked-alt',
        'color' => '#28a745',
        'perms' => [
          'tours.view' => 'Ver tours',
          'tours.create' => 'Crear tours',
          'tours.edit' => 'Editar tours',
          'tours.delete' => 'Eliminar tours',
          'tours.prices' => 'Gestionar precios',
        ]
      ],
      'Reservas' => [
        'icon' => 'fa-calendar-check',
        'color' => '#17a2b8',
        'perms' => [
          'bookings.view' => 'Ver reservas',
          'bookings.create' => 'Crear reservas',
          'bookings.edit' => 'Editar reservas',
          'bookings.delete' => 'Cancelar reservas',
          'bookings.export' => 'Exportar reservas',
        ]
      ],
      'Horarios' => [
        'icon' => 'fa-clock',
        'color' => '#ffc107',
        'perms' => [
          'schedules.view' => 'Ver horarios',
          'schedules.manage' => 'Gestionar horarios',
          'capacity.view' => 'Ver disponibilidad',
          'capacity.manage' => 'Ajustar capacidad',
        ]
      ],
      'Reportes' => [
        'icon' => 'fa-chart-line',
        'color' => '#6f42c1',
        'perms' => [
          'reports.view' => 'Ver reportes',
          'reports.export' => 'Exportar reportes',
        ]
      ],
      'Reseñas' => [
        'icon' => 'fa-star',
        'color' => '#fd7e14',
        'perms' => [
          'reviews.view' => 'Ver reseñas',
          'reviews.manage' => 'Gestionar reseñas',
          'reviews.reply' => 'Responder reseñas',
        ]
      ],
      'Configuración' => [
        'icon' => 'fa-cog',
        'color' => '#6c757d',
        'perms' => [
          'settings.view' => 'Ver configuración',
          'settings.edit' => 'Editar configuración',
        ]
      ],
    ];

    // Permisos activos por rol (MOCK)
    $activePermissions = [
      1 => array_keys(array_merge(...array_column($modules, 'perms'))),
      2 => ['users.view', 'tours.view', 'tours.edit', 'bookings.view', 'bookings.create', 'reports.view'],
      3 => ['bookings.view', 'bookings.create', 'capacity.view', 'tours.view'],
      4 => ['bookings.create', 'bookings.view', 'tours.view'],
      5 => ['bookings.view', 'reviews.view', 'reviews.reply'],
    ];
  @endphp

  {{-- Selector de Rol --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-4">
          <label class="font-weight-bold mb-2">
            <i class="fas fa-user-shield"></i> Seleccionar Rol:
          </label>
          <select id="roleSelector" class="form-control form-control-lg" onchange="loadRolePermissions(this.value)">
            <option value="">-- Elige un rol --</option>
            @foreach($roles as $role)
              <option value="{{ $role['id'] }}" data-color="{{ $role['color'] }}" data-icon="{{ $role['icon'] }}">
                {{ $role['name'] }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8">
          <div id="roleInfo" class="d-none">
            <div class="role-selected-banner">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <div class="role-avatar" id="roleAvatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="ml-3">
                    <h4 class="mb-0" id="roleName">Rol</h4>
                    <small class="text-muted">Editando permisos</small>
                  </div>
                </div>
                <div class="text-right">
                  <h2 class="mb-0 text-primary" id="totalPerms">0</h2>
                  <small class="text-muted">Permisos activos</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Vista Kanban --}}
  <div id="kanbanBoard" class="d-none">
    <div class="kanban-controls mb-3">
      <div class="btn-group" role="group">
        <button class="btn btn-outline-success" onclick="selectAllCards()">
          <i class="fas fa-check-double"></i> Marcar todos
        </button>
        <button class="btn btn-outline-secondary" onclick="deselectAllCards()">
          <i class="fas fa-times"></i> Quitar todos
        </button>
      </div>
      <div class="float-right">
        <input type="text" class="form-control" placeholder="🔍 Buscar..." oninput="filterKanban(this.value)">
      </div>
    </div>

    <div class="kanban-container">
      @foreach($modules as $moduleName => $module)
        @php $moduleId = Str::slug($moduleName); @endphp
        <div class="kanban-column" data-module="{{ $moduleId }}">
          <div class="kanban-header" style="background: {{ $module['color'] }}">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <i class="fas {{ $module['icon'] }} mr-2"></i>
                <h5 class="mb-0">{{ $moduleName }}</h5>
              </div>
              <span class="badge badge-light" id="count-{{ $moduleId }}">0</span>
            </div>
            <div class="kanban-actions mt-2">
              <button class="btn btn-sm btn-light" onclick="toggleColumn('{{ $moduleId }}', true)">
                <i class="fas fa-check"></i> Todos
              </button>
              <button class="btn btn-sm btn-outline-light" onclick="toggleColumn('{{ $moduleId }}', false)">
                <i class="fas fa-times"></i> Ninguno
              </button>
            </div>
          </div>

          <div class="kanban-body" id="column-{{ $moduleId }}">
            @foreach($module['perms'] as $permKey => $permLabel)
              <div class="kanban-card" data-perm="{{ $permKey }}" data-label="{{ Str::lower($permLabel) }}">
                <div class="card-checkbox">
                  <input type="checkbox" 
                         class="perm-check" 
                         id="perm_{{ Str::slug($permKey) }}"
                         value="{{ $permKey }}"
                         onchange="updateCounters()">
                </div>
                <div class="card-content" onclick="toggleCard('{{ Str::slug($permKey) }}')">
                  <h6 class="card-title mb-1">{{ $permLabel }}</h6>
                  <small class="text-muted">
                    <code>{{ $permKey }}</code>
                  </small>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Estado vacío --}}
  <div id="emptyState">
    <div class="empty-kanban">
      <div class="empty-icon">
        <i class="fas fa-columns"></i>
      </div>
      <h3>Selecciona un rol para comenzar</h3>
      <p class="text-muted">El tablero Kanban se mostrará cuando elijas un rol</p>
    </div>
  </div>
@endsection

@push('css')
<style>
  /* Role Banner */
  .role-selected-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
  }
  .role-avatar {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
  }

  /* Kanban Container */
  .kanban-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: 500px;
  }
  .kanban-container::-webkit-scrollbar {
    height: 8px;
  }
  .kanban-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  .kanban-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
  }

  /* Kanban Column */
  .kanban-column {
    min-width: 300px;
    max-width: 320px;
    flex-shrink: 0;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
  }
  .kanban-column.hidden {
    display: none;
  }

  .kanban-header {
    padding: 1rem;
    color: white;
  }
  .kanban-header h5 {
    color: white;
    font-weight: bold;
  }
  .kanban-actions {
    display: flex;
    gap: 0.5rem;
  }
  .kanban-actions .btn {
    flex: 1;
    font-size: 0.85rem;
  }

  .kanban-body {
    padding: 1rem;
    max-height: 600px;
    overflow-y: auto;
  }
  .kanban-body::-webkit-scrollbar {
    width: 6px;
  }
  .kanban-body::-webkit-scrollbar-track {
    background: transparent;
  }
  .kanban-body::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
  }

  /* Kanban Card */
  .kanban-card {
    background: white;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: start;
    gap: 0.75rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
  }
  .kanban-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
  }
  .kanban-card.hidden {
    display: none !important;
  }
  .kanban-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
  }

  .card-checkbox {
    flex-shrink: 0;
    padding-top: 2px;
  }
  .card-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #667eea;
  }

  .card-content {
    flex: 1;
  }
  .card-title {
    font-weight: 600;
    color: #2d3748;
  }
  .card-content code {
    font-size: 0.75rem;
    color: #718096;
    background: #edf2f7;
    padding: 2px 6px;
    border-radius: 4px;
  }

  /* Empty State */
  .empty-kanban {
    text-align: center;
    padding: 5rem 2rem;
  }
  .empty-icon {
    width: 150px;
    height: 150px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
  }
  .empty-kanban h3 {
    color: #2d3748;
    margin-bottom: 0.5rem;
  }

  /* Controls */
  .kanban-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .kanban-controls input {
    width: 250px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .kanban-column {
      min-width: 280px;
    }
    .role-selected-banner {
      text-align: center;
    }
    .role-selected-banner .d-flex {
      flex-direction: column;
      gap: 1rem;
    }
  }
</style>
@endpush

@push('js')
<script>
  // Datos mock
  const mockPermissions = @json($activePermissions);
  const mockRoles = @json($roles);

  let currentRoleId = null;

  // Cargar permisos del rol
  function loadRolePermissions(roleId) {
    if (!roleId) {
      $('#kanbanBoard').addClass('d-none');
      $('#emptyState').show();
      $('#roleInfo').addClass('d-none');
      currentRoleId = null;
      return;
    }

    currentRoleId = roleId;
    const role = mockRoles.find(r => r.id == roleId);
    const perms = mockPermissions[roleId] || [];

    // Actualizar banner
    $('#roleInfo').removeClass('d-none');
    $('#roleName').text(role.name);
    $('#roleAvatar').html(`<i class="fas ${role.icon}"></i>`);
    $('#roleAvatar').css('background', `var(--${role.color})`);

    // Mostrar tablero
    $('#emptyState').hide();
    $('#kanbanBoard').removeClass('d-none');

    // Desmarcar todos
    $('.perm-check').prop('checked', false);
    $('.kanban-card').removeClass('selected');

    // Marcar permisos activos
    perms.forEach(perm => {
      const checkbox = $(`#perm_${perm.replace(/\./g, '-')}`);
      checkbox.prop('checked', true);
      checkbox.closest('.kanban-card').addClass('selected');
    });

    updateCounters();
  }

  // Toggle card
  function toggleCard(permId) {
    const checkbox = $(`#${permId}`);
    checkbox.prop('checked', !checkbox.prop('checked'));
    checkbox.trigger('change');
  }

  // Actualizar contadores
  function updateCounters() {
    // Total general
    const total = $('.perm-check:checked').length;
    $('#totalPerms').text(total);

    // Por columna
    $('.kanban-column').each(function() {
      const column = $(this);
      const moduleId = column.data('module');
      const checked = column.find('.perm-check:checked').length;
      const totalInColumn = column.find('.perm-check').length;
      
      $(`#count-${moduleId}`).text(`${checked}/${totalInColumn}`);
    });

    // Actualizar clases de cards
    $('.perm-check').each(function() {
      const card = $(this).closest('.kanban-card');
      if ($(this).is(':checked')) {
        card.addClass('selected');
      } else {
        card.removeClass('selected');
      }
    });
  }

  // Marcar/desmarcar columna
  function toggleColumn(moduleId, checked) {
    $(`#column-${moduleId} .perm-check`).prop('checked', checked);
    updateCounters();
  }

  // Marcar/desmarcar todo
  function selectAllCards() {
    $('.perm-check').prop('checked', true);
    updateCounters();
  }

  function deselectAllCards() {
    $('.perm-check').prop('checked', false);
    updateCounters();
  }

  // Filtrar
  function filterKanban(query) {
    query = query.toLowerCase().trim();

    if (!query) {
      $('.kanban-card, .kanban-column').show();
      return;
    }

    $('.kanban-column').each(function() {
      const column = $(this);
      let hasVisible = false;

      column.find('.kanban-card').each(function() {
        const card = $(this);
        const perm = card.data('perm').toLowerCase();
        const label = card.data('label');
        const match = perm.includes(query) || label.includes(query);

        card.toggle(match);
        if (match) hasVisible = true;
      });

      column.toggle(hasVisible);
    });
  }

  // Guardar
  function saveAllPermissions() {
    if (!currentRoleId) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Oops...',
          text: 'Selecciona un rol primero',
          confirmButtonText: 'Entendido'
        });
      } else {
        alert('Selecciona un rol primero');
      }
      return;
    }

    const selected = $('.perm-check:checked').map(function() {
      return $(this).val();
    }).get();

    const role = mockRoles.find(r => r.id == currentRoleId);

    console.log('💾 Guardando permisos:', {
      roleId: currentRoleId,
      role: role.name,
      permisos: selected
    });

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: '¡Permisos guardados!',
        html: `
          <div class="text-left">
            <div class="alert alert-info">
              <strong><i class="fas ${role.icon} mr-2"></i>${role.name}</strong>
            </div>
            <p><strong>Total de permisos:</strong> ${selected.length}</p>
            <div style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 1rem; border-radius: 8px;">
              ${selected.map(p => `<span class="badge badge-primary m-1">${p}</span>`).join('')}
            </div>
          </div>
        `,
        confirmButtonText: 'Perfecto',
        confirmButtonColor: '#667eea',
        width: 700
      });
    } else {
      alert(`✓ Guardado\n\nRol: ${role.name}\nPermisos: ${selected.length}\n\n${selected.join(', ')}`);
    }
  }

  // Init
  $(document).ready(function() {
    updateCounters();
  });
</script>
@endpush