// resources/js/Components/Workspace/ImportExportDropdown.jsx
import React, { useRef, useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import useLang from '@/Hooks/useLang';

export default function ImportExportDropdown({ onImport, onExport }) {
    const { __ } = useLang()
    const { auth } = usePage().props;
    const [isOpen, setIsOpen] = React.useState(false);
    const dropdownRef = useRef(null);

    useEffect(() => {
        function handleClickOutside(event) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        }

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <div className="relative" ref={dropdownRef}>
            <button
                id="dropdownImportExportButton"
                onClick={() => setIsOpen(!isOpen)}
                className="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors duration-300 flex items-center justify-center border border-slate-600 hover:border-slate-500"
                type="button"
            >
                <i className="fas fa-exchange-alt mr-2"></i>
                <span className="hidden sm:inline">{__('import_export.actions')}</span>
                <svg className="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>

            {isOpen && (
                <div 
                    id="dropdownImportExport"
                    className="z-10 absolute top-12 left-0 bg-slate-800 divide-y divide-slate-700 rounded-lg shadow-lg border border-slate-700 w-44 backdrop-blur-sm bg-opacity-95"
                >
                    <ul className="py-2 text-sm text-slate-200">
                        {!auth.user.is_free && (
                            <li>
                                <button
                                    onClick={() => {
                                        onImport();
                                        setIsOpen(false);
                                    }}
                                    className="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center"
                                >
                                    <i className="fas fa-download w-5 h-5 mr-2 text-blue-400"></i>
                                    {__('import_export.import')}
                                </button>
                            </li>
                        )}
                        <li>
                            <button
                                onClick={() => {
                                    onExport();
                                    setIsOpen(false);
                                }}
                                className="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center"
                            >
                                <i className="fas fa-upload w-5 h-5 mr-2 text-green-400"></i>
                                {__('import_export.export')}
                            </button>
                        </li>
                    </ul>
                </div>
            )}
        </div>
    );
}