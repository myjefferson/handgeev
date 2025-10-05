<?php

return [
    'title' => 'Import Workspace',
    'description' => 'Import Workspace',
    
    'breadcrumb' => [
        'back' => 'Back',
        'icon' => 'fa-arrow-left',
    ],
    
    'header' => [
        'title' => 'Import Workspace',
        'subtitle' => 'Import a workspace from a JSON file',
    ],
    
    'format_info' => [
        'title' => 'File Format',
        'icon' => 'fa-info-circle',
        'description' => 'The file must be in JSON format exported from HandGeev. Expected structure: workspace → topics → fields with key_name as main key.',
    ],
    
    'forms' => [
        'workspace_title' => [
            'label' => 'Workspace Name *',
            'placeholder' => 'Enter a name for the workspace',
        ],
        'file_upload' => [
            'label' => 'JSON File *',
            'drag_drop' => 'Click to upload or drag and drop',
            'file_info' => 'JSON (MAX. 10MB)',
            'file_selected' => 'File selected:',
        ],
        'expected_structure' => [
            'title' => 'Expected JSON File Structure:',
        ],
        'buttons' => [
            'cancel' => 'Cancel',
            'import' => 'Import Workspace',
            'importing' => 'Importing...',
            'remove_file' => 'Remove file',
        ],
    ],
    
    'alerts' => [
        'invalid_file' => 'Please select a JSON file.',
        'file_too_large' => 'File is too large. Maximum size is 10MB.',
    ],
    
    'tips' => [
        'export' => [
            'title' => 'Export Tip',
            'icon' => 'fa-lightbulb',
            'description' => 'You can export an existing workspace to see the JSON file structure.',
        ],
    ],
    
    'processing' => [
        'icon' => 'fa-spinner fa-spin',
    ],
];