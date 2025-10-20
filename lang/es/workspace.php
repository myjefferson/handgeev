<?php

return [
    'title' => 'Workspace',
    'description' => 'Workspace de HandGeev - :title',
    
    'navigation' => [
        'back_to_workspaces' => 'Volver a Mis Workspaces',
    ],
    
    'sidebar' => [
        'topics_count' => ':count tema|:count temas',
        'new_topic' => 'Nuevo Tema',
        'fields_count' => ':count campo|:count campos',
        'import_topic' => 'Importar Tema',
        'export_topic' => 'Exportar Tema',
        'download_topic' => 'Descargar Tema',
        'import_success' => '¡Tema importado exitosamente!',
        'export_success' => '¡Tema exportado exitosamente!',
        'import_error' => 'Error al importar el tema',
        'export_error' => 'Error al exportar el tema',
    ],

    'import_export' => [
        'import' => 'Importar',
        'export' => 'Exportar',
        'actions' => 'Acciones',
        'quick_export' => 'Exportación Rápida',
        'import_topic' => 'Importar Tema',
        'export_topic' => 'Exportar Tema',
        'import_file' => 'Importar desde Archivo',
        'import_existing' => 'Importar Tema Existente',
        'export_json' => 'Exportar como JSON',
        'export_download' => 'Descargar como Archivo',
        'topic_name' => 'Nombre del Tema',
        'json_file' => 'Archivo JSON',
        'select_topic' => 'Seleccionar Tema',
        'new_topic_name' => 'Nuevo Nombre del Tema',
        'file_requirements' => 'Seleccione un archivo JSON exportado de HandGeev (máx. 10MB)',
        'loading_topics' => 'Cargando temas...',
        'select_topic_placeholder' => 'Seleccione un tema',
        'error_loading_topics' => 'Error al cargar temas',
        'importing' => 'Importando...',
        'cancel' => 'Cancelar',
        'choose_export_method' => 'Elija cómo desea exportar el tema:',
        'json_structure' => 'Estructura de datos',
        'json_file_download' => 'Archivo JSON',
        'download_started' => '¡Descarga iniciada!',
        'plan_restriction' => 'La funcionalidad de importación está disponible solo para planes Start, Pro, Premium y Admin',
        'file_required' => 'Por favor seleccione un archivo e ingrese un nombre para el tema',
        'topic_required' => 'Por favor seleccione un tema e ingrese un nombre',
        'invalid_file' => 'Archivo inválido: estructura de campos no encontrada',
        'field_required' => 'Campo :index: :field es obligatorio',
        'type_not_supported' => 'Campo :index: tipo ":type" no es soportado',
        'plan_limit_exceeded' => 'Límite de campos excedido. Solo puede agregar :remaining campos adicionales.',
    ],
    
    'notifications' => [
        'import_success' => '¡Tema importado exitosamente!',
        'export_success' => '¡Tema exportado exitosamente!',
        'download_success' => '¡Descarga iniciada exitosamente!',
        'import_error' => 'Error al importar tema: :message',
        'export_error' => 'Error al exportar tema: :message',
        'permission_denied' => 'No tiene permiso para realizar esta acción',
        'file_invalid' => 'Formato de archivo inválido',
        'plan_restricted' => 'Esta característica no está disponible para su plan actual',
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