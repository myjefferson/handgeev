<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">{{ __('workspace.import_export.import_topic') }}</h3>
            <button onclick="closeImportModal()" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Abas de Importação -->
            <div class="flex border-b border-slate-700">
                <button id="tabFile" class="import-tab py-2 px-4 border-b-2 border-blue-500 text-white font-medium">
                    <i class="fas fa-file-import mr-2"></i>{{ __('workspace.import_export.import_file') }}
                </button>
                {{-- <button id="tabExisting" class="import-tab py-2 px-4 border-b-2 border-transparent text-slate-400 hover:text-white">
                    <i class="fas fa-copy mr-2"></i>{{ __('workspace.import_export.import_existing') }}
                </button> --}}
            </div>
            
            <!-- Conteúdo - Importar por Arquivo -->
            <div id="tabFileContent" class="import-tab-content">
                <form id="importFileForm" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('workspace.import_export.topic_name') }}</label>
                            <input type="text" name="topic_title" required
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400"
                                placeholder="{{ __('workspace.import_export.topic_name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('workspace.import_export.json_file') }}</label>
                            <input type="file" name="file" accept=".json" required
                                class="w-full bg-slate-700 border border-slate-600 rounded-md px-3 py-2 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-400">
                            <p class="text-xs text-slate-400 mt-1">{{ __('workspace.import_export.file_requirements') }}</p>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Conteúdo - Importar Tópico Existente -->
            {{-- <div id="tabExistingContent" class="import-tab-content hidden">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('workspace.import_export.select_topic') }}</label>
                        <select id="existingTopicSelect" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                            <option value="">{{ __('workspace.import_export.loading_topics') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('workspace.import_export.new_topic_name') }}</label>
                        <input type="text" id="existingTopicTitle" 
                            class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400"
                            placeholder="{{ __('workspace.import_export.new_topic_name') }}">
                    </div>
                </div>
            </div> --}}
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                <button onclick="closeImportModal()" 
                    class="px-4 py-2 text-slate-400 hover:text-white transition-colors">
                    {{ __('workspace.import_export.cancel') }}
                </button>
                <button id="confirmImportBtn" onclick="confirmImport()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>{{ __('workspace.import_export.import') }}
                </button>
            </div>
        </div>
    </div>
</div>