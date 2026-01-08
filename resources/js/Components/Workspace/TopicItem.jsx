import React from 'react';
import { router } from '@inertiajs/react';

export default function TopicItem({
    topic,
    topicLimits,
    isSelected,
    onSelect,
    onRename,
    translations
}) {
    const handleDelete = async (e) => {
        e.stopPropagation();
        
        if (confirm(`Tem certeza que deseja excluir o tópico "${topic.title}"?`)) {
            await router.delete(route('topic.destroy', topic.id));
        }
    };

    return (
        <div
            onClick={() => onSelect(topic.id)}
            className={`p-4 rounded-xl border transition-all cursor-pointer group ${
                isSelected 
                    ? 'bg-teal-500/10 border-teal-400/30' 
                    : 'bg-slate-700/50 border-slate-600 hover:bg-slate-700/70 hover:border-slate-500'
            }`}
        >
            <div className="flex items-center justify-between">
                <div className="flex-1 min-w-0">
                    <div className="flex items-center space-x-3">
                        <i className={`fas ${
                            topic.structure_id ? 'fa-cube' : 'fa-file-alt'
                        } ${isSelected ? 'text-teal-400' : 'text-gray-400'} text-sm`}></i>
                        
                        <div className="flex-1 min-w-0">
                            <h3 className={`font-medium truncate ${
                                isSelected ? 'text-teal-400' : 'text-white'
                            }`}>
                                {topic.title}
                            </h3>
                            
                            {topic.structure && (
                                <p className="text-xs text-gray-400 mt-1">
                                    {topic.structure.name}
                                    {topicLimits && (
                                        <span className="ml-2">
                                            • {topicLimits.currentFieldsCount} campos
                                        </span>
                                    )}
                                </p>
                            )}
                        </div>
                    </div>
                </div>
                
                <div className="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            onRename();
                        }}
                        className="p-1 text-gray-400 hover:text-teal-400 rounded transition-colors"
                        title="Renomear"
                    >
                        <i className="fas fa-edit text-xs"></i>
                    </button>
                    
                    <button
                        onClick={handleDelete}
                        className="p-1 text-gray-400 hover:text-red-400 rounded transition-colors"
                        title="Excluir"
                    >
                        <i className="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
            
            {/* Barra de progresso do limite */}
            {topicLimits && !topicLimits.isUnlimited && topicLimits.fieldsLimit > 0 && (
                <div className="mt-3">
                    <div className="flex justify-between text-xs text-gray-400 mb-1">
                        <span> {topicLimits.fieldsLimit < 9999 ? 'Campos usados' : ''}</span>
                        <span>{topicLimits.currentFieldsCount}/{topicLimits.fieldsLimit < 9999 ? topicLimits.fieldsLimit : '∞'}</span>
                    </div>
                    {
                        topicLimits.fieldsLimit < 9999 ? 
                            <div className="w-full bg-slate-600 rounded-full h-1.5">
                                <div 
                                    className={`h-1.5 rounded-full transition-all ${
                                        topicLimits.currentFieldsCount >= topicLimits.fieldsLimit 
                                            ? 'bg-red-400' 
                                            : 'bg-teal-400'
                                    }`}
                                    style={{
                                        width: `${Math.min((topicLimits.currentFieldsCount / topicLimits.fieldsLimit) * 100, 100)}%`
                                    }}
                                ></div>
                            </div> : ''
                    }
                    
                </div>
            )}
        </div>
    );
}