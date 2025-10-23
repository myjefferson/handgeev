<!-- Modal para Deletar Conta - ATUALIZADO COM SUGESTÃO DE CANCELAMENTO -->
<div id="delete-account-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-700">
                <h3 class="text-lg font-semibold text-white">
                    Deletar Conta
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="delete-account-modal">
                    <i class="fas fa-times"></i>
                    <span class="sr-only">Fechar modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ route('user.account.delete') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-medium">Atenção!</h4>
                                <p class="text-gray-400 text-sm">Esta ação tem consequências permanentes.</p>
                            </div>
                        </div>
                        
                        <!-- Verificação de assinatura ativa -->
                        @if(Auth::user()->hasActiveSubscription() || Auth::user()->isOnGracePeriod())
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4 mb-4">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-exclamation-circle text-amber-400 mr-2 text-lg"></i>
                                    <span class="text-amber-400 font-medium text-lg">Assinatura Ativa Detectada</span>
                                </div>
                                
                                <div class="space-y-3">
                                    <p class="text-amber-300 text-sm">
                                        <strong>Você possui uma assinatura ativa ou em período de cortesia.</strong>
                                    </p>
                                    
                                    <div class="bg-slate-700/50 rounded-lg p-3 border border-slate-600">
                                        <p class="text-amber-200 text-sm mb-3">
                                            Para deletar sua conta, você precisa primeiro cancelar sua assinatura e aguardar a expiração do plano.
                                        </p>
                                        
                                        <div class="text-xs text-amber-300 space-y-1">
                                            <div class="flex items-start">
                                                <i class="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                <span>Cancelar assinatura</span>
                                            </div>
                                            <div class="flex items-start">
                                                <i class="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                <span>Aguardar expiração do plano</span>
                                            </div>
                                            <div class="flex items-start">
                                                <i class="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                <span>Deletar conta</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botão para gerenciar assinatura -->
                                    <div class="flex flex-col space-y-2 mt-4">
                                        <a href="{{ route('billing.show') }}" 
                                           class="w-full bg-amber-600 hover:bg-amber-500 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-cog mr-2"></i>
                                            Gerenciar Minha Assinatura
                                        </a>
                                        
                                        <a href="{{ route('subscription.pricing') }}" 
                                           class="w-full bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 border border-slate-600 flex items-center justify-center">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Ver Planos de Assinatura
                                        </a>
                                    </div>

                                    <div class="text-xs text-amber-400 mt-3 p-2 bg-amber-500/5 rounded-lg border border-amber-500/10">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Após o cancelamento e expiração do plano, você poderá deletar sua conta permanentemente.
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-300 text-sm mb-4">
                                Para confirmar que você realmente deseja deletar sua conta, por favor digite sua senha abaixo.
                                Sua conta será marcada para exclusão e removida permanentemente após 30 dias.
                            </p>

                            <div class="space-y-3">
                                <div>
                                    <label for="delete_password" class="block text-sm font-medium text-gray-400 mb-2">
                                        Sua Senha
                                    </label>
                                    <input type="password" 
                                           id="delete_password" 
                                           name="password" 
                                           required
                                           class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-colors"
                                           placeholder="Digite sua senha atual">
                                    @error('password')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex items-start p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <i class="fas fa-info-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-red-400 text-sm">
                                            <strong>Você perderá permanentemente:</strong>
                                        </p>
                                        <ul class="text-red-300 text-sm mt-1 space-y-1">
                                            <li class="flex items-center">
                                                <i class="fas fa-times mr-2 text-xs"></i>
                                                Todas as suas workspaces
                                            </li>
                                            <li class="flex items-center">
                                                <i class="fas fa-times mr-2 text-xs"></i>
                                                Todos os tópicos e campos
                                            </li>
                                            <li class="flex items-center">
                                                <i class="fas fa-times mr-2 text-xs"></i>
                                                Histórico de atividades
                                            </li>
                                            <li class="flex items-center">
                                                <i class="fas fa-times mr-2 text-xs"></i>
                                                Configurações pessoais
                                            </li>
                                        </ul>
                                        <div class="mt-2 pt-2 border-t border-red-500/20">
                                            <p class="text-amber-300 text-sm">
                                                <strong>Período de recuperação:</strong> 30 dias
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        @if(!Auth::user()->hasActiveSubscription() && !Auth::user()->isOnGracePeriod())
                            <button type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-500 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Confirmar Deleção
                            </button>
                        @endif
                        <button type="button" 
                                data-modal-toggle="delete-account-modal"
                                onclick="resetFormDeleteAccount()"
                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 border border-slate-600 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            {{ Auth::user()->hasActiveSubscription() || Auth::user()->isOnGracePeriod() ? 'Entendido' : 'Cancelar' }}
                        </button>
                    </div>

                    <!-- Informação adicional para usuários com assinatura -->
                    @if(Auth::user()->hasActiveSubscription() || Auth::user()->isOnGracePeriod())
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400">
                                Precisa de ajuda? 
                                <a href="{{ route('help.center') }}" class="text-cyan-400 hover:text-cyan-300 underline">
                                    Entre em contato com o suporte
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Função para resetar o formulário de perfil
    function resetFormDeleteAccount() {
        document.querySelector('#delete_password').value = "";
    }
</script>