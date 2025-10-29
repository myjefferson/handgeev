<?php

return [
    'title' => 'Workspace',
    'description' => 'HandGeev Workspace - :title',
    
    'navigation' => [
        'back_to_workspaces' => 'Back to My Workspaces',
    ],
    
    'sidebar' => [
        'topics_count' => 'topics',
        'new_topic' => 'New Topic',
        'fields_count' => ':count field|:count',
        'import_topic' => 'Import Topic',
        'rename_topic' => 'Rename Topic',
        'export_topic' => 'Export Topic',
        'download_topic' => 'Download Topic',
        'import_success' => 'Topic imported successfully!',
        'export_success' => 'Topic exported successfully!',
        'import_error' => 'Error importing topic',
        'export_error' => 'Error exporting topic',
    ],

    'modals' => [
        'delete_topic' => [
            'message' => 'Are you sure you want to delete the topic ":title"? All fields will be permanently removed.'
        ],
        'new_topic' => [
            'prompt' => 'Enter the name for the new topic:',
            'placeholder' => 'My New Topic'
        ],
        'rename_topic' => [
            'title' => 'Rename Topic',
            'topic_title' => 'Topic Title',
            'characters' => 'characters',
            'cancel' => 'Cancel',
            'save' => 'Save',
            'close' => 'Close modal'
        ]
    ],

    'import_export' => [
        'import' => 'Import',
        'export' => 'Export',
        'actions' => 'Actions',
        'quick_export' => 'Quick Export',
        'import_topic' => 'Import Topic',
        'export_topic' => 'Export Topic',
        'import_file' => 'Import from File',
        'import_existing' => 'Import Existing Topic',
        'export_json' => 'Export as JSON',
        'export_download' => 'Download as File',
        'topic_name' => 'Topic Name',
        'json_file' => 'JSON File',
        'select_topic' => 'Select Topic',
        'new_topic_name' => 'New Topic Name',
        'file_requirements' => 'Select a JSON file exported from HandGeev (max. 10MB)',
        'loading_topics' => 'Loading topics...',
        'select_topic_placeholder' => 'Select a topic',
        'error_loading_topics' => 'Error loading topics',
        'importing' => 'Importing...',
        'cancel' => 'Cancel',
        'choose_export_method' => 'Choose how you want to export the topic:',
        'json_structure' => 'Data structure',
        'json_file_download' => 'JSON file',
        'download_started' => 'Download started!',
        'plan_restriction' => 'Import functionality is only available for Start, Pro, Premium and Admin plans',
        'file_required' => 'Please select a file and enter a topic name',
        'topic_required' => 'Please select a topic and enter a name',
        'invalid_file' => 'Invalid file: field structure not found',
        'field_required' => 'Field :index: :field is required',
        'type_not_supported' => 'Field :index: type ":type" is not supported',
        'plan_limit_exceeded' => 'Field limit exceeded. You can only add :remaining additional fields.',
    ],

    'notifications' => [
        'import_success' => 'Topic imported successfully!',
        'export_success' => 'Topic exported successfully!',
        'download_success' => 'Download started successfully!',
        'import_error' => 'Error importing topic: :message',
        'export_error' => 'Error exporting topic: :message',
        'permission_denied' => 'You do not have permission to perform this action',
        'file_invalid' => 'Invalid file format',
        'plan_restricted' => 'This feature is not available for your current plan',
    ],

    'limits' => [
        'upgrade_required' => [
            'title' => 'Field limit reached',
            'message' => 'Field limit reached (:current/:limit). :upgrade_link to add more fields.',
            'upgrade_link' => 'Upgrade now',
        ],
        'fields_usage' => [
            'title' => 'Fields usage',
            'message' => '📊 Fields used: :current/:limit (:remaining remaining)',
        ],
    ],
    
    'table' => [
        'headers' => [
            'visibility' => 'Visibility',
            'key' => 'Key',
            'value' => 'Value (optional)',
            'type' => 'Type',
            'actions' => 'Actions',
        ],
        'empty' => [
            'icon' => 'fas fa-inbox',
            'message' => 'No fields registered in this topic',
        ],
        'add_field' => [
            'trigger' => 'Click to add new field',
            'limit_reached' => 'Field limit reached. :upgrade_link',
        ],
    ],
    
    'fields' => [
        'placeholders' => [
            'key' => 'Key name',
            'text_value' => 'Enter value',
            'number_value' => 'Enter a number',
        ],
        'types' => [
            'text' => 'Text',
            'number' => 'Number',
            'boolean' => 'Boolean',
            'locked' => [
                'number' => '🔒 Number',
                'boolean' => '🔒 Boolean',
            ],
        ],
        'boolean_options' => [
            'true' => 'True',
            'false' => 'False',
        ],
        'upgrade_message' => [
            'icon' => 'fas fa-crown',
            'text' => ':upgrade_link to access more types',
            'link' => 'Upgrade now',
        ],
    ],
    
    'actions' => [
        'save' => 'Save',
        'remove' => 'Remove',
        'delete' => 'Delete',
    ],
    
    'modals' => [
        'delete_topic' => [
            'title' => 'Delete topic',
            'message' => 'Are you sure you want to delete the topic ":title"? All fields will be removed.',
            'confirm' => 'Delete',
            'cancel' => 'Cancel',
        ],
        'new_topic' => [
            'title' => 'New topic',
            'prompt' => 'Enter the name of the new topic:',
            'placeholder' => 'Topic name',
        ],
    ],
    
    'notifications' => [
        'saving' => 'Saving...',
        'saved' => 'Saved!',
        'deleting' => 'Deleting...',
    ],
];