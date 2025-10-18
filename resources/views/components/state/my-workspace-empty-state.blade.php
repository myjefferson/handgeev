<div class="text-center py-12 bg-slate-800/50 rounded-xl border border-slate-700">
    <div class="text-slate-400 text-6xl mb-4">{{ $icon ?? '📁' }}</div>
    <h3 class="text-lg font-semibold text-white mb-2">{{ $title ?? 'Nenhum item encontrado' }}</h3>
    <p class="text-slate-400 mb-6">{{ $description ?? 'Não há itens para exibir no momento.' }}</p>
    
    @if($showButton ?? false)
        <button data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace" 
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ $buttonText ?? 'Criar Primeiro Workspace' }}
        </button>
    @endif
</div>