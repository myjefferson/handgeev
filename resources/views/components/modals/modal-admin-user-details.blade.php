<div id="userDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-slate-800 rounded-xl border border-slate-700 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <h3 class="text-xl font-semibold text-white">Detalhes do Usuário</h3>
            <button class="text-slate-400 hover:text-white close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div id="userDetailsContent" class="space-y-4">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
        
        <div class="flex justify-end p-6 border-t border-slate-700">
            <button class="bg-slate-700 text-white px-4 py-2 rounded-lg hover:bg-slate-600 transition-colors close-modal">
                Fechar
            </button>
        </div>
    </div>
</div>