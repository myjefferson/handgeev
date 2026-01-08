import React from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

export default function Detail({ structure, topics }) {
    const { auth } = usePage().props
    const canEdit = structure.user_id === auth.user.id;
    const canDelete = canEdit && structure.topics_count === 0;

    const handleExport = (structureId) => {
        // Abre em nova janela para download
        window.open(route('structures.export', structureId), '_blank');
        
        // Ou usa um iframe oculto
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = route('structures.export', structureId);
        document.body.appendChild(iframe);
        
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 5000);
    };

    const handleDelete = () => {
        if (confirm(`Tem certeza que deseja excluir a estrutura "${structure.name}"? Esta ação não pode ser desfeita.`)) {
            router.delete(route('structures.destroy', structure.id));
        }
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
        <DashboardLayout title={`Estrutura: ${structure.name}`}>
            <Head>
                {/* <title>{structure.name} - HandGeev</title> */}
            </Head>

            <div className="max-w-6xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-4">
                            <Link
                                href={route('structures')}
                                className="p-2 text-gray-400 hover:text-teal-400 rounded-lg transition-colors"
                            >
                                <i className="fas fa-arrow-left"></i>
                            </Link>
                            <div>
                                <h1 className="text-2xl font-bold text-white">{structure.name}</h1>
                                <p className="text-gray-400 mt-1">
                                    {structure.description || 'Sem descrição'}
                                </p>
                            </div>
                        </div>
                        
                        <div className="flex space-x-3">
                            {canEdit && (
                                <button
                                    onClick={() => handleExport(structure.id)}
                                    className="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white font-medium rounded-xl transition-colors group relative"
                                    title="Exportar estrutura"
                                >
                                    <i className="fas fa-download"></i>
                                    <span className="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs bg-slate-800 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 border border-slate-700">
                                        Exportar
                                        <span className="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-slate-800"></span>
                                    </span>
                                </button>
                            )}
                            {canEdit && (
                                <Link
                                    href={route('structures.edit', structure.id)}
                                    className="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                                >
                                    <i className="fas fa-edit mr-2"></i>
                                    Editar
                                </Link>
                            )}
                            
                            {canDelete && (
                                <button
                                    onClick={handleDelete}
                                    className="px-4 py-2 bg-red-500 hover:bg-red-400 text-white font-medium rounded-xl transition-colors"
                                >
                                    <i className="fas fa-trash mr-2"></i>
                                    Excluir
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Metadados */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                        <div className="bg-slate-800 rounded-xl p-4 border border-slate-700">
                            <div className="text-sm text-gray-400">Campos</div>
                            <div className="text-2xl font-bold text-white">{structure.fields_count}</div>
                        </div>
                        <div className="bg-slate-800 rounded-xl p-4 border border-slate-700">
                            <div className="text-sm text-gray-400">Tópicos</div>
                            <div className="text-2xl font-bold text-white">{structure.topics_count}</div>
                        </div>
                        <div className="bg-slate-800 rounded-xl p-4 border border-slate-700">
                            <div className="text-sm text-gray-400">Visibilidade</div>
                            <div className="text-lg font-semibold text-teal-400">
                                {structure.is_public ? 'Pública' : 'Privada'}
                            </div>
                        </div>
                        <div className="bg-slate-800 rounded-xl p-4 border border-slate-700">
                            <div className="text-sm text-gray-400">Criador</div>
                            <div className="text-lg font-semibold text-white">{structure.user?.name}</div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Campos da Estrutura */}
                    <div className="bg-slate-800 rounded-2xl border border-slate-700 p-6">
                        <h2 className="text-lg font-semibold text-white mb-4">Campos da Estrutura</h2>
                        
                        <div className="space-y-3">
                            {structure.fields.map((field, index) => (
                                <div key={field.id} className="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h3 className="font-medium text-white">
                                                {field.name}
                                                {field.is_required && (
                                                    <span className="ml-2 text-xs bg-red-500 text-white px-2 py-1 rounded">Obrigatório</span>
                                                )}
                                            </h3>
                                            <p className="text-sm text-gray-400 mt-1">
                                                Tipo: {fieldTypes[field.type] || field.type}
                                                {field.default_value && ` • Padrão: ${field.default_value}`}
                                            </p>
                                        </div>
                                        <div className="text-sm text-gray-500">
                                            #{index + 1}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Tópicos que usam esta estrutura */}
                    <div className="bg-slate-800 rounded-2xl border border-slate-700 p-6">
                        <h2 className="text-lg font-semibold text-white mb-4">Tópicos Vinculados</h2>
                        
                        {topics.length === 0 ? (
                            <div className="text-center py-8">
                                <i className="fas fa-folder-open text-4xl text-gray-500 mb-4"></i>
                                <p className="text-gray-400">Nenhum tópico usando esta estrutura</p>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {topics.map(topic => (
                                    <div key={topic.id} className="bg-slate-700/50 rounded-lg p-4 border border-slate-600">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="font-medium text-white">{topic.title}</h3>
                                                <p className="text-sm text-gray-400 mt-1">
                                                    Workspace: {topic.workspace?.title}
                                                </p>
                                            </div>
                                            <Link
                                                href={`/workspace/${topic.workspace_id}`}
                                                className="px-3 py-1 bg-slate-600 hover:bg-slate-500 text-white text-sm rounded-lg transition-colors"
                                            >
                                                Ver
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}