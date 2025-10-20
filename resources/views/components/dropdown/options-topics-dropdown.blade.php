<div class="relative" id="importExportDropdown">
    <!-- Dropdown Menu -->
    <div id="dropdownImportExport" 
         class="z-10 hidden bg-slate-800 divide-y divide-slate-700 rounded-lg shadow-lg border border-slate-700 w-44 absolute top-12 left-0">
        <ul class="py-2 text-sm text-slate-200" aria-labelledby="dropdownImportExportButton">
            <!-- Item de Importação -->
            @if(!Auth::user()->isFree())
            <li>
                <button type="button" 
                        id="importTopicBtn"
                        class="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center">
                    <i class="fas fa-download w-5 h-5 mr-2 text-blue-400"></i>
                    {{ __('workspace.import_export.import') }}
                </button>
            </li>
            @endif
            
            {{-- <!-- Item de Exportação -->
            @if(count($workspace->topics) > 0)
            <li>
                <button type="button" 
                        id="exportAllBtn"
                        class="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center">
                    <i class="fas fa-upload w-5 h-5 mr-2 text-green-400"></i>
                    {{ __('workspace.import_export.export') }}
                </button>
            </li>
            @endif --}}
            
            {{-- <!-- Separador -->
            @if(!Auth::user()->isFree() && count($workspace->topics) > 0)
            <li class="border-t border-slate-700 my-1"></li>
            @endif
            
            <!-- Item de Download Rápido -->
            @if(count($workspace->topics) > 0)
            <li>
                <button type="button" 
                        id="quickExportBtn"
                        class="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center text-slate-400 hover:text-slate-200">
                    <i class="fas fa-file-export w-5 h-5 mr-2 text-purple-400"></i>
                    {{ __('workspace.import_export.quick_export') }}
                </button>
            </li>
            @endif --}}
        </ul>
    </div>
</div>

<style>
    #dropdownImportExport {
        backdrop-filter: blur(10px);
        background-color: rgba(30, 41, 59, 0.95);
    }

    #dropdownImportExport ul li button:hover {
        transform: translateX(2px);
        transition: all 0.2s ease;
    }
</style>