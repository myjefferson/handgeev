// resources/js/Pages/Workspace/Settings.jsx
import React, { useState, useEffect } from 'react';
import { Head, usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import TabNavigation from '@/Components/Workspace/Settings/TabNavigation';
import OverviewTab from '@/Components/Workspace/Settings/OverviewTab';
import SecurityTab from '@/Components/Workspace/Settings/SecurityTab';
import AccessControlTab from '@/Components/Workspace/Settings/AccessControlTab';
import SettingsWorkspaceModals from '@/Components/Workspace/Settings/Modals/SettingsWorkspaceModals';
import Alert from '@/Components/Alerts/Alert';

export default function Settings({ workspace, hasPasswordWorkspace }) {
    const { auth } = usePage().props;

    console.log(auth)
    const [activeTab, setActiveTab] = useState('overview');
    const [modals, setModals] = useState({
        delete: false,
        duplicate: false
    });

    const openModal = (modalName) => setModals(prev => ({ ...prev, [modalName]: true }));
    const closeModal = (modalName) => setModals(prev => ({ ...prev, [modalName]: false }));

    const tabs = [
        { id: 'overview', label: 'Visão Geral', icon: 'chart-bar' },
        { id: 'security', label: 'Segurança & API', icon: 'shield-alt' },
        { id: 'access', label: 'Controle de Acesso', icon: 'users', upgrade: auth.user.is_free }
    ];

    return (
        <DashboardLayout>
            <Head>
                {/* <title>Configurações do Workspace - {workspace.title}</title> */}
                <meta name="description" content="Configurações do Workspace" />
            </Head>

            <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                <header className="mb-8">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center">
                            <Link 
                                href={route('workspace.show', { id: workspace.id })}
                                className="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <i className="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                            </Link>
                            <div>
                                <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
                                    Configurações do Workspace
                                </h1>
                                <p className="text-sm text-gray-500 dark:text-gray-400">{workspace.title}</p>
                            </div>
                        </div>
                    </div>
                </header>

                <TabNavigation 
                    tabs={tabs}
                    activeTab={activeTab}
                    onTabChange={setActiveTab}
                />

                <Alert/>

                <div className="mt-8">
                    {activeTab === 'overview' && (
                        <OverviewTab 
                            workspace={workspace}
                            onOpenDuplicate={() => openModal('duplicate')}
                            onOpenDelete={() => openModal('delete')}
                        />
                    )}
                    
                    {activeTab === 'security' && (
                        <SecurityTab 
                            workspace={workspace}
                            auth={auth}
                        />
                    )}
                    
                    {activeTab === 'access' && (
                        <AccessControlTab 
                            workspace={workspace}
                            hasPasswordWorkspace={hasPasswordWorkspace}
                            auth={auth}
                        />
                    )}
                </div>
            </div>

            <SettingsWorkspaceModals
                modals={modals}
                onClose={closeModal}
                workspace={workspace}
            />
        </DashboardLayout>
    );
}