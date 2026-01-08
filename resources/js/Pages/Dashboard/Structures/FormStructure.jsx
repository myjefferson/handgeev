// resources/js/Pages/Management/Structures/CreateEdit.jsx
import React, { useState, useEffect } from 'react';
import { Head, useForm, router, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';

export default function FormStructure({ structure = null, mode = 'create', auth = {} }) {
    const isEditing = mode === 'edit';
    const title = isEditing ? 'Editar Estrutura' : 'Nova Estrutura';

    // Supondo que o usuário tenha um campo 'plan' em auth.user
    const currentPlan = auth.user.plan?.name || 'free';

    const { data, setData, errors, processing, reset } = useForm({
        name: structure?.name || '',
        description: structure?.description || '',
        is_public: structure?.is_public || false,
        fields: structure?.fields || [
            { name: '', type: 'text', default_value: '', is_required: false, order: 0 }
        ]
    });

    // Ordena os campos quando a estrutura é carregada
    useEffect(() => {
        if (structure?.fields) {
            const sortedFields = [...structure.fields].sort((a, b) => a.order - b.order);
            setData('fields', sortedFields);
        }
    }, [structure]);

    const addField = () => {
        const newOrder = data.fields.length > 0 
            ? Math.max(...data.fields.map(f => f.order)) + 1 
            : 0;
            
        setData('fields', [
            ...data.fields,
            { 
                name: '', 
                type: 'text', 
                default_value: '', 
                is_required: false, 
                order: newOrder 
            }
        ]);
    };

    const removeField = (index) => {
        if (data.fields.length > 1) {
            const newFields = data.fields.filter((_, i) => i !== index);
            // Reordena os campos restantes
            const reorderedFields = newFields.map((field, idx) => ({
                ...field,
                order: idx
            }));
            setData('fields', reorderedFields);
        }
    };

    const updateField = (index, field, value) => {
        const newFields = [...data.fields];
        newFields[index][field] = value;
        setData('fields', newFields);
    };

    const moveField = (index, direction) => {
        if ((direction === -1 && index === 0) || (direction === 1 && index === data.fields.length - 1)) {
            return;
        }

        const newFields = [...data.fields];
        const newIndex = index + direction;
        
        // Troca as posições
        [newFields[index], newFields[newIndex]] = [newFields[newIndex], newFields[index]];
        
        // Atualiza a ordem
        newFields.forEach((field, idx) => {
            field.order = idx;
        });

        setData('fields', newFields);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const url = isEditing 
            ? route('structures.update', structure.id)
            : route('structures.store');
        const method = isEditing ? 'put' : 'post';

        router[method](url, data, {
            onSuccess: () => {
                if (!isEditing) {
                    reset();
                }
            }
        });
    };

    // Tipos disponíveis por plano
    const planTypes = {
        free: ['text', 'number', 'decimal', 'boolean', 'date', 'datetime'],
        start: ['text', 'number', 'decimal', 'boolean', 'date', 'datetime', 'email', 'url'],
        pro: ['text', 'number', 'decimal', 'boolean', 'date', 'datetime', 'email', 'url', 'json'],
        premium: ['text', 'number', 'decimal', 'boolean', 'date', 'datetime', 'email', 'url', 'json'],
        admin: ['text', 'number', 'decimal', 'boolean', 'date', 'datetime', 'email', 'url', 'json']
    };

    const fieldTypes = [
        { value: 'text', label: 'Texto' },
        { value: 'number', label: 'Número' },
        { value: 'decimal', label: 'Decimal' },
        { value: 'boolean', label: 'Booleano' },
        { value: 'date', label: 'Data' },
        { value: 'datetime', label: 'Data e Hora' },
        { value: 'email', label: 'E-mail' },
        { value: 'url', label: 'URL' },
        { value: 'json', label: 'JSON' }
    ];

    // Estado para controlar qual campo está sendo visualizado
    const [selectedFieldIndex, setSelectedFieldIndex] = useState(null);

    // Verifica se um tipo está disponível no plano atual
    const isTypeAvailable = (type) => {
        return planTypes[currentPlan]?.includes(type) || false;
    };

    // Filtra tipos disponíveis para o plano atual
    const availableFieldTypes = fieldTypes.filter(type => 
        isTypeAvailable(type.value)
    );

    // Tipos bloqueados por plano
    const blockedTypes = fieldTypes.filter(type => 
        !isTypeAvailable(type.value)
    );

    // Verifica se há tipos bloqueados para mostrar o aviso
    const hasBlockedTypes = blockedTypes.length > 0;

    return (
        <DashboardLayout title={title}>
            <Head>
                {/* <title>{title} - HandGeev</title> */}
            </Head>

            <div className="max-w-4xl mx-auto">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-white">{title}</h1>
                            <p className="text-gray-400 mt-2">
                                {isEditing 
                                    ? 'Modifique os campos da sua estrutura' 
                                    : 'Crie um modelo reutilizável para seus tópicos'
                                }
                            </p>
                        </div>
                        <Link
                            href={route('structures')}
                            className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-xl transition-colors"
                        >
                            <i className="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </Link>
                    </div>
                </div>
                
                <Alert/>
                
                {/* Informação sobre tipos por plano */}
                {hasBlockedTypes && (
                    <div className="mb-6 bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                        <div className="flex items-start justify-between">
                            <div className="flex-1">
                                <h3 className="text-sm font-semibold text-teal-400 mb-1">
                                    Faça um upgrade e tenha mais Tipos
                                </h3>
                                <div className="text-sm text-gray-300 space-y-2">
                                    {
                                        auth.user.plan?.name === 'free' &&
                                        <div className="flex items-center">
                                            <span className="inline-block w-4 h-4 bg-blue-500 rounded mr-2"></span>
                                            <span><strong>Upgrade para START:</strong> Tenha os tipos: E-mail, URL</span>
                                        </div>
                                    }
                                    {
                                        ['free', 'start', 'admin'].includes(auth.user.plan?.name) &&
                                        <div className="flex items-center">
                                            <span className="inline-block w-4 h-4 bg-yellow-500 rounded mr-2"></span>
                                            <span><strong>Upgrade para PRO ou PREMIUM:</strong> Tenha o tipo: JSON</span>
                                        </div>
                                    }
                                </div>
                            </div>
                            <Link
                                href={route('subscription.pricing')} // Ajuste a rota conforme sua aplicação
                                className="ml-4 px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-lg text-sm transition-colors whitespace-nowrap"
                            >
                                <i className="fas fa-crown mr-2"></i>
                                Mudar de Plano
                            </Link>
                        </div>
                    </div>
                )}
                
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Informações Básicas */}
                    <div className="bg-slate-800 rounded-2xl border border-slate-700 p-6">
                        <h2 className="text-lg font-semibold text-white mb-4">Informações Básicas</h2>
                        
                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-300 mb-2">
                                    Nome da Estrutura *
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                    placeholder="Ex: Produto, Cliente, Pedido"
                                    required
                                />
                                {errors.name && <p className="text-red-400 text-sm mt-1">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-300 mb-2">
                                    Descrição
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={3}
                                    className="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                    placeholder="Descreva o propósito desta estrutura..."
                                />
                                {errors.description && <p className="text-red-400 text-sm mt-1">{errors.description}</p>}
                            </div>

                            <div className="flex items-center">
                                <input
                                    type="checkbox"
                                    id="is_public"
                                    checked={data.is_public}
                                    onChange={(e) => setData('is_public', e.target.checked)}
                                    className="mr-3 rounded bg-slate-600 border-slate-500 text-teal-400 focus:ring-teal-400"
                                />
                                <label htmlFor="is_public" className="text-sm text-gray-300">
                                    Tornar esta estrutura pública (outros usuários poderão usá-la)
                                </label>
                            </div>
                        </div>
                    </div>

                    {/* Campos da Estrutura */}
                    <div className="bg-slate-800 rounded-2xl border border-slate-700 p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="text-lg font-semibold text-white">Campos</h2>
                            <button
                                type="button"
                                onClick={addField}
                                className="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                            >
                                <i className="fas fa-plus mr-2"></i>
                                Adicionar Campo
                            </button>
                        </div>

                        <div className="space-y-4">
                            {data.fields.map((field, index) => (
                                <div 
                                    key={index} 
                                    className="bg-slate-700/50 rounded-xl p-4 border border-slate-600"
                                    onFocus={() => setSelectedFieldIndex(index)}
                                >
                                    <div className="flex items-center justify-between mb-4">
                                        <h3 className="text-md font-medium text-white">
                                            Campo {index + 1}
                                        </h3>
                                        <div className="flex space-x-2">
                                            {/* Botões de mover */}
                                            <button
                                                type="button"
                                                onClick={() => moveField(index, -1)}
                                                disabled={index === 0}
                                                className="p-2 text-gray-400 hover:text-teal-400 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition-colors"
                                                title="Mover para cima"
                                            >
                                                <i className="fas fa-arrow-up"></i>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => moveField(index, 1)}
                                                disabled={index === data.fields.length - 1}
                                                className="p-2 text-gray-400 hover:text-teal-400 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition-colors"
                                                title="Mover para baixo"
                                            >
                                                <i className="fas fa-arrow-down"></i>
                                            </button>
                                            
                                            {/* Botão remover */}
                                            {data.fields.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeField(index)}
                                                    className="p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors"
                                                    title="Remover campo"
                                                >
                                                    <i className="fas fa-times"></i>
                                                </button>
                                            )}
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-12 gap-4 items-start">
                                        {/* Nome do Campo */}
                                        <div className="col-span-4">
                                            <label className="block text-sm font-medium text-gray-300 mb-2">
                                                Nome do Campo *
                                            </label>
                                            <input
                                                type="text"
                                                value={field.name}
                                                onChange={(e) => updateField(index, 'name', e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-teal-400"
                                                placeholder="Ex: nome, preço, ativo"
                                                required
                                            />
                                        </div>

                                        {/* Tipo do Campo */}
                                        <div className="col-span-3 relative">
                                            <label className="block text-sm font-medium text-gray-300 mb-2">
                                                Tipo *
                                            </label>
                                            <select
                                                value={field.type}
                                                onChange={(e) => {
                                                    updateField(index, 'type', e.target.value);
                                                }}
                                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-teal-400 appearance-none"
                                                onFocus={() => setSelectedFieldIndex(index)}
                                            >
                                                {/* Tipos disponíveis */}
                                                {availableFieldTypes.map(type => (
                                                    <option key={type.value} value={type.value}>
                                                        {type.label}
                                                    </option>
                                                ))}
                                                
                                                {/* Tipos bloqueados */}
                                                {blockedTypes.map(type => (
                                                    <option 
                                                        key={type.value} 
                                                        value={type.value}
                                                        disabled
                                                        className="opacity-50 cursor-not-allowed"
                                                    >
                                                        {type.label} 🔒
                                                    </option>
                                                ))}
                                            </select>
                                            
                                            {/* Tooltip para tipos bloqueados */}
                                            {!isTypeAvailable(field.type) && (
                                                <div className="absolute top-full left-0 mt-2 w-64 bg-slate-900 border border-slate-700 rounded-lg p-3 z-10 shadow-lg">
                                                    <div className="flex items-start">
                                                        <div className="flex-shrink-0">
                                                            <i className="fas fa-crown text-yellow-400 mt-1"></i>
                                                        </div>
                                                        <div className="ml-2">
                                                            <p className="text-sm font-medium text-white">
                                                                Tipo bloqueado
                                                            </p>
                                                            <p className="text-xs text-gray-300 mt-1">
                                                                Este tipo está disponível apenas para planos {currentPlan === 'free' ? 'START, PRO e PREMIUM' : 'PRO e PREMIUM'}.
                                                            </p>
                                                            <Link
                                                                href={route('subscription.pricing')}
                                                                className="inline-block mt-2 text-xs bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium px-3 py-1 rounded-lg transition-colors"
                                                            >
                                                                <i className="fas fa-rocket mr-1"></i>
                                                                Ver Planos
                                                            </Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                        </div>

                                        {/* Valor Padrão */}
                                        <div className="col-span-3">
                                            <label className="block text-sm font-medium text-gray-300 mb-2">
                                                Valor Padrão
                                            </label>
                                            <input
                                                type="text"
                                                value={field.default_value}
                                                onChange={(e) => updateField(index, 'default_value', e.target.value)}
                                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-teal-400"
                                                placeholder="Valor inicial"
                                            />
                                        </div>

                                        {/* Obrigatório e Ordem */}
                                        <div className="col-span-2 space-y-2">
                                            <div className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={field.is_required}
                                                    onChange={(e) => updateField(index, 'is_required', e.target.checked)}
                                                    className="mr-2 rounded bg-slate-600 border-slate-500 text-teal-400 focus:ring-teal-400"
                                                />
                                                <label className="text-sm text-gray-300">
                                                    Obrigatório
                                                </label>
                                            </div>
                                            <input
                                                type="hidden"
                                                value={field.order}
                                                onChange={(e) => updateField(index, 'order', parseInt(e.target.value))}
                                            />
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {errors.fields && <p className="text-red-400 text-sm mt-2">{errors.fields}</p>}
                    </div>

                    {/* Actions */}
                    <div className="flex justify-end space-x-4">
                        <Link
                            href={route('structures')}
                            className="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-xl transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar')} Estrutura
                        </button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}