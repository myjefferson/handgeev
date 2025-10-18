
<button id="export-dropdown-button"
        data-dropdown-toggle="export-dropdown-menu"
        class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover"
        type="button" aria-expanded="false" aria-controls="export-dropdown-menu">
    <i class="fas fa-file-export mr-2"></i>
    Exportar
    <svg class="w-3 h-3 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
    </svg>
</button>

<div id="export-dropdown-menu" class="z-20 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-56 dark:bg-gray-700" role="menu" aria-labelledby="export-dropdown-button">
    <!-- Header do Dropdown -->
    <div class="px-4 py-3 text-sm text-gray-900 dark:text-white bg-teal-50 dark:bg-teal-900/20 rounded-t-lg">
        <div class="font-medium">Exportar Workspace</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ $workspace->topics->count() }} tópicos • {{ $workspace->totalFields() }} campos
        </div>
    </div>
    
    <!-- Opções de Exportação -->
    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="export-dropdown-button">
        <!-- Exportação Completa -->
        <li>
            <a href="{{ route('workspace.export', ['id' => $workspace->id]) }}" 
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white group">
                <i class="fas fa-download mr-3 text-teal-500 group-hover:scale-110 transition-transform"></i>
                <div>
                    <div class="font-medium">Exportação Completa</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Baixar JSON completo</div>
                </div>
            </a>
        </li>

        <!-- Exportação Rápida -->
        <li>
            <a href="{{ route('workspace.export.quick', ['id' => $workspace->id]) }}" 
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white group">
                <i class="fas fa-bolt mr-3 text-yellow-500 group-hover:scale-110 transition-transform"></i>
                <div>
                    <div class="font-medium">Exportação Rápida</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Apenas dados essenciais</div>
                </div>
            </a>
        </li>
        <!-- Divisor -->
        <li class="border-t border-gray-200 dark:border-gray-600 my-1"></li>
        <!-- Copiar JSON -->
        <li>
            <button type="button" 
                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white group export-copy-json"
                    data-workspace-id="{{ $workspace->id }}">
                <i class="fas fa-copy mr-3 text-blue-500 group-hover:scale-110 transition-transform"></i>
                <div>
                    <div class="font-medium">Copiar JSON</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Para área de transferência</div>
                </div>
            </button>
        </li>

        <!-- Visualizar JSON -->
        <li>
            <button type="button" 
                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white group export-preview-json"
                    data-workspace-id="{{ $workspace->id }}">
                <i class="fas fa-eye mr-3 text-green-500 group-hover:scale-110 transition-transform"></i>
                <div>
                    <div class="font-medium">Visualizar JSON</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Preview antes de baixar</div>
                </div>
            </button>
        </li>
    </ul>
</div>