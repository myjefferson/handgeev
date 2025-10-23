@extends('template.template-site')

@section('title', 'Erro na Confirmação')
@section('description', 'Erro na Confirmação')

@section('content_site')
    <div class="flex min-h-screen max-h-max items-center justify-center py-20">
        <div class="max-w-md w-full">
            <!-- Card de erro -->
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 text-center">
                <!-- Ícone de erro -->
                <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>

                <!-- Título -->
                <h1 class="text-2xl font-bold text-white mb-4">
                    Link Inválido ou Expirado
                </h1>

                <!-- Mensagem -->
                <p class="text-slate-300 mb-6">
                    {{ $message ?? 'Este link de confirmação é inválido ou expirou.' }}
                </p>

                <!-- Informação adicional -->
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-2 text-sm">
                        <i class="fas fa-lightbulb text-amber-400 mt-0.5"></i>
                        <p class="text-amber-300 text-left">
                            <strong>Solução:</strong> Solicite um novo link de confirmação na página do seu perfil.
                        </p>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="space-y-3">
                    <a href="{{ route('user.profile') }}" 
                    class="w-full bg-teal-600 hover:bg-teal-500 text-white py-3 px-4 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-user-circle"></i>
                        <span>Ir para o Perfil</span>
                    </a>
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