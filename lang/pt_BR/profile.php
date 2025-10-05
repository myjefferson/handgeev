<?php

return [
    'title' => 'Meu Perfil',
    'description' => 'Meu Perfil',
    
    'header' => [
        'title' => 'Meu Perfil',
        'subtitle' => 'Gerencie suas informações pessoais e preferências',
    ],
    
    'alerts' => [
        'success' => [
            'icon' => 'fa-check-circle',
            'default' => 'Operação realizada com sucesso!',
        ],
        'error' => [
            'icon' => 'fa-exclamation-circle',
            'default' => 'Ocorreu um erro!',
        ],
    ],
    
    'sidebar' => [
        'avatar' => [
            'alt' => 'Avatar do usuário',
        ],
        'badges' => [
            'admin' => 'Administrador',
            'premium' => 'Plano Premium',
            'pro' => 'Plano Pro',
            'start' => 'Plano Start',
            'free' => 'Plano Free',
        ],
        'upgrade' => [
            'button' => 'Upgrade para Pro',
            'icon' => 'fa-rocket',
        ],
        'stats' => [
            'title' => 'Estatísticas',
            'workspaces' => 'Workspaces',
            'topics' => 'Tópicos',
            'fields' => 'Campos',
        ],
    ],
    
    'tabs' => [
        'personal_info' => [
            'label' => 'Informações Pessoais',
            'icon' => 'fa-user-edit',
            'title' => 'Editar Informações Pessoais',
        ],
        'password' => [
            'label' => 'Alterar Senha',
            'icon' => 'fa-lock',
            'title' => 'Alterar Senha',
        ],
    ],
    
    'forms' => [
        'personal_info' => [
            'name' => [
                'label' => 'Nome *',
                'placeholder' => 'Seu nome',
            ],
            'surname' => [
                'label' => 'Sobrenome *',
                'placeholder' => 'Seu sobrenome',
            ],
            'email' => [
                'label' => 'Email *',
                'placeholder' => 'seu@email.com',
            ],
            'phone' => [
                'label' => 'Telefone',
                'placeholder' => '(00) 00000-0000',
            ],
            'buttons' => [
                'save' => 'Salvar Alterações',
                'cancel' => 'Cancelar',
                'icons' => [
                    'save' => 'fa-save',
                    'cancel' => 'fa-undo',
                ],
            ],
        ],
        'password' => [
            'current_password' => [
                'label' => 'Senha Atual *',
                'placeholder' => 'Digite sua senha atual',
            ],
            'new_password' => [
                'label' => 'Nova Senha *',
                'placeholder' => 'Digite a nova senha',
            ],
            'confirm_password' => [
                'label' => 'Confirmar Nova Senha *',
                'placeholder' => 'Confirme a nova senha',
            ],
            'tips' => [
                'title' => 'Dicas para uma senha segura:',
                'icon' => 'fa-lightbulb',
                'items' => [
                    'Use pelo menos 8 caracteres',
                    'Combine letras maiúsculas e minúsculas',
                    'Inclua números e caracteres especiais',
                    'Evite informações pessoais',
                ],
            ],
            'buttons' => [
                'update' => 'Atualizar Senha',
                'back' => 'Voltar para Perfil',
                'icons' => [
                    'update' => 'fa-key',
                    'back' => 'fa-arrow-left',
                ],
            ],
        ],
    ],
    
    'processing' => [
        'text' => 'Processando...',
        'icon' => 'fa-spinner fa-spin',
    ],
];