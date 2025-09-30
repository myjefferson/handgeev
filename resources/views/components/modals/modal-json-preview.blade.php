<!-- Modal para visualização do JSON -->
<div id="json-preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg w-11/12 max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-file-code mr-2 text-teal-500"></i>
                Preview do JSON - {{ $workspace->title }}
            </h3>
            <button type="button" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    onclick="closeJsonPreview()">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 overflow-auto max-h-[70vh]">
            <pre id="json-preview-content" class="text-sm bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto font-mono"></pre>
        </div>
        <div class="flex justify-between items-center p-4 border-t border-gray-200 dark:border-gray-600">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Use Ctrl+C para copiar ou o botão abaixo
            </div>
            <div class="flex space-x-2">
                <button type="button" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        onclick="closeJsonPreview()">
                    <i class="fas fa-times mr-2"></i>
                    Fechar
                </button>
                <button type="button" 
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors flex items-center"
                        onclick="copyJsonToClipboard()">
                    <i class="fas fa-copy mr-2"></i>
                    Copiar JSON
                </button>
            </div>
        </div>
    </div>
</div>