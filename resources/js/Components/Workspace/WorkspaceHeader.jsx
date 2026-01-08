// resources/js/Components/Workspace/WorkspaceHeader.jsx
import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import ExportDropdown from './ExportDropdown';

export default function WorkspaceHeader({ workspace, onOpenShare }) {
    const { auth } = usePage().props;
    const [exportDropdownOpen, setExportDropdownOpen] = useState(false);

    return (
        <div className="flex justify-between items-center">
            <div className="flex gap-3 items-center">      
                <div className="flex items-center space-x-2">
                    <ExportDropdown 
                        workspace={workspace}
                        isOpen={exportDropdownOpen}
                        onToggle={() => setExportDropdownOpen(!exportDropdownOpen)}
                        onClose={() => setExportDropdownOpen(false)}
                    />

                    {workspace.type_view_workspace_id === 1 && (
                        <button 
                            onClick={onOpenShare}
                            className="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                            </svg>
                            <span>Geev Studio</span>
                        </button>
                    )}
                    
                    {workspace.type_view_workspace_id === 2 && (
                        <Link 
                            href={route('workspace.api-rest.show', {
                                global_key_api: auth.user.global_key_api,
                                workspace_key_api: workspace.workspace_key_api
                            })} 
                            className="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                            </svg>
                            <span>Geev API</span>
                        </Link>
                    )}

                    <Link 
                        href={route('workspace.setting', { id: workspace.id })}
                        className="flex items-center px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <i className="fas fa-cog"></i>
                    </Link>
                </div>
            </div>
        </div>
    );
}