<?php

return [
    'title' => 'Importar Workspace',
    'description' => 'Importar Workspace',
    
    'breadcrumb' => [
        'back' => 'Voltar',
        'icon' => 'fa-arrow-left',
    ],
    
    'header' => [
        'title' => 'Importar Workspace',
        'subtitle' => 'Importe um workspace a partir de um arquivo JSON',
    ],
    
    'format_info' => [
        'title' => 'Formato do Arquivo',
        'icon' => 'fa-info-circle',
        'description' => 'O arquivo deve estar no formato JSON exportado do HandGeev. Estrutura esperada: workspace → topics → fields com key_name como chave principal.',
    ],
    
    'forms' => [
        'workspace_title' => [
            'label' => 'Nome do Workspace *',
            'placeholder' => 'Digite um nome para o workspace',
        ],
        'file_upload' => [
            'label' => 'Arquivo JSON *',
            'drag_drop' => 'Clique para fazer upload do arquivo',
            'file_info' => 'JSON (MAX. 10MB)',
            'file_selected' => 'Arquivo selecionado:',
        ],
        'expected_structure' => [
            'title' => 'Estrutura do Arquivo JSON Esperada:',
        ],
        'buttons' => [
            'cancel' => 'Cancelar',
            'import' => 'Importar Workspace',
            'importing' => 'Importando...',
            'remove_file' => 'Remover arquivo',
        ],
    ],
    
    'alerts' => [
        'invalid_file' => 'Por favor, selecione um arquivo JSON.',
        'file_too_large' => 'O arquivo é muito grande. O tamanho máximo é 10MB.',
    ],
    
    'tips' => [
        'export' => [
            'title' => 'Dica de Exportação',
            'icon' => 'fa-lightbulb',
            'description' => 'Você pode exportar um workspace existente para ver a estrutura do arquivo JSON.',
        ],
    ],
    
    'processing' => [
        'icon' => 'fa-spinner fa-spin',
    ],
];