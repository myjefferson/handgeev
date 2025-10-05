@extends('template.template-dashboard')

@section('title', __('import.title'))
@section('description', __('import.description'))

@section('content_dashboard')
    <div class="min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-2">

            <a href="{{ route('workspaces.index') }}" class="block w-max text-sm text-gray-300 hover:text-teal-400 transition-colors mb-8">
                <i class="fas {{ __('import.breadcrumb.icon') }} mr-1"></i> {{ __('import.breadcrumb.back') }}
            </a>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('import.header.title') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('import.header.subtitle') }}
                </p>
            </div>


            <!-- Card de Importação -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <!-- Informações sobre o formato -->
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                        <i class="fas {{ __('import.format_info.icon') }} mr-2"></i>{{ __('import.format_info.title') }}
                    </h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400">
                        {{ __('import.format_info.description') }}
                    </p>
                </div>
                <form action="{{ route('workspace.import') }}" method="POST" enctype="multipart/form-data" id="import-form" autocomplete="off">
                    @csrf
                    
                    <!-- Nome do Workspace -->
                    <div class="mb-6">
                        <label for="workspace_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('import.forms.workspace_title.label') }}
                        </label>
                        <input type="text" 
                            name="workspace_title" 
                            id="workspace_title"
                            value="{{ old('workspace_title') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="{{ __('import.forms.workspace_title.placeholder') }}"
                            required>
                        @error('workspace_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload do Arquivo -->
                    <div class="mb-6">
                        <label for="workspace_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('import.forms.file_upload.label') }}
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="workspace_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold">{{ __('import.forms.file_upload.drag_drop') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('import.forms.file_upload.file_info') }}
                                    </p>
                                </div>
                                <input id="workspace_file" 
                                    name="workspace_file" 
                                    type="file" 
                                    class="hidden" 
                                    accept=".json"
                                    required />
                            </label>
                        </div>
                        @error('workspace_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Preview do arquivo -->
                        <div id="file-preview" class="mt-3 hidden">
                            <div class="flex items-center justify-between p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-code text-teal-600 dark:text-teal-400 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('import.forms.file_upload.file_selected') }}</p>
                                        <p id="file-name" class="text-sm font-medium text-gray-900 dark:text-white"></p>
                                        <p id="file-size" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                    </div>
                                </div>
                                <button type="button" id="remove-file" class="text-red-500 hover:text-red-700" title="{{ __('import.forms.buttons.remove_file') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estrutura Esperada -->
                    <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('import.forms.expected_structure.title') }}
                        </h3>
                        <pre class="text-xs bg-slate-800 text-slate-200 p-3 rounded overflow-x-auto"><code>{
        "workspace": {
            "title": "Name Workspace",
            "type_workspace_id": 1,
            "topics": [
                {
                    "id": 27,
                    "title": "Name Topic",
                    "order": 1,
                    "fields": [
                        {
                            "is_visible": 1,
                            "key": "value"
                        }
                    ]
                }
            ]
        }
    }</code></pre>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('workspaces.index') }}" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ __('import.forms.buttons.cancel') }}
                        </a>
                        <button type="submit" 
                                id="import-btn"
                                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors teal-glow-hover disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-upload mr-2"></i>
                            {{ __('import.forms.buttons.import') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dicas -->
            <div class="mt-6 grid grid-cols-1 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas {{ __('import.tips.export.icon') }} text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('import.tips.export.title') }}</h4>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                {{ __('import.tips.export.description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('workspace_file');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const removeFileBtn = document.getElementById('remove-file');
        const importBtn = document.getElementById('import-btn');
        const importForm = document.getElementById('import-form');

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type !== 'application/json') {
                    alert('{{ __("import.alerts.invalid_file") }}');
                    fileInput.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) { // 10MB
                    alert('{{ __("import.alerts.file_too_large") }}');
                    fileInput.value = '';
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
                filePreview.classList.remove('hidden');
            }
        });

        // Remove file
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
        });

        // Form submission
        importForm.addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            if (!file) {
                e.preventDefault();
                alert('{{ __("import.alerts.invalid_file") }}');
                return;
            }

            // Disable button and show loading
            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="fas {{ __("import.processing.icon") }} mr-2"></i>{{ __("import.forms.buttons.importing") }}';
        });
    });
    </script>
@endpush