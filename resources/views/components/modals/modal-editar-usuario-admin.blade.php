<div id="editUserModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl border border-slate-700 w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-white">Editar Usuário</h3>
            <button type="button" data-modal-hide="editUserModal" class="close-modal text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form>
            <input type="hidden" id="edit_user_id">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Nome</label>
                        <input type="text" id="edit_user_name" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 disabled:bg-slate-700 disabled:text-slate-400" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Sobrenome</label>
                        <input type="text" id="edit_user_surname" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 disabled:bg-slate-700 disabled:text-slate-400" disabled>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Email</label>
                    <input type="email" id="edit_user_email" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 disabled:bg-slate-700 disabled:text-slate-400" disabled>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Perfil</label>
                    <select id="edit_user_role" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2">
                        <option value="free">Free</option>
                        <option value="start">Start</option>
                        <option value="pro">Pro</option>
                        <option value="premium">Premium</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Status</label>
                    <select id="edit_user_status" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2">
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                        <option value="suspended">Suspenso</option>
                        <option value="past_due">past_due</option>
                        <option value="unpaid">Não pago</option>
                        <option value="incomplete">Incompleto</option>
                        <option value="trial">Trial</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-8">
                <button type="button" data-modal-hide="editUserModal" class="close-modal px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                    Cancelar
                </button>
                <button type="button" class="save-edit px-4 py-2 bg-teal-500 text-slate-900 font-medium rounded-lg hover:bg-teal-400">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Estilos para o modal - mais específicos */
    #editUserModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    #editUserModal:not(.hidden) {
        opacity: 1;
        pointer-events: auto;
    }
    
    #editUserModal.hidden {
        display: none;
    }
    
    .modal-content {
        background-color: #1e293b;
        border-radius: 0.75rem;
        border: 1px solid #334155;
        width: 100%;
        max-width: 28rem;
        padding: 1.5rem;
    }
</style>