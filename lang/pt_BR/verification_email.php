<?php

return [
    'title' => 'Verificação de Email - HandGeev',
    
    'header' => [
        'title' => 'Verifique seu Email',
        'sent_to' => 'Enviamos um código de verificação para:',
    ],
    
    'form' => [
        'code_label' => 'Digite o código de 6 dígitos:',
        'code_placeholder' => '000000',
        'submit_button' => 'Verificar Código',
        'resend_code' => 'Reenviar código',
        'change_email' => 'Alterar email',
        'logout' => 'Sair',
    ],
    
    'modal' => [
        'title' => 'Alterar Email',
        'email_label' => 'Novo email:',
        'cancel_button' => 'Cancelar',
        'change_button' => 'Alterar',
    ],
    
    'messages' => [
        'code_expires' => 'O código expira em 30 minutos',
        'success' => 'Código verificado com sucesso!',
        'error' => 'Código inválido ou expirado.',
        'email_updated' => 'Email atualizado com sucesso!',
        'code_resent' => 'Código reenviado com sucesso!',
    ],
    
    'validation' => [
        'code_required' => 'O código é obrigatório',
        'code_digits' => 'O código deve ter 6 dígitos',
        'code_numeric' => 'O código deve conter apenas números',
        'email_required' => 'O email é obrigatório',
        'email_email' => 'Digite um email válido',
        'email_unique' => 'Este email já está em uso',
    ],
    
    'icons' => [
        'email' => '📧',
        'check' => 'fas fa-check-circle',
        'redo' => 'fas fa-redo',
        'edit' => 'fas fa-edit',
        'logout' => 'fas fa-sign-out-alt',
    ],
];