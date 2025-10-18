<?php

return [
    'title' => 'Email Verification - HandGeev',
    
    'header' => [
        'title' => 'Verify Your Email',
        'sent_to' => 'We sent a verification code to:',
    ],
    
    'form' => [
        'code_label' => 'Enter the 6-digit code:',
        'code_placeholder' => '000000',
        'submit_button' => 'Verify Code',
        'resend_code' => 'Resend code',
        'change_email' => 'Change email',
        'logout' => 'Logout',
    ],
    
    'modal' => [
        'title' => 'Change Email',
        'email_label' => 'New email:',
        'cancel_button' => 'Cancel',
        'change_button' => 'Change',
    ],
    
    'messages' => [
        'code_expires' => 'The code expires in 30 minutes',
        'success' => 'Code verified successfully!',
        'error' => 'Invalid or expired code.',
        'email_updated' => 'Email updated successfully!',
        'code_resent' => 'Code resent successfully!',
    ],
    
    'validation' => [
        'code_required' => 'The code is required',
        'code_digits' => 'The code must be 6 digits',
        'code_numeric' => 'The code must contain only numbers',
        'email_required' => 'Email is required',
        'email_email' => 'Please enter a valid email',
        'email_unique' => 'This email is already in use',
    ],
    
    'icons' => [
        'email' => '📧',
        'check' => 'fas fa-check-circle',
        'redo' => 'fas fa-redo',
        'edit' => 'fas fa-edit',
        'logout' => 'fas fa-sign-out-alt',
    ],
];