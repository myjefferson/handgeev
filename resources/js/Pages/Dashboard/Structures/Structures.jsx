// resources/js/Pages/Management/Structures/Show.jsx
import React, { useState } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import UpsellButton from '@/Components/Buttons/UpgradeButton';
import Alert from '@/Components/Alerts/Alert';
import ImportStructureModal from '@/Components/Modals/ImportStructureModal';

export default function StructuresShow({ structures }) {
    const { auth } = usePage().props;
    const [showImportModal, setShowImportModal] = useState(false);

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

    const handleDelete = (structure) => {
        if (confirm(`Tem certeza que deseja excluir a estrutura "${structure.name}"? Esta ação não pode ser desfeita.`)) {
            router.delete(route('structures.destroy', structure.id));
        }
    };

    return (
        <DashboardLayout title="Minhas Estruturas" description="Gerencie suas estruturas de dados">
            <Head>
                {/* <title>Estruturas - HandGeev</title> */}
            </Head>

            <div className="max-w-7xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-white">Estruturas</h1>
                            <p className="text-gray-400 mt-2">
                                Crie modelos reutilizáveis para organizar seus tópicos
                            </p>
                        </div>
                        <div className='space-x-3'>
                            <div className='flex space-x-4'>
                                { auth.user.plan?.name !== 'free' ? (
                                        <button
                                            onClick={() => setShowImportModal(true)}
                                            className="px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-xl transition-colors duration-300 teal-glow-hover"
                                        >
                                            <i className="fas fa-upload mr-2"></i>
                                            <span>Importar</span>
                                        </button>
                                        
                                ) : ( <UpsellButton title="Importar" /> )}
                                { auth.user.plan?.structures >= structures.length ? (
                                    <Link
                                        href={route('structures.create')}
                                        className="px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors duration-300 teal-glow-hover"
                                    >
                                        <i className="fas fa-plus mr-2"></i>
                                        Nova Estrutura
                                    </Link>
                                ) : ( <UpsellButton title="Adicionar estrutura"/> )}
                            </div>
                        </div>
                    </div>
                </div>

                <Alert />

                {/* Structures Grid */}
                {structures.length === 0 ? (
                    <div className="text-center py-12">
                        <div className="bg-slate-800/50 rounded-2xl p-8 max-w-md mx-auto">
                            <i className="fas fa-cubes text-4xl text-gray-500 mb-4"></i>
                            <h3 className="text-lg font-medium text-white mb-2">Nenhuma estrutura criada</h3>
                            <p className="text-gray-400 mb-6">
                                Crie sua primeira estrutura para organizar os campos dos seus tópicos
                            </p>
                            <Link
                                href={route('structures.create')}
                                className="px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                            >
                                Criar Primeira Estrutura
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {structures.map((structure) => (
                            <div key={structure.id} className="bg-slate-800 rounded-2xl border border-slate-700 p-6 hover:border-teal-400/30 transition-colors">
                                <div className="flex items-start justify-between mb-4">
                                    <h3 className="text-lg font-semibold text-white">{structure.name}</h3>
                                    <div className="flex space-x-2">
                                        <button
                                            onClick={() => handleExport(structure.id)}
                                            className="p-2 text-gray-400 hover:text-teal-400 rounded-lg transition-colors"
                                            title="Exportar"
                                        >
                                            <i className="fas fa-download"></i>
                                        </button>
                                        <Link
                                            href={route('structures.edit', structure.id)}
                                            className="p-2 text-gray-400 hover:text-teal-400 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <i className="fas fa-edit"></i>
                                        </Link>
                                        {structure.topics_count === 0 && (
                                            <button
                                                onClick={() => handleDelete(structure)}
                                                className="p-2 text-gray-400 hover:text-red-400 rounded-lg transition-colors"
                                                title="Excluir"
                                            >
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        )}
                                    </div>
                                </div>

                                <p className="text-gray-400 text-sm mb-4">
                                    {structure.description || 'Sem descrição'}
                                </p>

                                <div className="space-y-2 mb-4">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-400">Campos:</span>
                                        <span className="text-white">{structure.fields_count}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-400">Tópicos vinculados:</span>
                                        <span className="text-white">{structure.topics_count}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-400">Visibilidade:</span>
                                        <span className={structure.is_public ? 'text-teal-400' : 'text-gray-400'}>
                                            {structure.is_public ? 'Pública' : 'Privada'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-gray-400">Criado em:</span>
                                        <span className="text-white">
                                            {new Date(structure.created_at).toLocaleDateString('pt-BR')}
                                        </span>
                                    </div>
                                </div>

                                <div className="flex space-x-2">
                                    <Link
                                        href={route('structures.show', structure.id)}
                                        className="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors text-center"
                                    >
                                        Ver Detalhes
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {/* Import Modal */}
                {showImportModal && (
                    <ImportStructureModal
                        onClose={() => setShowImportModal(false)}
                        onSuccess={() => {
                            setShowImportModal(false);
                            router.reload();
                        }}
                    />
                )}
            </div>
        </DashboardLayout>
    );
}