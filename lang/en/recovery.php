<?php

return [
    'title' => 'Recover Account - HandGeev',
    
    'header' => [
        'title' => 'Recover Account',
        'subtitle' => 'Enter your email to recover your account',
    ],
    
    'form' => [
        'email' => 'Email',
        'email_placeholder' => 'your@email.com',
        'submit_button' => 'Send Recovery Link',
        'back_to_login' => 'Back to login',
    ],
    
    'messages' => [
        'success' => 'Recovery link sent successfully!',
        'error' => 'Error sending recovery link.',
        'email_sent' => 'We have sent a recovery link to your email.',
        'invalid_email' => 'Email not found in our database.',
    ],
    
    'validation' => [
        'email_required' => 'The email field is required',
        'email_email' => 'Please enter a valid email',
        'email_exists' => 'This email is not registered in our database',
    ],
    
    'icons' => [
        'email' => 'fas fa-envelope',
        'paper_plane' => 'fas fa-paper-plane',
        'arrow_left' => 'fas fa-arrow-left',
    ],
];