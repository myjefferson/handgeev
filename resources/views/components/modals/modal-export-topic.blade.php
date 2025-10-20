<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">{{ __('workspace.import_export.export_topic') }}</h3>
            <button onclick="closeExportModal()" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <p class="text-slate-400">{{ __('workspace.import_export.choose_export_method') }}</p>
            
            <div class="grid grid-cols-2 gap-4">
                <button onclick="exportTopic('json')" 
                    class="p-4 bg-slate-700 hover:bg-slate-600 rounded-lg border border-slate-600 transition-colors group">
                    <div class="text-center">
                        <i class="fas fa-code text-green-400 text-xl mb-2"></i>
                        <p class="text-white font-medium">{{ __('workspace.import_export.export_json') }}</p>
                        <p class="text-slate-400 text-sm mt-1">{{ __('workspace.import_export.json_structure') }}</p>
                    </div>
                </button>
                
                <button onclick="exportTopic('download')" 
                    class="p-4 bg-slate-700 hover:bg-slate-600 rounded-lg border border-slate-600 transition-colors group">
                    <div class="text-center">
                        <i class="fas fa-file-download text-blue-400 text-xl mb-2"></i>
                        <p class="text-white font-medium">{{ __('workspace.import_export.export_download') }}</p>
                        <p class="text-slate-400 text-sm mt-1">{{ __('workspace.import_export.json_file_download') }}</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>