<!-- resources/views/management/structures/index.blade.php -->
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-white">Meus Modelos</h1>
        <button class="btn-primary flex items-center" id="create-model-btn">
            <i class="fas fa-plus mr-2"></i>
            Novo Modelo
        </button>
    </div>

    <!-- Stats dos Modelos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-teal-500/20 flex items-center justify-center mr-4">
                    <i class="fas fa-layer-group text-teal-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Total de Modelos</p>
                    <p class="text-2xl font-bold text-white">{{ $structures->count() }} / {{ $modelLimit }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center mr-4">
                    <i class="fas fa-database text-blue-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Tópicos Vinculados</p>
                    <p class="text-2xl font-bold text-white">{{ $totalLinkedTopics }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center mr-4">
                    <i class="fas fa-infinity text-purple-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Limite do Plano</p>
                    <p class="text-2xl font-bold text-white">{{ $modelLimit == 0 ? 'Ilimitado' : $modelLimit }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Modelos -->
    <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs uppercase bg-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nome</th>
                        <th class="px-6 py-4 font-medium">Campos</th>
                        <th class="px-6 py-4 font-medium">Tópicos Vinculados</th>
                        <th class="px-6 py-4 font-medium">Workspace</th>
                        <th class="px-6 py-4 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($structures as $model)
                    <tr class="border-b border-slate-700 hover:bg-slate-750 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-cube text-teal-400 mr-3"></i>
                                <span class="font-medium text-white">{{ $model->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-slate-700 px-3 py-1 rounded-full text-xs">
                                {{ $model->fields->count() }} campos
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-500/20 px-3 py-1 rounded-full text-xs text-blue-400">
                                {{ $model->topics->count() }} tópicos
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-300">{{ $model->workspace->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button class="p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors edit-model" 
                                        data-model-id="{{ $model->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="p-2 text-blue-400 hover:text-blue-300 rounded-lg transition-colors view-model" 
                                        data-model-id="{{ $model->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors delete-model" 
                                        data-model-id="{{ $model->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-cube text-3xl mb-3"></i>
                            <p>Nenhum modelo criado ainda</p>
                            <p class="text-sm mt-2">Crie seu primeiro modelo para começar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>