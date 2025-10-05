<!-- Card de Upsell para Pro -->
    <div class="settings-card bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl shadow-sm p-8 border border-purple-200 dark:border-purple-700">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full mb-4">
                <i class="fas fa-users-cog text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Colaboração Profissional
            </h2>
            <p class="text-purple-600 dark:text-purple-300 font-medium">
                Recurso exclusivo para assinantes Pro
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 text-center">
                Trabalhe em equipe com recursos avançados
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Colaboradores Ilimitados</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Convide sua equipe inteira</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Controle de Permissões</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Defina níveis de acesso</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Convites por Email</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Convide com um clique</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-history text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Histórico de Atividades</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Acompanhe todas as mudanças</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">Preço especial por tempo limitado</p>
                        <div class="flex items-baseline justify-center">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">R$ 29</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">/mês</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Economize 40% compared to mensal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('subscription.pricing') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-crown mr-3"></i>
                Upgrade para Pro - Comece Agora
            </a>
            
            <div class="mt-4 flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    Teste grátis de 7 dias
                </span>
                <span class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    Cancelamento instantâneo
                </span>
                <span class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    Suporte prioritário
                </span>
            </div>
        </div>

        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        💡 Dica Profissional
                    </h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        Equipes que usam colaboração Pro economizam em média <strong>12 horas/mês</strong> em gestão de projetos e comunicação.
                    </p>
                </div>
            </div>
        </div>
    </div>

<!-- Card de Depoimentos (opcional) -->
{{-- <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">O que nossos usuários Pro dizem</h3>
    
    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                    MC
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Maria Costa</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Designer Freelancer</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                "O controle de colaboradores me permitiu trabalhar com meus clientes de forma muito mais profissional. Eles adoram poder ver o progresso!"
            </p>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                    JS
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">João Silva</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Startup Founder</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                "Essential para minha equipe de desenvolvimento. Conseguimos gerenciar 5 projetos simultâneos com total controle de acessos."
            </p>
        </div>
    </div>
</div> --}}