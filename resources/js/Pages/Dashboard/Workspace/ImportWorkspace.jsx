import useLang from '@/Hooks/useLang';
import React, { useState, useRef } from 'react';
import { Head, usePage, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const ImportWorkspace = ({ auth }) => {
    const { __ } = useLang();
    const { props } = usePage();
    const { flash } = props;

    const [selectedFile, setSelectedFile] = useState(null);
    const [isImporting, setIsImporting] = useState(false);
    const fileInputRef = useRef(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        workspace_title: '',
        workspace_file: null,
    });

    // Handle file selection
    const handleFileSelect = (e) => {
        const file = e.target.files[0];
        
        if (!file) return;

        // Validate file type
        if (file.type !== 'application/json') {
            alert(__('alerts.invalid_file'));
            resetFile();
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert(__('alerts.file_too_large'));
            resetFile();
            return;
        }

        setSelectedFile(file);
        setData('workspace_file', file);
    };

    // Remove selected file
    const handleRemoveFile = () => {
        resetFile();
    };

    const resetFile = () => {
        setSelectedFile(null);
        setData('workspace_file', null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    // Handle drag and drop
    const handleDragOver = (e) => {
        e.preventDefault();
        e.stopPropagation();
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            const event = {
                target: {
                    files: [file]
                }
            };
            handleFileSelect(event);
        }
    };

    // Handle form submission
    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (!selectedFile) {
            alert(__('alerts.invalid_file'));
            return;
        }

        setIsImporting(true);
        post(route('workspace.import'), {
            onFinish: () => {
                setIsImporting(false);
            },
        });
    };

    // Format file size
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    return (
        <DashboardLayout>
            <Head 
                title={__('title')} 
                description={__('description')} 
            />

            <div className="min-h-screen">
                <div className="max-w-4xl mx-auto p-0 sm:p-0 md:p-6 py-2">
                    {/* Breadcrumb */}
                    <Link 
                        href={route('workspaces.show')} 
                        className="block w-max text-sm text-gray-300 hover:text-teal-400 transition-colors mb-8"
                    >
                        <i className={`fas ${__('breadcrumb.icon')} mr-1`}></i> 
                        {__('breadcrumb.back')}
                    </Link>

                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            {__('header.title')}
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {__('header.subtitle')}
                        </p>
                    </div>

                    {/* Alertas */}
                    {flash.error && (
                        <div className="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
                            <i className="fas fa-exclamation-circle mr-2"></i>
                            {flash.error}
                        </div>
                    )}

                    {flash.success && (
                        <div className="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400">
                            <i className="fas fa-check-circle mr-2"></i>
                            {flash.success}
                        </div>
                    )}

                    {/* Card de Importação */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        {/* Informações sobre o formato */}
                        <div className="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <h3 className="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                                <i className={`fas ${__('format_info.icon')} mr-2`}></i>
                                {__('format_info.title')}
                            </h3>
                            <p className="text-xs text-blue-600 dark:text-blue-400">
                                {__('format_info.description')}
                            </p>
                        </div>

                        <form onSubmit={handleSubmit} encType="multipart/form-data" id="import-form" autoComplete="off">
                            {/* Nome do Workspace */}
                            <div className="mb-6">
                                <label htmlFor="workspace_title" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {__('forms.workspace_title.label')}
                                </label>
                                <input 
                                    type="text" 
                                    name="workspace_title" 
                                    id="workspace_title"
                                    value={data.workspace_title}
                                    onChange={(e) => setData('workspace_title', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder={__('forms.workspace_title.placeholder')}
                                    required
                                />
                                {errors.workspace_title && (
                                    <p className="mt-1 text-sm text-red-600">{errors.workspace_title}</p>
                                )}
                            </div>

                            {/* Upload do Arquivo */}
                            <div className="mb-6">
                                <label htmlFor="workspace_file" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {__('forms.file_upload.label')}
                                </label>
                                <div className="flex items-center justify-center w-full">
                                    <label 
                                        htmlFor="workspace_file" 
                                        className="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                        onDragOver={handleDragOver}
                                        onDrop={handleDrop}
                                    >
                                        <div className="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i className="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                            <p className="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                <span className="font-semibold">
                                                    {__('forms.file_upload.drag_drop')}
                                                </span>
                                            </p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {__('forms.file_upload.file_info')}
                                            </p>
                                        </div>
                                        <input 
                                            id="workspace_file" 
                                            ref={fileInputRef}
                                            name="workspace_file" 
                                            type="file" 
                                            className="hidden" 
                                            accept=".json"
                                            onChange={handleFileSelect}
                                            required 
                                        />
                                    </label>
                                </div>
                                {errors.workspace_file && (
                                    <p className="mt-1 text-sm text-red-600">{errors.workspace_file}</p>
                                )}
                                
                                {/* Preview do arquivo */}
                                {selectedFile && (
                                    <div id="file-preview" className="mt-3">
                                        <div className="flex items-center justify-between p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                                            <div className="flex items-center">
                                                <i className="fas fa-file-code text-teal-600 dark:text-teal-400 mr-3"></i>
                                                <div>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                                        {__('forms.file_upload.file_selected')}
                                                    </p>
                                                    <p id="file-name" className="text-sm font-medium text-gray-900 dark:text-white">
                                                        {selectedFile.name}
                                                    </p>
                                                    <p id="file-size" className="text-xs text-gray-500 dark:text-gray-400">
                                                        {formatFileSize(selectedFile.size)}
                                                    </p>
                                                </div>
                                            </div>
                                            <button 
                                                type="button" 
                                                id="remove-file" 
                                                className="text-red-500 hover:text-red-700" 
                                                title={__('forms.buttons.remove_file')}
                                                onClick={handleRemoveFile}
                                            >
                                                <i className="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Estrutura Esperada */}
                            <div className="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
                                <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    {__('forms.expected_structure.title')}
                                </h3>
                                <pre className="text-xs bg-slate-800 text-slate-200 p-3 rounded overflow-x-auto">
                                    <code>{`{
    "workspace": {
        "title": "Name Workspace",
        "type_workspace_id": 1,
        "topics": [
            {
                "id": 27,
                "title": "Name Topic",
                "order": 1,
                "fields": [
                    {
                        "is_visible": 1,
                        "key": "value"
                    }
                ]
            }
        ]
    }
}`}</code>
                                </pre>
                            </div>

                            {/* Botões de Ação */}
                            <div className="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                                <Link 
                                    href={route('workspaces.show')}
                                    className="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    {__('forms.buttons.cancel')}
                                </Link>
                                <button 
                                    type="submit" 
                                    id="import-btn"
                                    disabled={processing || isImporting || !selectedFile}
                                    className="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors teal-glow-hover disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {processing || isImporting ? (
                                        <>
                                            <i className={`fas ${__('processing.icon')} mr-2`}></i>
                                            {__('forms.buttons.importing')}
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-upload mr-2"></i>
                                            {__('forms.buttons.import')}
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Dicas */}
                    <div className="mt-6 grid grid-cols-1 gap-4">
                        <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div className="flex items-start">
                                <i className={`fas ${__('tips.export.icon')} text-blue-600 dark:text-blue-400 mt-1 mr-3`}></i>
                                <div>
                                    <h4 className="text-sm font-medium text-blue-900 dark:text-blue-100">
                                        {__('tips.export.title')}
                                    </h4>
                                    <p className="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                        {__('tips.export.description')}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .teal-glow-hover:hover {
                    box-shadow: 0 0 20px rgba(45, 212, 191, 0.3);
                }
            `}</style>
        </DashboardLayout>
    );
};

export default ImportWorkspace;