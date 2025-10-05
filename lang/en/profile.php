<?php

return [
    'title' => 'My Profile',
    'description' => 'My Profile',
    
    'header' => [
        'title' => 'My Profile',
        'subtitle' => 'Manage your personal information and preferences',
    ],
    
    'alerts' => [
        'success' => [
            'icon' => 'fa-check-circle',
            'default' => 'Operation completed successfully!',
        ],
        'error' => [
            'icon' => 'fa-exclamation-circle',
            'default' => 'An error occurred!',
        ],
    ],
    
    'sidebar' => [
        'avatar' => [
            'alt' => 'User avatar',
        ],
        'badges' => [
            'admin' => 'Administrador',
            'premium' => 'Premium Plan',
            'pro' => 'Pro Plan',
            'start' => 'Start Plan',
            'free' => 'Free Plan',
        ],
        'upgrade' => [
            'button' => 'Upgrade to Pro',
            'icon' => 'fa-rocket',
        ],
        'stats' => [
            'title' => 'Statistics',
            'workspaces' => 'Workspaces',
            'topics' => 'Topics',
            'fields' => 'Fields',
        ],
    ],
    
    'tabs' => [
        'personal_info' => [
            'label' => 'Personal Information',
            'icon' => 'fa-user-edit',
            'title' => 'Edit Personal Information',
        ],
        'password' => [
            'label' => 'Change Password',
            'icon' => 'fa-lock',
            'title' => 'Change Password',
        ],
    ],
    
    'forms' => [
        'personal_info' => [
            'name' => [
                'label' => 'First Name *',
                'placeholder' => 'Your first name',
            ],
            'surname' => [
                'label' => 'Last Name *',
                'placeholder' => 'Your last name',
            ],
            'email' => [
                'label' => 'Email *',
                'placeholder' => 'your@email.com',
            ],
            'phone' => [
                'label' => 'Phone',
                'placeholder' => '(00) 00000-0000',
            ],
            'buttons' => [
                'save' => 'Save Changes',
                'cancel' => 'Cancel',
                'icons' => [
                    'save' => 'fa-save',
                    'cancel' => 'fa-undo',
                ],
            ],
        ],
        'password' => [
            'current_password' => [
                'label' => 'Current Password *',
                'placeholder' => 'Enter your current password',
            ],
            'new_password' => [
                'label' => 'New Password *',
                'placeholder' => 'Enter new password',
            ],
            'confirm_password' => [
                'label' => 'Confirm New Password *',
                'placeholder' => 'Confirm new password',
            ],
            'tips' => [
                'title' => 'Tips for a secure password:',
                'icon' => 'fa-lightbulb',
                'items' => [
                    'Use at least 8 characters',
                    'Combine uppercase and lowercase letters',
                    'Include numbers and special characters',
                    'Avoid personal information',
                ],
            ],
            'buttons' => [
                'update' => 'Update Password',
                'back' => 'Back to Profile',
                'icons' => [
                    'update' => 'fa-key',
                    'back' => 'fa-arrow-left',
                ],
            ],
        ],
    ],
    
    'processing' => [
        'text' => 'Processing...',
        'icon' => 'fa-spinner fa-spin',
    ],
];