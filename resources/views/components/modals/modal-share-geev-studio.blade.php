 <!-- Modal de Compartilhamento -->
<div id="modalShareApiInterface" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <!-- Modal header -->
    <div class="bg-slate-700 rounded-lg">
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Compartilhar Workspace
            </h3>
            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modalShareApiInterface">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Fechar modal</span>
            </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5 space-y-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link de Compartilhamento</label>
                <div class="flex">
                    <input type="text" id="share-link" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ route('workspace.shared-geev-studio.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace?->workspace_key_api]) }}" readonly>
                    <button id="copy-link" data-tooltip-target="tooltip-copy-link" class="flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <i class="fas fa-copy mr-1"></i>
                        <span class="hidden md:inline">Copiar</span>
                    </button>
                    <div id="tooltip-copy-link" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                        Copiar link
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este link contém suas chaves de acesso de forma segura.</p>
            </div>
            
            {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Global Hash (User)</label>
                    <div class="flex">
                        <input type="text" id="global-hash" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ auth()->user()->global_key_api }}" readonly>
                        <button id="copy-global-hash" data-tooltip-target="tooltip-copy-global" class="flex-shrink-0 inline-flex items-center py-2.5 px-3 text-sm font-medium text-white bg-gray-700 rounded-r-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <i class="fas fa-copy"></i>
                        </button>
                        <div id="tooltip-copy-global" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Copiar Global Hash
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Workspace Key</label>
                    <div class="flex">
                        <input type="text" id="workspace-hash" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ $workspace->workspace_key_api }}" readonly>
                        <button id="copy-workspace-hash" data-tooltip-target="tooltip-copy-workspace" class="flex-shrink-0 inline-flex items-center py-2.5 px-3 text-sm font-medium text-white bg-gray-700 rounded-r-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <i class="fas fa-copy"></i>
                        </button>
                        <div id="tooltip-copy-workspace" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Copiar Workspace Key
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                </div>
            </div> --}}
            
            {{-- <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-300" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <span class="font-medium">Aviso de Segurança!</span>
                        <ul class="mt-1.5 list-disc list-inside">
                            <li>Compartilhe este link apenas com pessoas de confiança</li>
                            <li>Qualquer pessoa com o link poderá visualizar este workspace</li>
                            <li>O Global Hash é compartilhado entre todos os seus workspaces</li>
                        </ul>
                    </div>
                </div>
            </div> --}}
            <div class="flex items-center justify-end py-4 md:py-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                {{-- <button id="regenerate-global-hash" type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-sync-alt mr-1"></i> Regenerar Global Hash
                </button>
                <button id="regenerate-workspace-hash" type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-sync-alt mr-1"></i> Regenerar Workspace Key
                </button> --}}
                <a href="{{ route('workspace.shared-geev-studio.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <i class="fas fa-external-link-alt mr-1"></i> Abrir Link
                </a>
            </div>
        </div>
    </div>
</div>