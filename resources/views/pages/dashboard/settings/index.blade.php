@extends('template.template-dashboard')

@section('title', __('settings.title'))
@section('description', __('settings.description'))

@section('content_dashboard')
    <div class="max-w-4xl mx-auto">        
        <div class="flex justify-between items-center">
            <h3 class="title-header text-2xl font-semibold">{{ __('settings.title') }}</h3>
        </div>
        
        <div class="mt-8 space-y-8">
            <!-- Seção de API (mantida igual) -->
            <section class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ __('settings.api_code') }}
                    </h2>
                </div>
                
                <div class="space-y-6">
                    <!-- Código Primário -->
                    <div class="bg-slate-900 rounded-lg p-5 border border-slate-700">
                        <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('settings.global_api_key') }}</label>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <code class="font-mono text-teal-400 bg-slate-800 px-3 py-2 rounded-lg text-sm">
                                    {{ $settings->global_key_api ? $settings->global_key_api : __('settings.not_generated') }}
                                </code>
                            </div>
                            <button class="copy-btn text-slate-400 hover:text-teal-400 transition-colors" 
                                    data-text="{{ $settings->global_key_api }}"
                                    title="{{ __('settings.copy') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">{{ __('settings.global_api_description') }}</p>
                    </div>
                                        
                    <!-- Botão de Gerar Código -->
                    <div class="bg-gradient-to-r from-teal-400/10 to-blue-400/10 rounded-lg p-5 border border-teal-400/20">
                        <h3 class="font-medium text-white mb-2">{{ __('settings.generate_new_code') }}</h3>
                        <p class="text-sm text-slate-400 mb-4">{{ __('settings.generate_warning') }}</p>
                        <button id="generateCodeButton" class="bg-teal-400 hover:bg-teal-600 text-slate-900 font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 20v-2h2.75l-.4-.35q-1.225-1.225-1.787-2.662T4 12.05q0-2.775 1.663-4.937T10 4.25v2.1Q8.2 7 7.1 8.563T6 12.05q0 1.125.425 2.188T7.75 16.2l.25.25V14h2v6zm10-.25v-2.1q1.8-.65 2.9-2.212T18 11.95q0-1.125-.425-2.187T16.25 7.8L16 7.55V10h-2V4h6v2h-2.75l.4.35q1.225 1.225 1.788 2.663T20 11.95q0 2.775-1.662 4.938T14 19.75"/>
                            </svg>
                            {{ __('settings.generate_global_api') }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- Seção de Preferências -->
            <section class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('settings.preferences') }}
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Idioma -->
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">{{ __('settings.language') }}</h3>
                        <p class="text-sm text-slate-400 mb-4">{{ __('settings.language_description') }}</p>
                        <select id="languageSelect" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors">
                            <option value="pt_BR" {{ auth()->user()->language === 'pt_BR' ? 'selected' : '' }}>Português (Brasil)</option>
                            <option value="en" {{ auth()->user()->language === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ auth()->user()->language === 'es' ? 'selected' : '' }}>Español</option>
                        </select>
                        <div id="languageMessage" class="mt-2 text-xs hidden"></div>
                    </div>
                    
                    <!-- Fuso Horário -->
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">{{ __('settings.timezone') }}</h3>
                        <p class="text-sm text-slate-400 mb-4">{{ __('settings.timezone_description') }}</p>
                        <select id="timezoneSelect" class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors">
                            <option value="America/Sao_Paulo" {{ auth()->user()->timezone === 'America/Sao_Paulo' ? 'selected' : '' }}>Brasília (GMT-3)</option>
                            <option value="UTC" {{ auth()->user()->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ auth()->user()->timezone === 'America/New_York' ? 'selected' : '' }}>New York (GMT-5)</option>
                            <option value="Europe/London" {{ auth()->user()->timezone === 'Europe/London' ? 'selected' : '' }}>London (GMT+0)</option>
                            <option value="Asia/Tokyo" {{ auth()->user()->timezone === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (GMT+9)</option>
                        </select>
                        <div id="timezoneMessage" class="mt-2 text-xs hidden"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Função para mostrar mensagens
            function showMessage(element, message, isSuccess = true) {
                element.removeClass('hidden text-red-400 text-green-400')
                    .addClass(isSuccess ? 'text-green-400' : 'text-red-400')
                    .text(message)
                    .fadeIn();
                
                setTimeout(() => element.fadeOut(), 3000);
            }

            // Copiar código API
            $('.copy-btn').click(function() {
                const text = $(this).data('text');
                if (text && text !== '{{ __("settings.not_generated") }}') {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('{{ __("settings.copied") }}');
                    });
                }
            });

            // Alterar Idioma
            $('#languageSelect').change(function() {
                const language = $(this).val();
                const messageEl = $('#languageMessage');
                
                $.ajax({
                    url: "{{ route('settings.language') }}",
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        language: language
                    },
                    success: function (response) {
                        if (response.success) {
                            showMessage(messageEl, response.message, true);
                            // Recarrega a página para aplicar o novo idioma
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showMessage(messageEl, response.message, false);
                        }
                    },
                    error: function (xhr) {
                        const error = xhr.responseJSON?.message || '{{ __("settings.update_error") }}';
                        showMessage(messageEl, error, false);
                    }
                });
            });

            // Alterar Fuso Horário
            $('#timezoneSelect').change(function() {
                const timezone = $(this).val();
                const messageEl = $('#timezoneMessage');
                
                $.ajax({
                    url: "{{ route('settings.timezone') }}",
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        timezone: timezone
                    },
                    success: function (response) {
                        if (response.success) {
                            showMessage(messageEl, response.message, true);
                        } else {
                            showMessage(messageEl, response.message, false);
                        }
                    },
                    error: function (xhr) {
                        const error = xhr.responseJSON?.message || '{{ __("settings.update_error") }}';
                        showMessage(messageEl, error, false);
                    }
                });
            });

            // Gerar novo código API (código existente)
            $('#generateCodeButton').click(function (e) {
                if (!confirm('{{ __("settings.generate_confirm") }}')) {
                    return;
                }

                const button = $(this);
                const originalText = button.html();
                
                button.prop('disabled', true).html(`
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-900 mr-2"></div>
                    {{ __("settings.generating") }}
                `);

                $.ajax({
                    url: "{{ route('dashboard.settings.update.hash') }}",
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        if (response.success) {
                            $('.bg-slate-900:first-child code').text(response.data.global_key_api);
                            $('.copy-btn').data('text', response.data.global_key_api);
                            alert('{{ __("settings.generate_success") }}');
                        } else {
                            alert('{{ __("settings.generate_error") }}');
                        }
                    },
                    error: function (xhr) {
                        alert('{{ __("settings.update_error") }}: ' + xhr.responseText);
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        code {
            word-break: break-all;
            font-family: 'Fira Code', 'Monaco', 'Cascadia Code', 'Roboto Mono', monospace;
        }
    </style>
@endpush