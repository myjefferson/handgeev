<!-- Modal de Duplicação -->
<div id="duplicateModal" tabindex="-1" aria-hidden="true" 
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-slate-800 rounded-lg shadow border border-slate-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-700">
                <h3 class="text-lg font-semibold text-white">
                    📋 Duplicar Workspace
                </h3>
                <button type="button" 
                        class="text-slate-400 bg-transparent hover:bg-slate-700 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="duplicateModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <form id="duplicateForm" method="POST">
                @csrf
                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <label for="new_title" class="block mb-2 text-sm font-medium text-slate-300">
                            Nome do novo workspace
                        </label>
                        <input type="text" 
                            id="new_title" 
                            name="new_title"
                            class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                            placeholder="Ex: Meu Workspace - Cópia"
                            required
                            value=""
                            autocomplete="off"
                            >
                        <p class="mt-1 text-xs text-slate-400">
                            Escolha um nome único para o workspace duplicado
                        </p>
                    </div>
                    
                    <!-- Informações da duplicação -->
                    <div class="bg-slate-900/50 rounded-lg p-3 mb-4">
                        <p class="text-sm text-slate-300">
                            <span class="font-semibold">Serão duplicados:</span>
                        </p>
                        <ul class="text-xs text-slate-400 mt-1 space-y-1">
                            <li id="topicsCount">• 0 tópicos</li>
                            <li id="fieldsCount">• 0 campos</li>
                        </ul>
                    </div>

                    <!-- Mensagens de erro -->
                    <div id="errorMessage" class="hidden p-3 mb-3 text-sm text-red-400 bg-red-900/20 rounded-lg"></div>
                </div>
                
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-4 md:p-5 border-t border-slate-700 rounded-b">
                    <button type="button" 
                            data-modal-hide="duplicateModal"
                            class="py-2.5 px-5 text-sm font-medium text-slate-300 focus:outline-none bg-slate-700 rounded-lg border border-slate-600 hover:bg-slate-600 focus:z-10">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="duplicateSubmitBtn"
                            class="flex items-center py-2.5 px-5 ms-3 text-sm font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>