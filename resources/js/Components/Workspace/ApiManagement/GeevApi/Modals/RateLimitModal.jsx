// resources/js/Components/Modals/RateLimitModal.jsx
import React from 'react';
import Modal from '@/Components/Modals/Modal';

export default function RateLimitModal({ 
    show = false, 
    onClose = () => {}, 
    rateLimitData = {}
}) {
    const limits = rateLimitData.limits || {};
    const usage = rateLimitData.current_usage || {};
    const plan = rateLimitData.plan || 'Free';

    const getUsagePercentage = (remaining, limit) => {
        return Math.max(0, ((limit - remaining) / limit) * 100);
    };

    const getUsageColor = (percentage) => {
        if (percentage >= 90) return 'bg-red-500';
        if (percentage >= 70) return 'bg-yellow-500';
        return 'bg-green-500';
    };

    const usageItems = [
        {
            period: 'minute',
            label: 'Por Minuto',
            remaining: usage.minute?.remaining || 0,
            limit: limits.per_minute || 60,
            resetIn: usage.minute?.available_in || 0
        },
        {
            period: 'hour',
            label: 'Por Hora',
            remaining: usage.hour?.remaining || 0,
            limit: limits.per_hour || 360,
            resetIn: usage.hour?.available_in || 0
        },
        {
            period: 'day',
            label: 'Por Dia',
            remaining: usage.day?.remaining || 0,
            limit: limits.per_day || 10000,
            resetIn: usage.day?.available_in || 0
        }
    ];

    const formatTime = (seconds) => {
        if (seconds < 60) return `${seconds} segundos`;
        const minutes = Math.floor(seconds / 60);
        return `${minutes} minuto${minutes > 1 ? 's' : ''}`;
    };

    return (
        <Modal show={show} onClose={onClose} maxWidth="md">
            <div className="bg-slate-800 rounded-lg p-6 relative">
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h3 className="text-lg font-semibold text-white">Limites de Requisição</h3>
                        <p className="text-slate-400 text-sm mt-1">Plano: <span className="text-cyan-400">{plan}</span></p>
                    </div>
                    <button 
                        onClick={onClose}
                        className="text-slate-400 hover:text-white cursor-pointer"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div className="space-y-6">
                    {usageItems.map((item) => {
                        const percentage = getUsagePercentage(item.remaining, item.limit);
                        const colorClass = getUsageColor(percentage);

                        return (
                            <div key={item.period} className="bg-slate-700 rounded-lg p-4">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-slate-300 text-sm font-medium">{item.label}</span>
                                    <span className="text-white text-sm font-mono">
                                        {item.remaining} / {item.limit}
                                    </span>
                                </div>
                                <div className="w-full bg-slate-600 rounded-full h-2 mb-2">
                                    <div 
                                        className={`h-2 rounded-full transition-all ${colorClass}`}
                                        style={{ width: `${percentage}%` }}
                                    ></div>
                                </div>
                                <div className="flex justify-between text-xs text-slate-400">
                                    <span>Reset em: {formatTime(item.resetIn)}</span>
                                    <span>{Math.round(percentage)}% utilizado</span>
                                </div>
                            </div>
                        );
                    })}

                    {/* Dicas */}
                    <div className="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                        <h6 className="text-blue-400 text-sm font-medium mb-2">💡 Dicas</h6>
                        <ul className="text-blue-300 text-xs space-y-1">
                            <li>• Monitore seus limites para evitar bloqueios</li>
                            <li>• Implemente retry com backoff exponencial</li>
                            <li>• Considere fazer upgrade para limites maiores</li>
                        </ul>
                    </div>

                    {/* Headers de Rate Limit */}
                    <div className="bg-slate-700 rounded-lg p-4">
                        <h6 className="text-slate-300 text-sm font-medium mb-2">Headers de Rate Limit</h6>
                        <div className="space-y-1 text-xs">
                            <div className="flex justify-between">
                                <code className="text-cyan-300">X-RateLimit-Limit</code>
                                <span className="text-slate-400">Limite total</span>
                            </div>
                            <div className="flex justify-between">
                                <code className="text-cyan-300">X-RateLimit-Remaining</code>
                                <span className="text-slate-400">Requisições restantes</span>
                            </div>
                            <div className="flex justify-between">
                                <code className="text-cyan-300">X-RateLimit-Reset</code>
                                <span className="text-slate-400">Timestamp do reset</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    );
}