<?php

return [
    'title' => 'Workspace',
    'description' => 'HandGeev Workspace - :title',
    
    'navigation' => [
        'back_to_workspaces' => 'Back to My Workspaces',
    ],
    
    'sidebar' => [
        'topics_count' => ':count topic|:count topics',
        'new_topic' => 'New Topic',
        'fields_count' => ':count',
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