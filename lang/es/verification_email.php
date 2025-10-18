<?php

return [
    'title' => 'Verificación de Email - HandGeev',
    
    'header' => [
        'title' => 'Verifique su Email',
        'sent_to' => 'Enviamos un código de verificación a:',
    ],
    
    'form' => [
        'code_label' => 'Ingrese el código de 6 dígitos:',
        'code_placeholder' => '000000',
        'submit_button' => 'Verificar Código',
        'resend_code' => 'Reenviar código',
        'change_email' => 'Cambiar email',
        'logout' => 'Salir',
    ],
    
    'modal' => [
        'title' => 'Cambiar Email',
        'email_label' => 'Nuevo email:',
        'cancel_button' => 'Cancelar',
        'change_button' => 'Cambiar',
    ],
    
    'messages' => [
        'code_expires' => 'El código expira en 30 minutos',
        'success' => '¡Código verificado con éxito!',
        'error' => 'Código inválido o expirado.',
        'email_updated' => '¡Email actualizado con éxito!',
        'code_resent' => '¡Código reenviado con éxito!',
    ],
    
    'validation' => [
        'code_required' => 'El código es obligatorio',
        'code_digits' => 'El código debe tener 6 dígitos',
        'code_numeric' => 'El código debe contener solo números',
        'email_required' => 'El email es obligatorio',
        'email_email' => 'Por favor ingrese un email válido',
        'email_unique' => 'Este email ya está en uso',
    ],
    
    'icons' => [
        'email' => '📧',
        'check' => 'fas fa-check-circle',
        'redo' => 'fas fa-redo',
        'edit' => 'fas fa-edit',
        'logout' => 'fas fa-sign-out-alt',
    ],
];