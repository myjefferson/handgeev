<?php

return [
    'title' => 'Recuperar Conta - HandGeev',
    
    'header' => [
        'title' => 'Recuperar Conta',
        'subtitle' => 'Informe seu e-mail para recuperar sua conta',
    ],
    
    'form' => [
        'email' => 'E-mail',
        'email_placeholder' => 'seu@email.com',
        'submit_button' => 'Enviar Link de Recuperação',
        'back_to_login' => 'Voltar para o login',
    ],
    
    'messages' => [
        'success' => 'Link de recuperação enviado com sucesso!',
        'error' => 'Erro ao enviar link de recuperação.',
        'email_sent' => 'Enviamos um link de recuperação para seu e-mail.',
        'invalid_email' => 'E-mail não encontrado em nossa base.',
    ],
    
    'validation' => [
        'email_required' => 'O campo e-mail é obrigatório',
        'email_email' => 'Digite um e-mail válido',
        'email_exists' => 'Este e-mail não está cadastrado em nossa base',
    ],
    
    'icons' => [
        'email' => 'fas fa-envelope',
        'paper_plane' => 'fas fa-paper-plane',
        'arrow_left' => 'fas fa-arrow-left',
    ],
];