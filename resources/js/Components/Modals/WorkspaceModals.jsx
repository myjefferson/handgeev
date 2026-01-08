import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import ImportModal from '@/Components/Modals/ImportTopicModal';
import ExportModal from '@/Components/Modals/ExportModalWorkspace';
import RenameModal from '@/Components/Modals/RenameModal';
import ShareModal from '@/Components/Modals/ShareModal';

export default function Modals({ modals, onClose, workspace, selectedTopic, auth }) {
    return (
        <>
            <ImportModal
                isOpen={modals.import}
                onClose={() => onClose('import')}
                workspace={workspace}
            />

            <ExportModal 
                isOpen={modals.export}
                onClose={() => onClose('export')}
                topic={selectedTopic}
            />

            <RenameModal 
                isOpen={modals.rename}
                onClose={() => onClose('rename')}
                topic={selectedTopic}
            />

            <ShareModal 
                isOpen={modals.share}
                onClose={() => onClose('share')}
                workspace={workspace}
                auth={auth}
            />
        </>
    );
}