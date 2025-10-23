@extends('template.template-site')

@section('title', 'Email Alterado com Sucesso')
@section('description', 'Email Alterado com Sucesso')

@section('content_site')
    <div class="flex min-h-screen max-h-max items-center justify-center py-20">
        <div class="max-w-md w-full">
            <!-- Card de sucesso -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 text-center">
                <!-- Ícone de sucesso -->
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-green-400 text-3xl"></i>
                </div>

                <!-- Título -->
                <h1 class="text-2xl font-bold text-white mb-4">
                    Email Alterado com Sucesso!
                </h1>

                <!-- Mensagem -->
                <p class="text-slate-300 mb-2">
                    Seu email foi alterado com sucesso.
                </p>
                <p class="text-slate-300 mb-6">
                    Agora você pode fazer login usando seu novo email.
                </p>

                <!-- Informações do email -->
                <div class="bg-slate-700/50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-400">Novo email:</span>
                        <span class="text-green-400 font-medium">{{ $newEmail ?? 'Seu novo email' }}</span>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="space-y-3">
                    <a href="{{ route('user.profile') }}" 
                    class="w-full bg-teal-600 hover:bg-teal-500 text-white py-3 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-user-circle"></i>
                        <span>Voltar para o Perfil</span>
                    </a>
                    
                    <a href="{{ route('dashboard.home') }}" 
                    class="w-full bg-slate-700 hover:bg-slate-600 text-slate-300 py-3 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-home"></i>
                        <span>Ir para o Dashboard</span>
                    </a>
                </div>

                <!-- Informação adicional -->
                <div class="mt-6 p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                    <div class="flex items-start space-x-2 text-sm">
                        <i class="fas fa-info-circle text-blue-400 mt-0.5"></i>
                        <p class="text-blue-300 text-left">
                            <strong>Dica:</strong> Recomendamos que você faça logout e login novamente para garantir que todas as sessões estejam atualizadas.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-slate-500 text-sm">
                    © {{ date('Y') }} Handgeev. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>

    @include('components.footer.footer')
@endsection