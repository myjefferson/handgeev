<?php

return [
    'title' => 'Importar Workspace',
    'description' => 'Importar Workspace',
    
    'breadcrumb' => [
        'back' => 'Volver',
        'icon' => 'fa-arrow-left',
    ],
    
    'header' => [
        'title' => 'Importar Workspace',
        'subtitle' => 'Importa un workspace desde un archivo JSON',
    ],
    
    'format_info' => [
        'title' => 'Formato del Archivo',
        'icon' => 'fa-info-circle',
        'description' => 'El archivo debe estar en formato JSON exportado de HandGeev. Estructura esperada: workspace → topics → fields con key_name como clave principal.',
    ],
    
    'forms' => [
        'workspace_title' => [
            'label' => 'Nombre del Workspace *',
            'placeholder' => 'Ingresa un nombre para el workspace',
        ],
        'file_upload' => [
            'label' => 'Archivo JSON *',
            'drag_drop' => 'Haz clic para subir o arrastra el archivo',
            'file_info' => 'JSON (MÁX. 10MB)',
            'file_selected' => 'Archivo seleccionado:',
        ],
        'expected_structure' => [
            'title' => 'Estructura Esperada del Archivo JSON:',
        ],
        'buttons' => [
            'cancel' => 'Cancelar',
            'import' => 'Importar Workspace',
            'importing' => 'Importando...',
            'remove_file' => 'Eliminar archivo',
        ],
    ],
    
    'alerts' => [
        'invalid_file' => 'Por favor, selecciona un archivo JSON.',
        'file_too_large' => 'El archivo es muy grande. El tamaño máximo es 10MB.',
    ],
    
    'tips' => [
        'export' => [
            'title' => 'Consejo de Exportación',
            'icon' => 'fa-lightbulb',
            'description' => 'Puedes exportar un workspace existente para ver la estructura del archivo JSON.',
        ],
    ],
    
    'processing' => [
        'icon' => 'fa-spinner fa-spin',
    ],
];