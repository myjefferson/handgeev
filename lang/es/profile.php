<?php

return [
    'title' => 'Mi Perfil',
    'description' => 'Mi Perfil',
    
    'header' => [
        'title' => 'Mi Perfil',
        'subtitle' => 'Gestiona tu información personal y preferencias',
    ],
    
    'alerts' => [
        'success' => [
            'icon' => 'fa-check-circle',
            'default' => '¡Operación completada con éxito!',
        ],
        'error' => [
            'icon' => 'fa-exclamation-circle',
            'default' => '¡Ocurrió un error!',
        ],
    ],
    
    'sidebar' => [
        'avatar' => [
            'alt' => 'Avatar del usuario',
        ],
        'badges' => [
            'admin' => 'Administrador',
            'premium' => 'Plan Premium',
            'pro' => 'Plan Pro',
            'start' => 'Plan Start',
            'free' => 'Plan Free',
        ],
        'upgrade' => [
            'button' => 'Mejorar a Pro',
            'icon' => 'fa-rocket',
        ],
        'stats' => [
            'title' => 'Estadísticas',
            'workspaces' => 'Workspaces',
            'topics' => 'Tópicos',
            'fields' => 'Campos',
        ],
    ],
    
    'tabs' => [
        'personal_info' => [
            'label' => 'Información Personal',
            'icon' => 'fa-user-edit',
            'title' => 'Editar Información Personal',
        ],
        'password' => [
            'label' => 'Cambiar Contraseña',
            'icon' => 'fa-lock',
            'title' => 'Cambiar Contraseña',
        ],
    ],
    
    'forms' => [
        'personal_info' => [
            'name' => [
                'label' => 'Nombre *',
                'placeholder' => 'Tu nombre',
            ],
            'surname' => [
                'label' => 'Apellido *',
                'placeholder' => 'Tu apellido',
            ],
            'email' => [
                'label' => 'Email *',
                'placeholder' => 'tu@email.com',
            ],
            'phone' => [
                'label' => 'Teléfono',
                'placeholder' => '(00) 00000-0000',
            ],
            'buttons' => [
                'save' => 'Guardar Cambios',
                'cancel' => 'Cancelar',
                'icons' => [
                    'save' => 'fa-save',
                    'cancel' => 'fa-undo',
                ],
            ],
        ],
        'password' => [
            'current_password' => [
                'label' => 'Contraseña Actual *',
                'placeholder' => 'Ingresa tu contraseña actual',
            ],
            'new_password' => [
                'label' => 'Nueva Contraseña *',
                'placeholder' => 'Ingresa nueva contraseña',
            ],
            'confirm_password' => [
                'label' => 'Confirmar Nueva Contraseña *',
                'placeholder' => 'Confirma nueva contraseña',
            ],
            'tips' => [
                'title' => 'Consejos para una contraseña segura:',
                'icon' => 'fa-lightbulb',
                'items' => [
                    'Usa al menos 8 caracteres',
                    'Combina mayúsculas y minúsculas',
                    'Incluye números y caracteres especiales',
                    'Evita información personal',
                ],
            ],
            'buttons' => [
                'update' => 'Actualizar Contraseña',
                'back' => 'Volver al Perfil',
                'icons' => [
                    'update' => 'fa-key',
                    'back' => 'fa-arrow-left',
                ],
            ],
        ],
    ],
    
    'processing' => [
        'text' => 'Procesando...',
        'icon' => 'fa-spinner fa-spin',
    ],
];