<?php

return [
    'title' => 'Recuperar Cuenta - HandGeev',
    
    'header' => [
        'title' => 'Recuperar Cuenta',
        'subtitle' => 'Ingrese su correo para recuperar su cuenta',
    ],
    
    'form' => [
        'email' => 'Correo Electrónico',
        'email_placeholder' => 'tu@email.com',
        'submit_button' => 'Enviar Enlace de Recuperación',
        'back_to_login' => 'Volver al inicio de sesión',
    ],
    
    'messages' => [
        'success' => '¡Enlace de recuperación enviado con éxito!',
        'error' => 'Error al enviar el enlace de recuperación.',
        'email_sent' => 'Hemos enviado un enlace de recuperación a su correo.',
        'invalid_email' => 'Correo no encontrado en nuestra base de datos.',
    ],
    
    'validation' => [
        'email_required' => 'El campo correo electrónico es obligatorio',
        'email_email' => 'Por favor ingrese un correo válido',
        'email_exists' => 'Este correo no está registrado en nuestra base de datos',
    ],
    
    'icons' => [
        'email' => 'fas fa-envelope',
        'paper_plane' => 'fas fa-paper-plane',
        'arrow_left' => 'fas fa-arrow-left',
    ],
];