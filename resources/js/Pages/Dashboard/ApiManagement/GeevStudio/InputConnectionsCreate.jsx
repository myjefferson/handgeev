import { Head, Link, useForm } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';
import { useState, useEffect } from 'react';

export default function InputConnectionsCreate({ workspace, structures }) {
    const [selectedStructure, setSelectedStructure] = useState(null);
    const [sourceType, setSourceType] = useState('rest_api');
    
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        structure_id: '',
        trigger_field_id: '',
        is_active: true,
        timeout_seconds: 30,
        prevent_loops: true,
        source: {
            type: 'rest_api',
            config: {
                url: '',
                method: 'GET',
                headers: {},
                body: null
            }
        },
        mappings: []
    });

    // Quando a estrutura é selecionada, carrega seus campos
    useEffect(() => {
        if (data.structure_id) {
            const structure = structures.find(s => s.id == data.structure_id);
            setSelectedStructure(structure);
            
            // Inicializa mapeamentos com os campos da estrutura
            if (structure && structure.fields && data.mappings.length === 0) {
                const newMappings = structure.fields.map(field => ({
                    source_field: '',
                    target_field_id: field.id,
                    transformation: null,
                    is_required: field.is_required || false
                }));
                setData('mappings', newMappings);
            }
        } else {
            setSelectedStructure(null);
            setData('mappings', []);
        }
    }, [data.structure_id]);

    // Atualiza um mapeamento específico
    const updateMapping = (index, field, value) => {
        const updatedMappings = [...data.mappings];
        updatedMappings[index][field] = value;
        setData('mappings', updatedMappings);
    };

    // Atualiza a configuração da fonte
    const updateSourceConfig = (field, value) => {
        setData('source', {
            ...data.source,
            config: {
                ...data.source.config,
                [field]: value
            }
        });
    };

    // Atualiza headers da fonte REST
    const updateHeader = (index, key, value) => {
        const headers = { ...data.source.config.headers };
        const headerKeys = Object.keys(headers);
        
        if (key === '') {
            // Remove header
            const newHeaders = {};
            headerKeys.forEach((k, i) => {
                if (i !== index) {
                    newHeaders[k] = headers[k];
                }
            });
            updateSourceConfig('headers', newHeaders);
        } else {
            // Atualiza/Adiciona header
            headers[key] = value;
            updateSourceConfig('headers', headers);
        }
    };

    const addHeader = () => {
        const headers = { ...data.source.config.headers };
        headers[''] = '';
        updateSourceConfig('headers', headers);
    };

    const submit = (e) => {
        e.preventDefault();
        
        // Remove headers vazios
        const config = { ...data.source.config };
        if (config.headers) {
            const cleanHeaders = {};
            Object.keys(config.headers).forEach(key => {
                if (key && key.trim() !== '' && config.headers[key] && config.headers[key].trim() !== '') {
                    cleanHeaders[key.trim()] = config.headers[key].trim();
                }
            });
            config.headers = cleanHeaders;
        }
        
        post(route('workspaces.input-connections.store', workspace.id));
    };

    return (
        <DashboardLayout>
            <Head title="Nova Conexão de Entrada" />
            
            <div className="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
                <div className="px-4 py-6 sm:px-0">
                    <div className="md:flex md:items-center md:justify-between mb-5">
                        <div className="flex-1 min-w-0">
                            <h2 className="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                                Nova Conexão de Entrada
                            </h2>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Configure uma nova conexão para buscar dados externos
                            </p>
                        </div>
                        <div className="mt-4 flex md:mt-0 md:ml-4">
                            <Link
                                href={route('workspaces.input-connections.index', workspace.id)}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            >
                                Cancelar
                            </Link>
                            <button
                                onClick={submit}
                                disabled={processing}
                                className="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            >
                                {processing ? 'Salvando...' : 'Salvar Conexão'}
                            </button>
                        </div>
                    </div>

                    <Alert />

                    <form onSubmit={submit} className="mt-6 space-y-8">
                        {/* Seção 1: Informações Básicas */}
                        <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                <i className="fas fa-info-circle mr-2 text-blue-500"></i>
                                Informações Básicas
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nome da Conexão *
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="Ex: Importar Usuários da API"
                                    />
                                    {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Descrição
                                    </label>
                                    <textarea
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                        rows="2"
                                        placeholder="Descreva o propósito desta conexão"
                                    />
                                    {errors.description && <p className="mt-1 text-sm text-red-600">{errors.description}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Estrutura de Destino *
                                    </label>
                                    <select
                                        value={data.structure_id}
                                        onChange={e => setData('structure_id', e.target.value)}
                                        className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                    >
                                        <option value="">Selecione uma estrutura</option>
                                        {structures.map(structure => (
                                            <option key={structure.id} value={structure.id}>
                                                {structure.name} ({structure.fields?.length || 0} campos)
                                            </option>
                                        ))}
                                    </select>
                                    {errors.structure_id && <p className="mt-1 text-sm text-red-600">{errors.structure_id}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Campo Gatilho (Opcional)
                                    </label>
                                    <select
                                        value={data.trigger_field_id}
                                        onChange={e => setData('trigger_field_id', e.target.value)}
                                        className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                        disabled={!selectedStructure}
                                    >
                                        <option value="">Nenhum (executa sempre)</option>
                                        {selectedStructure?.fields?.map(field => (
                                            <option key={field.id} value={field.id}>
                                                {field.name} ({field.type})
                                            </option>
                                        ))}
                                    </select>
                                    <p className="mt-1 text-xs text-gray-500">
                                        A conexão será executada quando este campo for modificado
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Seção 2: Configuração da Fonte */}
                        <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                <i className="fas fa-cloud-download-alt mr-2 text-green-500"></i>
                                Configuração da Fonte de Dados
                            </h3>
                            
                            <div className="mb-6">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo de Fonte *
                                </label>
                                <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    {[
                                        { value: 'rest_api', label: 'API REST', icon: 'fa-cloud' },
                                        { value: 'webhook', label: 'Webhook', icon: 'fa-broadcast-tower' },
                                        { value: 'csv', label: 'CSV', icon: 'fa-file-csv' },
                                        { value: 'excel', label: 'Excel', icon: 'fa-file-excel' },
                                        { value: 'form', label: 'Formulário', icon: 'fa-window-restore' }
                                    ].map(type => (
                                        <button
                                            type="button"
                                            key={type.value}
                                            onClick={() => {
                                                setData('source', { ...data.source, type: type.value });
                                                setSourceType(type.value);
                                            }}
                                            className={`p-4 border rounded-lg text-center transition-colors ${data.source.type === type.value
                                                ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20'
                                                : 'border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700'
                                            }`}
                                        >
                                            <i className={`fas ${type.icon} text-xl mb-2 ${data.source.type === type.value ? 'text-teal-600 dark:text-teal-400' : 'text-gray-400'}`}></i>
                                            <p className="text-sm font-medium">{type.label}</p>
                                        </button>
                                    ))}
                                </div>
                                {errors['source.type'] && <p className="mt-1 text-sm text-red-600">{errors['source.type']}</p>}
                            </div>

                            {/* Configuração para API REST */}
                            {sourceType === 'rest_api' && (
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            URL da API *
                                        </label>
                                        <input
                                            type="url"
                                            value={data.source.config.url || ''}
                                            onChange={e => updateSourceConfig('url', e.target.value)}
                                            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                            placeholder="https://api.exemplo.com/usuarios"
                                        />
                                        {errors['source.config.url'] && <p className="mt-1 text-sm text-red-600">{errors['source.config.url']}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Método HTTP
                                        </label>
                                        <select
                                            value={data.source.config.method || 'GET'}
                                            onChange={e => updateSourceConfig('method', e.target.value)}
                                            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                        >
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                            <option value="PUT">PUT</option>
                                            <option value="PATCH">PATCH</option>
                                            <option value="DELETE">DELETE</option>
                                        </select>
                                    </div>

                                    <div>
                                        <div className="flex items-center justify-between mb-2">
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Headers (Opcional)
                                            </label>
                                            <button
                                                type="button"
                                                onClick={addHeader}
                                                className="text-sm text-teal-600 hover:text-teal-700"
                                            >
                                                <i className="fas fa-plus mr-1"></i> Adicionar Header
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            {Object.entries(data.source.config.headers || {}).map(([key, value], index) => (
                                                <div key={index} className="flex space-x-2">
                                                    <input
                                                        type="text"
                                                        value={key}
                                                        onChange={e => updateHeader(index, e.target.value, value)}
                                                        className="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                                        placeholder="Chave (ex: Authorization)"
                                                    />
                                                    <input
                                                        type="text"
                                                        value={value}
                                                        onChange={e => updateHeader(index, key, e.target.value)}
                                                        className="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                                        placeholder="Valor (ex: Bearer token)"
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={() => updateHeader(index, '', '')}
                                                        className="px-3 text-red-600 hover:text-red-800"
                                                    >
                                                        <i className="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            ))}
                                            {(!data.source.config.headers || Object.keys(data.source.config.headers).length === 0) && (
                                                <p className="text-sm text-gray-500 italic">Nenhum header configurado</p>
                                            )}
                                        </div>
                                    </div>

                                    {['POST', 'PUT', 'PATCH'].includes(data.source.config.method) && (
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Corpo da Requisição (JSON - Opcional)
                                            </label>
                                            <textarea
                                                value={data.source.config.body || ''}
                                                onChange={e => updateSourceConfig('body', e.target.value)}
                                                className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 font-mono text-sm"
                                                rows="4"
                                                placeholder='{"parametro": "valor"}'
                                            />
                                        </div>
                                    )}
                                </div>
                            )}

                            {/* Configuração para outros tipos */}
                            {sourceType !== 'rest_api' && (
                                <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                    <div className="flex">
                                        <i className="fas fa-info-circle text-yellow-500 mt-1 mr-3"></i>
                                        <div>
                                            <p className="text-yellow-800 dark:text-yellow-200">
                                                Suporte para {sourceType === 'webhook' ? 'Webhooks' : 
                                                           sourceType === 'csv' ? 'arquivos CSV' : 
                                                           sourceType === 'excel' ? 'arquivos Excel' : 'formulários'} 
                                                será implementado em breve.
                                            </p>
                                            <p className="text-sm text-yellow-600 dark:text-yellow-300 mt-1">
                                                Por enquanto, utilize a opção de API REST.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {errors['source.config'] && <p className="mt-1 text-sm text-red-600">{errors['source.config']}</p>}
                        </div>

                        {/* Seção 3: Mapeamentos de Campos */}
                        {selectedStructure && data.mappings.length > 0 && (
                            <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    <i className="fas fa-exchange-alt mr-2 text-purple-500"></i>
                                    Mapeamento de Campos
                                </h3>
                                <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                    Defina como os dados da fonte serão mapeados para os campos da estrutura
                                </p>

                                <div className="space-y-4">
                                    {data.mappings.map((mapping, index) => {
                                        const field = selectedStructure.fields.find(f => f.id == mapping.target_field_id);
                                        if (!field) return null;

                                        return (
                                            <div key={index} className="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Campo de Destino
                                                        </label>
                                                        <div className="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                            <span className="font-medium text-gray-900 dark:text-white">{field.name}</span>
                                                            <span className="ml-2 text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                                                {field.type}
                                                            </span>
                                                            {field.is_required && (
                                                                <span className="ml-2 text-xs px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded">
                                                                    Obrigatório
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Campo na Fonte *
                                                            <span className="text-xs text-gray-500 ml-2">
                                                                Use "dot notation" para objetos aninhados
                                                            </span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={mapping.source_field}
                                                            onChange={e => updateMapping(index, 'source_field', e.target.value)}
                                                            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                                            placeholder="Ex: usuario.nome ou data[0].valor"
                                                        />
                                                        {errors[`mappings.${index}.source_field`] && (
                                                            <p className="mt-1 text-sm text-red-600">{errors[`mappings.${index}.source_field`]}</p>
                                                        )}
                                                    </div>

                                                    <div>
                                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Transformação (Opcional)
                                                        </label>
                                                        <select
                                                            value={mapping.transformation || ''}
                                                            onChange={e => updateMapping(index, 'transformation', e.target.value)}
                                                            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                                        >
                                                            <option value="">Nenhuma</option>
                                                            <option value="trim">Remover espaços (Trim)</option>
                                                            <option value="uppercase">Converter para maiúsculas</option>
                                                            <option value="lowercase">Converter para minúsculas</option>
                                                            <option value="date_format">Formatar data</option>
                                                            <option value="number_format">Formatar número</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div className="mt-3">
                                                    <label className="inline-flex items-center">
                                                        <input
                                                            type="checkbox"
                                                            checked={mapping.is_required || false}
                                                            onChange={e => updateMapping(index, 'is_required', e.target.checked)}
                                                            className="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                                        />
                                                        <span className="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                            Campo obrigatório na fonte
                                                        </span>
                                                    </label>
                                                    <p className="text-xs text-gray-500 mt-1">
                                                        Se marcado, a conexão falhará se este campo não estiver presente nos dados da fonte
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                                {errors.mappings && <p className="mt-1 text-sm text-red-600">{errors.mappings}</p>}
                            </div>
                        )}

                        {/* Seção 4: Configurações Avançadas */}
                        <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                <i className="fas fa-cogs mr-2 text-orange-500"></i>
                                Configurações Avançadas
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <input
                                            type="checkbox"
                                            checked={data.is_active}
                                            onChange={e => setData('is_active', e.target.checked)}
                                            className="mr-2 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                        />
                                        Conexão ativa
                                    </label>
                                    <p className="text-xs text-gray-500">
                                        Desative para pausar a execução automática desta conexão
                                    </p>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Timeout (segundos)
                                    </label>
                                    <input
                                        type="number"
                                        value={data.timeout_seconds}
                                        onChange={e => setData('timeout_seconds', parseInt(e.target.value) || 30)}
                                        className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500"
                                        min="1"
                                        max="300"
                                    />
                                    <p className="text-xs text-gray-500 mt-1">
                                        Tempo máximo para esperar pela resposta da fonte
                                    </p>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        <input
                                            type="checkbox"
                                            checked={data.prevent_loops}
                                            onChange={e => setData('prevent_loops', e.target.checked)}
                                            className="mr-2 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                        />
                                        Prevenir loops de execução
                                    </label>
                                    <p className="text-xs text-gray-500">
                                        Evita que a conexão seja executada múltiplas vezes para o mesmo tópico
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Botões de ação */}
                        <div className="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                href={route('workspaces.input-connections.index', workspace.id)}
                                className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            >
                                {processing ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                        Salvando...
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-save mr-2"></i>
                                        Salvar Conexão
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}