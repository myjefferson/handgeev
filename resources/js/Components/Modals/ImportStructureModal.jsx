import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function ImportStructureModal({ onClose, onSuccess }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        import_file: null,
        name: '',
        description: '',
        is_public: false
    });

    const [fileContent, setFileContent] = useState(null);
    const [preview, setPreview] = useState(null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setData('import_file', file);

        // Ler o arquivo para preview
        const reader = new FileReader();
        reader.onload = (event) => {
            try {
                const content = JSON.parse(event.target.result);
                setFileContent(content);
                setPreview({
                    name: content.name || `Importado: ${file.name.replace('.json', '')}`,
                    description: content.description || '',
                    fields_count: content.fields?.length || 0,
                    fields: content.fields || []
                });
                setData('name', content.name || '');
                setData('description', content.description || '');
            } catch (error) {
                alert('Arquivo inválido. Por favor, selecione um arquivo JSON válido.');
                e.target.value = '';
            }
        };
        reader.readAsText(file);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (!fileContent) {
            alert('Por favor, selecione um arquivo para importar.');
            return;
        }

        const formData = new FormData();
        formData.append('import_file', data.import_file);
        formData.append('name', data.name);
        formData.append('description', data.description);
        formData.append('is_public', data.is_public);

        post(route('structures.import'), {
            data: formData,
            onSuccess: () => {
                reset();
                onClose();
                if (onSuccess) onSuccess();
            },
        });
    };

    const fieldTypes = {
        text: 'Texto',
        number: 'Número',
        decimal: 'Decimal',
        boolean: 'Booleano',
        date: 'Data',
        datetime: 'Data e Hora',
        email: 'E-mail',
        url: 'URL',
        json: 'JSON'
    };

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-slate-800 rounded-2xl border border-slate-700 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div className="p-6">
                    {/* Header */}
                    <div className="flex items-center justify-between mb-6">
                        <h2 className="text-xl font-bold text-white">Importar Estrutura</h2>
                        <button
                            onClick={onClose}
                            className="p-2 text-gray-400 hover:text-white rounded-lg transition-colors"
                        >
                            <i className="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form onSubmit={handleSubmit}>
                        {/* Upload do Arquivo */}
                        <div className="mb-6">
                            <label className="block text-sm font-medium text-gray-300 mb-2">
                                Arquivo JSON da Estrutura *
                            </label>
                            <div className="border-2 border-dashed border-slate-600 rounded-xl p-8 text-center hover:border-teal-400 transition-colors">
                                <input
                                    type="file"
                                    accept=".json,application/json"
                                    onChange={handleFileChange}
                                    className="hidden"
                                    id="importFile"
                                    required
                                />
                                <label htmlFor="importFile" className="cursor-pointer">
                                    <div className="flex flex-col items-center">
                                        <i className="fas fa-file-import text-4xl text-teal-400 mb-4"></i>
                                        <p className="text-white font-medium mb-2">
                                            {data.import_file 
                                                ? data.import_file.name 
                                                : 'Clique para selecionar ou arraste o arquivo'
                                            }
                                        </p>
                                        <p className="text-gray-400 text-sm">
                                            Selecione um arquivo JSON exportado do HandGeev
                                        </p>
                                    </div>
                                </label>
                            </div>
                            {errors.import_file && (
                                <p className="text-red-400 text-sm mt-2">{errors.import_file}</p>
                            )}
                        </div>

                        {/* Preview da Estrutura */}
                        {preview && (
                            <div className="mb-6 bg-slate-700/30 rounded-xl p-4 border border-slate-600">
                                <h3 className="font-medium text-white mb-3">Pré-visualização</h3>
                                
                                <div className="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-300 mb-1">
                                            Nome
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-teal-400"
                                            required
                                        />
                                        {errors.name && <p className="text-red-400 text-sm mt-1">{errors.name}</p>}
                                    </div>
                                    
                                    <div>
                                        <label className="block text-sm font-medium text-gray-300 mb-1">
                                            Descrição
                                        </label>
                                        <input
                                            type="text"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-teal-400"
                                        />
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <input
                                        type="checkbox"
                                        id="import_is_public"
                                        checked={data.is_public}
                                        onChange={(e) => setData('is_public', e.target.checked)}
                                        className="mr-3 rounded bg-slate-600 border-slate-500 text-teal-400 focus:ring-teal-400"
                                    />
                                    <label htmlFor="import_is_public" className="text-sm text-gray-300">
                                        Tornar esta estrutura pública
                                    </label>
                                </div>

                                {/* Campos da estrutura importada */}
                                <div>
                                    <h4 className="text-sm font-medium text-gray-300 mb-2">
                                        Campos ({preview.fields_count})
                                    </h4>
                                    <div className="space-y-2">
                                        {preview.fields.slice(0, 3).map((field, index) => (
                                            <div key={index} className="flex items-center justify-between text-sm">
                                                <span className="text-gray-300">{field.name}</span>
                                                <span className="text-gray-400">{fieldTypes[field.type] || field.type}</span>
                                            </div>
                                        ))}
                                        {preview.fields.length > 3 && (
                                            <p className="text-gray-400 text-sm">
                                                + {preview.fields.length - 3} outros campos...
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="flex justify-end space-x-3">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-xl transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                disabled={processing || !fileContent}
                                className="px-4 py-2 bg-teal-500 hover:bg-teal-400 disabled:opacity-50 disabled:cursor-not-allowed text-slate-900 font-medium rounded-xl transition-colors"
                            >
                                {processing ? 'Importando...' : 'Importar Estrutura'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}