@extends('template.dashboard')

@section('content_dashboard')
    <div class="max-w-4xl mx-auto">
        @include('components.header', [
            'title' => 'Configurações',
            'description' => 'Gerencie suas preferências e segurança da conta'
        ])
        
        <div class="mt-8 space-y-8">
            <!-- Seção de Segurança -->
            <section class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Segurança
                </h2>
                
                <div class="grid md:grid-cols-1 gap-6">
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">Senha</h3>
                        <p class="text-sm text-slate-400 mb-4">Atualize sua senha regularmente para manter a conta segura</p>
                        <button class="bg-teal-500 hover:bg-teal-600 text-slate-900 font-medium py-2 px-4 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 极速赛车开奖直播0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Alterar senha
                        </button>
                    </div>
                    
                    {{-- <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">Autenticação</h3>
                        <p class="text-sm text-slate-400 mb-4">Proteja sua conta com autenticação de dois fatores</p>
                        <button class="bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Configurar 2FA
                        </button>
                    </div> --}}
                </div>
            </section>

            <!-- Seção de API -->
            <section class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Códigos API
                    </h2>
                    <span class="bg-teal-400/10 text-teal-400 text-xs px-3 py-1 rounded-full">
                        {{ auth()->user()->getPlan()->name }}
                    </span>
                </div>
                
                <div class="space-y-6">
                    <!-- Código Primário -->
                    <div class="bg-slate-900 rounded-lg p-5 border border-slate-700">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Código API Primário</label>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <code class="font-mono text-teal-400 bg-slate-800 px-3 py-2 rounded-lg text-sm">
                                    {{ $settings->primary_hash_api ? $settings->primary_hash_api : 'Não gerado' }}
                                </code>
                            </div>
                            <button class="text-slate-400 hover:text-teal-400 transition-colors" title="Copiar código">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Use este código para integrações principais</p>
                    </div>
                    
                    <!-- Código Secundário -->
                    <div class="bg-slate-900 rounded-lg p-5 border border-slate-700">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Código API Secundário</label>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <code class="font-mono text-teal-400 bg-slate-800 px-3 py-2 rounded-lg text-sm">
                                    {{ $settings->secondary_hash_api ? $settings->secondary_hash_api : 'Não gerado' }}
                                </code>
                            </div>
                            <button class="text-slate-400 hover:text-teal-400 transition-colors" title="Copiar código">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Use para backups ou integrações secundárias</p>
                    </div>
                    
                    <!-- Botão de Gerar Código -->
                    <div class="bg-gradient-to-r from-teal-400/10 to-blue-400/10 rounded-lg p-5 border border-teal-400/20">
                        <h3 class="font-medium text-white mb-2">Gerar Novos Códigos</h3>
                        <p class="text-sm text-slate-400 mb-4">Ao gerar novos códigos, os antigos serão invalidados</p>
                        <button id="generateCodeButton" class="bg-teal-500 hover:bg-teal-600 text-slate-900 font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 20v-2h2.75l-.4-.35q-1.225-1.225-1.787-2.662T4 12.05q0-2.775 1.663-4.937T10 4.25v2.1Q8.2 7 7.1 8.563T6 12.05q0 1.125.425 2.188T7.75 16.2l.25.25V14h2v6zm10-.25v-2.1q1.8-.65 2.9-2.212T18 11.95q0-1.125-.425-2.187T16.25 7.8L16 7.55V10h-2V4h6v2h-2.75l.4.35q1.225 1.225 1.788 2.663T20 11.95q0 2.775-1.662 4.938T14 19.75"/>
                            </svg>
                            Gerar Novos Códigos API
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
                    Preferências
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">Idioma</h3>
                        <p class="text-sm text-slate-400 mb-4">Selecione o idioma da interface</p>
                        <select class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2">
                            <option value="pt">Português</option>
                            <option value="en">English</option>
                            <option value="es">Español</option>
                        </select>
                    </div>
                    
                    <div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
                        <h3 class="font-medium text-white mb-2">Fuso Horário</h3>
                        <p class="text-sm text-slate-400 mb-4">Configure seu fuso horário local</p>
                        <select class="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2">
                            <option value="America/Sao_Paulo">Brasília (GMT-3)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">New York (GMT-5)</option>
                        </select>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Função para copiar texto para a área de transferência
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Código copiado para a área de transferência!');
                }).catch(err => {
                    console.error('Erro ao copiar texto: ', err);
                });
            }

            // Copiar código primário
            $('.bg-slate-900:first-child .flex.items-center button').click(function() {
                const code = $(this).siblings().find('code').text();
                if (code !== 'Não gerado') {
                    copyToClipboard(code);
                }
            });

            // Copiar código secundário
            $('.bg-slate-900:nth-child(2) .flex.items-center button').click(function() {
                const code = $(this).siblings().find('code').text();
                if (code !== 'Não gerado') {
                    copyToClipboard(code);
                }
            });

            // Gerar novo código
            $('#generateCodeButton').click(function (e) {
                if (!confirm('Ao gerar novos códigos, os antigos serão invalidados. Continuar?')) {
                    return;
                }

                const button = $(this);
                const originalText = button.html();
                
                // Feedback visual
                button.prop('disabled', true).html(`
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-900 mr-2"></div>
                    Gerando...
                `);

                // Envia a solicitação AJAX
                $.ajax({
                    url: "{{ route('dashboard.settings.generateNewHashApi') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        if (response.success) {
                            // Atualiza os códigos na interface
                            $('.bg-slate-900:first-child code').text(response.data.primary_hash_api);
                            $('.bg-slate-900:nth-child(2) code').text(response.data.secondary_hash_api);
                            
                            // Feedback de sucesso
                            alert('Códigos gerados com sucesso!');
                        } else {
                            alert('Erro ao gerar novo código!');
                        }
                    },
                    error: function (xhr) {
                        alert('Ocorreu um erro: ' + xhr.responseText);
                    },
                    complete: function() {
                        // Restaura o botão
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