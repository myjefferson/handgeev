// resources/js/Pages/Workspace/Show.jsx
import React, { useState, useEffect } from 'react';
import { Head, usePage, router, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import WorkspaceHeader from '@/Components/Workspace/WorkspaceHeader';
import TopicSidebar from '@/Components/Workspace/TopicSidebar';
import TopicContent from '@/Components/Workspace/TopicContent';
import Modals from '@/Components/Modals/WorkspaceModals';
import useLang from '@/Hooks/useLang';

export default function Workspace({ workspace, topicsWithLimits, workspaceLimits, availableStructures }) {
    const { __ } = useLang();
    const { auth } = usePage().props;
    const [selectedTopicId, setSelectedTopicId] = useState(workspace.topics[0]?.id || null);
    const [modals, setModals] = useState({
        import: false,
        export: false,
        rename: false,
        share: false
    });

    const selectedTopic = workspace.topics.find(topic => topic.id === selectedTopicId);

    const openModal = (modalName) => setModals(prev => ({ ...prev, [modalName]: true }));
    const closeModal = (modalName) => setModals(prev => ({ ...prev, [modalName]: false }));

    return (
        <DashboardLayout>
            <Head>
                {/* <title>{workspace.title} - HandGeev</title> */}
                <meta name="description" content={__('description').replace('{title}', workspace.title)} />
            </Head>

            <div className="max-w-full mx-auto">
                <header className="mb-6">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center">
                            <Link 
                                href={route('workspaces.show')}
                                className="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300"
                            >
                                <i className="fas fa-arrow-left mr-1"></i>
                                Voltar
                            </Link>
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    {workspace.title}
                                </h1>
                                <p className="text-gray-600 dark:text-gray-400 mt-1">
                                    Workspace com tópicos estruturados
                                </p>
                            </div>
                        </div>
                        <WorkspaceHeader 
                            workspace={workspace}
                            onOpenShare={() => openModal('share')}
                        />
                    </div>
                </header>

                <div className="flex flex-col lg:flex-row gap-6 min-h-dvh">
                    {/* Sidebar de Tópicos */}
                    <TopicSidebar
                        workspace={workspace}
                        topics={workspace.topics}
                        workspaceLimits={workspaceLimits}
                        topicsWithLimits={topicsWithLimits}
                        availableStructures={availableStructures}
                        selectedTopicId={selectedTopicId}
                        onSelectTopic={setSelectedTopicId}
                        onOpenImport={() => openModal('import')}
                        onOpenExport={() => openModal('export')}
                        onOpenRename={() => openModal('rename')}
                        auth={auth}
                    />

                    {/* Conteúdo Principal */}
                    <div className="flex-1">
                        {workspace.topics.map((topic) => (
                            <TopicContent
                                key={topic.id}
                                topic={topic}
                                topicLimits={topicsWithLimits[topic.id] || workspaceLimits}
                                availableStructures={availableStructures}
                                isVisible={topic.id === selectedTopicId}
                                workspace={workspace}
                                auth={auth}
                            />
                        ))}
                        
                        {workspace.topics.length === 0 && (
                            <div className="bg-slate-800 rounded-2xl border border-slate-700 p-8 text-center">
                                <i className="fas fa-cubes text-4xl text-gray-500 mb-4"></i>
                                <h3 className="text-lg font-semibold text-white mb-2">Nenhum tópico criado</h3>
                                <p className="text-gray-400 mb-4">
                                    Crie seu primeiro tópico vinculado a uma estrutura
                                </p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <Modals
                modals={modals}
                onClose={closeModal}
                workspace={workspace}
                selectedTopic={selectedTopic}
                auth={auth}
            />
        </DashboardLayout>
    );
}