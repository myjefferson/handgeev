// resources/js/Components/Workspace/Settings/TabNavigation.jsx
import React from 'react';

export default function TabNavigation({ tabs, activeTab, onTabChange }) {
    return (
        <div className="border-b border-gray-200 dark:border-gray-700 mb-8">
            <div className="flex space-x-8">
                {tabs.map((tab) => (
                    <button
                        key={tab.id}
                        onClick={() => onTabChange(tab.id)}
                        className={`tab-button relative py-4 px-1 text-sm font-medium transition-colors ${
                            activeTab === tab.id
                                ? 'text-gray-700 dark:text-gray-300'
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                        }`}
                    >
                        <i className={`fas fa-${tab.icon} mr-2`}></i>
                        {tab.label}
                        {tab.upgrade && (
                            <span className="ml-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs px-2 py-0.5 rounded-full">
                                PRO
                            </span>
                        )}
                        {activeTab === tab.id && (
                            <div className="tab-border absolute bottom-0 left-0 w-full h-0.5 bg-teal-500"></div>
                        )}
                    </button>
                ))}
            </div>
        </div>
    );
}