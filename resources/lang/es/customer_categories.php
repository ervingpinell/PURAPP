<?php

return [
    'ui' => [
        'page_title_index'  => 'Categorías de Clientes',
        'page_title_create' => 'Nueva Categoría de Cliente',
        'page_title_edit'   => 'Editar Categoría',
        'header_index'      => 'Categorías de Clientes',
        'header_create'     => 'Nueva Categoría de Cliente',
        'header_edit'       => 'Editar Categoría: :name',
        'info_card_title'   => 'Información',

        // Nuevas para index/listado
        'list_title'        => 'Listado de Categorías',
        'empty_list'        => 'No hay categorías registradas.',
    ],

    'buttons' => [
        'new_category' => 'Nueva Categoría',
        'save'         => 'Guardar',
        'update'       => 'Actualizar',
        'cancel'       => 'Cancelar',
        'back'         => 'Volver',
        'delete'       => 'Eliminar',
        'edit'         => 'Editar',
    ],

    'table' => [
        'name'     => 'Nombre',
        'age_from' => 'Edad desde',
        'age_to'   => 'Edad hasta',
        'range'    => 'Rango',
        'active'   => 'Activo',
        'actions'  => 'Acciones',

        // Nuevas usadas en el index
        'order'    => 'Orden',
        'slug'     => 'Slug',
    ],

    'form' => [
    'translations' => [
    'title'          => 'Traducciones de nombre',
    'auto_translate' => 'Traducir automáticamente los demás idiomas (DeepL)',
    'regen_missing'  => 'Rellenar automáticamente los vacíos (DeepL)',
    'at_least_first' => 'Debes completar al menos el primer idioma.',
    'locale_hint'    => 'Traducción para el locale :loc.',
  ],
        'name' => [
            'label'       => 'Nombre',
            'placeholder' => 'Ej: Adulto, Niño, Infante',
            'required'    => 'El nombre es obligatorio',
        ],
        'slug' => [
            'label'       => 'Slug (identificador único)',
            'placeholder' => 'Ej: adult, child, infant',
            'title'       => 'Solo minúsculas, números, guiones y guiones bajos',
            'helper'      => 'Solo letras minúsculas, números, guiones (-) y guiones bajos (_)',
        ],
        'age_from' => [
            'label'       => 'Edad desde',
            'placeholder' => 'Ej: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Edad hasta',
            'placeholder'   => 'Ej: 2, 12, 64 (dejar vacío para “sin límite”)',
            'hint_no_limit' => 'dejar vacío para “sin límite”',
        ],
        'order' => [
            'label'  => 'Orden de Visualización',
            'helper' => 'Determina el orden en que aparecen las categorías (menor = primero)',
        ],
        'active' => [
            'label'  => 'Categoría activa',
            'helper' => 'Solo las categorías activas se muestran en los formularios de reserva',
        ],
        'min_per_booking' => [
            'label'       => 'Mínimo por reserva',
            'placeholder' => 'Ej: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Máximo por reserva',
            'placeholder' => 'Ej: 10 (dejar vacío para “sin límite”)',
        ],
    ],

    'states' => [
        'active'   => 'Activo',
        'inactive' => 'Inactivo',
    ],

    'alerts' => [
        'success_created' => 'Categoría creada correctamente.',
        'success_updated' => 'Categoría actualizada correctamente.',
        'success_deleted' => 'Categoría eliminada correctamente.',
        'warning_title'  => 'Advertencia',
        'warning_text'   => 'Eliminar una categoría que esté en uso en tours o reservas puede causar problemas. Se recomienda desactivarla en lugar de eliminarla.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirmar Eliminación',
            'text'    => '¿Estás seguro de eliminar la categoría :name?',
            'caution' => 'Esta acción no se puede deshacer.',
        ],
    ],

    'rules' => [
        'title'                 => 'Reglas Importantes',
        'no_overlap'            => 'Los rangos de edad no pueden solaparse entre categorías activas.',
        'no_upper_limit_hint'   => 'Deja “Edad hasta” vacío para indicar “sin límite superior”.',
        'slug_unique'           => 'El slug debe ser único.',
        'order_affects_display' => 'El orden determina cómo se muestran en el sistema.',
    ],

    'help' => [
        'title'           => 'Ayuda',
        'examples_title'  => 'Ejemplos de Categorías',
        'infant'          => 'Infante',
        'child'           => 'Niño',
        'adult'           => 'Adulto',
        'senior'          => 'Adulto mayor',
        'age_from_tip'    => 'Edad desde:',
        'age_to_tip'      => 'Edad hasta:',
        'range_tip'       => 'Rango:',
        'notes_title'     => 'Notas',
        'notes' => [
            'use_null_age_to' => 'Usa age_to = NULL para indicar "sin límite superior" (ej: 18+ años).',
            'inactive_hidden' => 'Las categorías inactivas no se muestran en formularios de reserva.',
        ],
    ],

    'info' => [
        'id'        => 'ID:',
        'created'   => 'Creado:',
        'updated'   => 'Actualizado:',
        'date_fmt'  => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => 'La edad hasta debe ser mayor o igual que la edad desde.',
    ],
];
