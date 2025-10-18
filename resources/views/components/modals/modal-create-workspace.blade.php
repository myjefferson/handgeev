<div id="modal-add-workspace" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-slate-800 rounded-xl shadow-sm border border-slate-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-700">
                <h3 class="text-xl font-semibold text-white">
                    Criar Novo Workspace
                </h3>
                <button type="button" class="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-add-workspace">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-6">
                <form class="space-y-6" action="{{ route('workspace.store') }}" method="POST">
                    @csrf
                    <div>
                        <label for="workspace-title" class="block mb-2 text-sm font-medium text-slate-300">Título do Workspace</label>
                        <input type="text" name="title" id="workspace-title" 
                               class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-3" 
                               placeholder="Ex: Meu Portfólio de Projetos" required />
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-300">Tipo de Tópico</label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Opção: Tópico Único -->
                            <div>
                                <input type="radio" id="single-topic-card" name="type_workspace_id" value="1" class="hidden peer" required />
                                <label for="single-topic-card" class="inline-flex items-center justify-between w-full p-4 text-slate-400 bg-slate-700 border border-slate-600 rounded-lg cursor-pointer hover:border-cyan-500/50 peer-checked:border-cyan-500 peer-checked:text-cyan-400 transition-all duration-200">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">Tópico Único</div>
                                        <div class="w-full text-sm mt-1">Uma única seção para todo o conteúdo.</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </div>
                            
                            <!-- Opção: Vários Tópicos -->
                            <div>
                                <input type="radio" id="multiple-topics-card" name="type_workspace_id" value="2" class="hidden peer" />
                                <label for="multiple-topics-card" class="inline-flex items-center justify-between w-full p-4 text-slate-400 bg-slate-700 border border-slate-600 rounded-lg cursor-pointer hover:border-cyan-500/50 peer-checked:border-cyan-500 peer-checked:text-cyan-400 transition-all duration-200">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">Vários Tópicos</div>
                                        <div class="w-full text-sm mt-1">Organize seu conteúdo em várias seções.</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is-published" type="checkbox" name="is_published" 
                                   class="w-4 h-4 border border-slate-600 rounded-sm bg-slate-700 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-slate-800" />
                        </div>
                        <label for="is-published" class="ms-2 text-sm font-medium text-slate-300">Publicar agora</label>
                    </div>
                    
                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg px-5 py-3 text-center transition-colors">
                        Criar Workspace
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>