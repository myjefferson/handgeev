<!-- Main modal -->
<div id="modal-add-workspace" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add new Workspace
                </h3>
                <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-add-workspace">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <form class="space-y-6" action="{{ route('workspace.store') }}" method="POST">
                    @csrf
                    <div>
                        <label for="workspace-title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título do Workspace</label>
                        <input type="text" name="title" id="workspace-title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Ex: Meu Portfólio de Projetos" required />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de Tópico</label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Opção: Tópico Único -->
                            <div>
                                <input type="radio" id="single-topic-card" name="type_workspace_id" value="1" class="hidden peer" required />
                                <label for="single-topic-card" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">Tópico Único</div>
                                        <div class="w-full text-sm">Uma única seção para todo o conteúdo.</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </div>
                            <!-- Opção: Vários Tópicos -->
                            <div>
                                <input type="radio" id="multiple-topics-card" name="type_workspace_id" value="2" class="hidden peer" />
                                <label for="multiple-topics-card" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">Vários Tópicos</div>
                                        <div class="w-full text-sm">Organize seu conteúdo em várias seções.</div>
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
                            <input id="is-published" type="checkbox" name="is_published" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-teal-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-teal-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                        </div>
                        <label for="is-published" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Publicar agora</label>
                    </div>
                    <button type="submit" class="w-full text-teal-950 bg-teal-400 hover:bg-teal-600 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg px-5 py-2.5 text-center dark:bg-teal-400 dark:hover:bg-teal-600 dark:focus:ring-teal-800">Create Workspace</button>
                </form>
            </div>
        </div>
    </div>
</div>
