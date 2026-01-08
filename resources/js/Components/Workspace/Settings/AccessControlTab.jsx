// resources/js/Components/Workspace/Settings/AccessControlTab.jsx
import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import PasswordProtectionCard from './PasswordProtectionCard';
import EditRequestsCard from './EditRequestsCard';

export default function AccessControlTab({ workspace, hasPasswordWorkspace, auth }) {
    if (auth.user.is_free) {
        return (
            <div className="grid grid-cols-1 gap-8">
                <PasswordProtectionCard upgrade />
                {/* <CollaboratorsCard upgrade /> */}
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 gap-8">
            <PasswordProtectionCard 
                workspace={workspace}
                hasPasswordWorkspace={hasPasswordWorkspace}
            />
            
            {/* <CollaboratorsCard workspace={workspace} /> */}
            
            {(auth.user.is_pro || auth.user.is_admin) && (
                <EditRequestsCard workspace={workspace} />
            )}
        </div>
    );
}