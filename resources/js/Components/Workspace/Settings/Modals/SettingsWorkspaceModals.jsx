// resources/js/Components/Workspace/Settings/Modals.jsx
import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import DeleteModal from './DeleteModal';
import DuplicateModal from './DuplicateModal';

export default function SettingsWorkspaceModals({ modals, onClose, workspace }) {
    return (
        <>
            <DeleteModal 
                isOpen={modals.delete}
                onClose={() => onClose('delete')}
                workspace={workspace}
            />

            <DuplicateModal 
                isOpen={modals.duplicate}
                onClose={() => onClose('duplicate')}
                workspace={workspace}
            />
        </>
    );
}