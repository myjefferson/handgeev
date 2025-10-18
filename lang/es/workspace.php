<?php

return [
    'title' => 'Workspace',
    'description' => 'Workspace de HandGeev - :title',
    
    'navigation' => [
        'back_to_workspaces' => 'Volver a Mis Workspaces',
    ],
    
    'sidebar' => [
        'topics_count' => ':count tópico|:count tópicos',
        'new_topic' => 'Nuevo Tópico',
        'fields_count' => ':count',
    ],
    
    'limits' => [
        'upgrade_required' => [
            'title' => 'Límite de campos alcanzado',
            'message' => 'Límite de campos alcanzado (:current/:limit). :upgrade_link para agregar más campos.',
            'upgrade_link' => 'Haz upgrade',
        ],
        'fields_usage' => [
            'title' => 'Campos utilizados',
            'message' => '📊 Campos utilizados: :current/:limit (:remaining restantes)',
        ],
    ],
    
    'table' => [
        'headers' => [
            'visibility' => 'Visibilidad',
            'key' => 'Clave',
            'value' => 'Valor (opcional)',
            'type' => 'Tipo',
            'actions' => 'Acciones',
        ],
        'empty' => [
            'icon' => 'fas fa-inbox',
            'message' => 'Ningún campo registrado en este tópico',
        ],
        'add_field' => [
            'trigger' => 'Haz clic para agregar nuevo campo',
            'limit_reached' => 'Límite de campos alcanzado. :upgrade_link',
        ],
    ],
    
    'fields' => [
        'placeholders' => [
            'key' => 'Nombre de la clave',
            'text_value' => 'Ingrese el valor',
            'number_value' => 'Ingrese un número',
        ],
        'types' => [
            'text' => 'Texto',
            'number' => 'Número',
            'boolean' => 'Booleano',
            'locked' => [
                'number' => '🔒 Número',
                'boolean' => '🔒 Booleano',
            ],
        ],
        'boolean_options' => [
            'true' => 'Verdadero',
            'false' => 'Falso',
        ],
        'upgrade_message' => [
            'icon' => 'fas fa-crown',
            'text' => ':upgrade_link para acceder a más tipos',
            'link' => 'Haz upgrade',
        ],
    ],
    
    'actions' => [
        'save' => 'Guardar',
        'remove' => 'Eliminar',
        'delete' => 'Borrar',
    ],
    
    'modals' => [
        'delete_topic' => [
            'title' => 'Eliminar tópico',
            'message' => '¿Estás seguro de que deseas eliminar el tópico ":title"? Todos los campos serán removidos.',
            'confirm' => 'Eliminar',
            'cancel' => 'Cancelar',
        ],
        'new_topic' => [
            'title' => 'Nuevo tópico',
            'prompt' => 'Ingrese el nombre del nuevo tópico:',
            'placeholder' => 'Nombre del tópico',
        ],
    ],
    
    'notifications' => [
        'saving' => 'Guardando...',
        'saved' => '¡Guardado!',
        'deleting' => 'Eliminando...',
    ],
];