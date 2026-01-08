import React from 'react';
import { Link } from '@inertiajs/react';

const EmptyState = ({ type, icon, title, description, showButton = true }) => {
    return (
        <div className="text-center py-12 bg-slate-800/50 rounded-xl border border-slate-700">
            <div className="text-slate-400 text-6xl mb-4">{icon}</div>
            <h3 className="text-lg font-semibold text-white mb-2">{title}</h3>
            <p className="text-slate-400 mb-6 max-w-md mx-auto">{description}</p>
            
            {showButton && (
                <Link 
                    href={route('workspace.create')}
                    className="inline-flex items-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg transition-colors"
                >
                    <i className="fas fa-plus mr-2"></i>
                    Criar Primeiro Workspace
                </Link>
            )}
        </div>
    );
};

export default EmptyState;