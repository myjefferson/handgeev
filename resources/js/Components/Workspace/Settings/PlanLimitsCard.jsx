// resources/js/Components/Workspace/Settings/PlanLimitsCard.jsx
import { usePage } from '@inertiajs/react';
import React from 'react';

export default function PlanLimitsCard({ workspace }) {
    // Dados simulados do plano - você precisará adaptar para sua lógica real
    const { auth } = usePage().props;

    const planData = {
        topics: 10,
        fields: 100,
        current_plan: 'Free'
    };

    const currentTopics = workspace.topics?.length || 0;
    const currentFields = workspace.topics?.reduce((total, topic) => total + (topic.fields?.length || 0), 0) || 0;

    const topicsPercentage = planData.topics > 0 ? 
        Math.min(100, (currentTopics / planData.topics) * 100) : 100;
    
    const fieldsPercentage = planData.fields > 0 ? 
        Math.min(100, (currentFields / planData.fields) * 100) : 100;

    const isNearLimit = topicsPercentage > 80 || fieldsPercentage > 80;

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Limites do Plano
            </h2>
            
            <div className="space-y-4">
                <div>
                    <div className="flex justify-between text-sm mb-1">
                        <span className="text-gray-600 dark:text-gray-400">Tópicos</span>
                        <span className="font-medium text-gray-900 dark:text-white">
                            {currentTopics} 
                            {planData.topics < 9999 ? ` / ${planData.topics}` : ' / ∞'}
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div 
                            className="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                            style={{ width: `${topicsPercentage}%` }}
                        ></div>
                    </div>
                </div>

                <div>
                    <div className="flex justify-between text-sm mb-1">
                        <span className="text-gray-600 dark:text-gray-400">Campos</span>
                        <span className="font-medium text-gray-900 dark:text-white">
                            {currentFields} 
                            {planData.fields > 0 ? ` / ${planData.fields}` : ' / ∞'}
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div 
                            className="bg-green-500 h-2 rounded-full transition-all duration-500" 
                            style={{ width: `${fieldsPercentage}%` }}
                        ></div>
                    </div>
                </div>

                <div className="pt-3 border-t border-gray-200 dark:border-gray-600">
                    <div className="flex items-center justify-between">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Plano Atual</span>
                        <span className="px-2 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-800 dark:text-teal-300 rounded text-xs font-medium">
                            {auth.user.plan?.name}
                        </span>
                    </div>
                </div>

                {isNearLimit && (
                    <div className="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                        <p className="text-amber-800 dark:text-amber-300 text-xs">
                            Você está próximo do limite do seu plano.{' '}
                            <a 
                                href={route('subscription.pricing')} 
                                className="underline hover:text-amber-600 dark:hover:text-amber-200"
                            >
                                Faça upgrade para mais recursos.
                            </a>
                        </p>
                    </div>
                )}
            </div>
        </div>
    );
}