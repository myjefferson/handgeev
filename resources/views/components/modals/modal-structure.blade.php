<!-- Modal Estrutura -->
<div id="structure-modal" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
            
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b rounded-t border-slate-700">
                <h3 class="text-xl font-bold text-white" id="modal-title">
                    Criar Nova Estrutura
                </h3>
                <button type="button" 
                        class="text-gray-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                        data-modal-hide="structure-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="structure-form" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="structure-id" name="id">
                
                <!-- Nome da Estrutura -->
                <div>
                    <label for="structure-name" class="block mb-2 text-sm font-medium text-gray-300">
                        Nome da Estrutura *
                    </label>
                    <input type="text" id="structure-name" name="name" 
                           class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-3.5 placeholder-gray-500"
                           placeholder="Ex: Produto, Cliente, Pedido" required>
                </div>
                
                <!-- Workspace -->
                <div>
                    <label for="workspace-id" class="block mb-2 text-sm font-medium text-gray-300">
                        Workspace *
                    </label>
                    <select id="workspace-id" name="workspace_id" 
                            class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-3.5" required>
                        <option value="" selected disabled>Selecione um workspace</option>
                        @foreach($workspaces as $workspace)
                        <option value="{{ $workspace->id }}">{{ $workspace->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Campos da Estrutura -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-300">
                            Campos da Estrutura *
                        </label>
                        <button type="button" id="add-field" 
                                class="text-teal-400 hover:text-teal-300 text-sm font-medium flex items-center focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-lg px-3 py-2">
                            <i class="fas fa-plus mr-2"></i>
                            Adicionar Campo
                        </button>
                    </div>
                    
                    <div id="structure-fields" class="space-y-4">
                        <!-- Campos serão adicionados dinamicamente via JavaScript -->
                    </div>
                </div>
            </form>

            <!-- Modal footer -->
            <div class="flex items-center justify-end p-6 space-x-3 border-t border-slate-700 rounded-b">
                <button type="button" 
                        data-modal-hide="structure-modal"
                        class="text-gray-300 bg-slate-700 hover:bg-slate-600 focus:ring-4 focus:outline-none focus:ring-slate-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        form="structure-form"
                        class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Salvar Estrutura
                </button>
            </div>
        </div>
    </div>
</div>