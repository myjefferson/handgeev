<?php

return [
    'title' => 'Workspace',
    'description' => 'Workspace do HandGeev - :title',
    
    'navigation' => [
        'back_to_workspaces' => 'Voltar para Meus Workspaces',
    ],
    
    'sidebar' => [
        'topics_count' => 'tópicos',
        'new_topic' => 'Novo Tópico',
        'fields_count' => ':count',
        'import_topic' => 'Importar Tópico',
        'export_topic' => 'Exportar Tópico',
        'download_topic' => 'Download do Tópico',
        'import_success' => 'Tópico importado com sucesso!',
        'export_success' => 'Tópico exportado com sucesso!',
        'import_error' => 'Erro ao importar tópico',
        'export_error' => 'Erro ao exportar tópico',
    ],

    'import_export' => [
        'actions' => 'Ações',
        'quick_export' => 'Exportação Rápida',
    ],
    
    'limits' => [
        'upgrade_required' => [
            'title' => 'Limite de campos atingido',
            'message' => 'Limite de campos atingido (:current/:limit). :upgrade_link para adicionar mais campos.',
            'upgrade_link' => 'Faça upgrade',
        ],
        'fields_usage' => [
            'title' => 'Campos utilizados',
            'message' => 'Campos utilizados: :current/:limit (:remaining restantes)',
        ],
    ],
    
    'table' => [
        'headers' => [
            'visibility' => 'Visibilidade',
            'key' => 'Chave',
            'value' => 'Valor (opcional)',
            'type' => 'Tipo',
            'actions' => 'Ações',
        ],
        'empty' => [
            'icon' => 'fas fa-inbox',
            'message' => 'Nenhum campo cadastrado neste tópico',
        ],
        'add_field' => [
            'trigger' => 'Clique para adicionar novo campo',
            'limit_reached' => 'Limite de campos atingido. :upgrade_link',
        ],
    ],
    
    'fields' => [
        'placeholders' => [
            'key' => 'Nome da chave',
            'text_value' => 'Digite o valor',
            'number_value' => 'Digite um número',
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
            'true' => 'Verdadeiro',
            'false' => 'Falso',
        ],
        'upgrade_message' => [
            'icon' => 'fas fa-crown',
            'text' => ':upgrade_link para acessar mais tipos',
            'link' => 'Faça upgrade',
        ],
    ],
    
    'actions' => [
        'save' => 'Salvar',
        'remove' => 'Remover',
        'delete' => 'Excluir',
    ],
    
    'modals' => [
        'delete_topic' => [
            'title' => 'Excluir tópico',
            'message' => 'Tem certeza que deseja excluir o tópico ":title"? Todos os campos serão removidos.',
            'confirm' => 'Excluir',
            'cancel' => 'Cancelar',
        ],
        'new_topic' => [
            'title' => 'Novo tópico',
            'prompt' => 'Digite o nome do novo tópico:',
            'placeholder' => 'Nome do tópico',
        ],
    ],
    
    'notifications' => [
        'saving' => 'Salvando...',
        'saved' => 'Salvo!',
        'deleting' => 'Excluindo...',
    ],
];