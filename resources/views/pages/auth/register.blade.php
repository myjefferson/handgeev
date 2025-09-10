<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Handgeev</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }
            
            .floating {
                animation: float 6s ease-in-out infinite;
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            }
            
            .teal-glow {
                box-shadow: 0 0 15px rgba(8, 255, 240, 0.3);
            }
            
            .teal-glow:hover {
                box-shadow: 0 0 20px rgba(8, 255, 240, 0.5);
            }
            
            .input-focus:focus {
                border-color: #08fff0;
                box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
            }
        </style>

        @vite('resources/css/app.css')
    </head>
    <body class="font-sans antialiased gradient-bg text-white">
        <div>
            <section>
                <div class="w-full grid grid-cols-1 md:grid-cols-[auto_400px] items-start mx-auto md:h-screen lg:py-0 text-white">
                    <!-- Lado esquerdo - Apresentação visual -->
                    <div class="p-8 hidden md:flex h-full flex-col justify-between gradient-bg relative overflow-hidden">
                        <!-- Elementos decorativos de fundo -->
                        <div class="absolute top-0 left-0 w-full h-full opacity-10">
                            <div class="absolute top-20 left-20 w-72 h-72 rounded-full bg-teal-400 filter blur-3xl"></div>
                            <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-purple-500 filter blur-3xl"></div>
                        </div>
                        
                        <div class="relative z-10">
                            <img class="mb-5 w-48" src="assets/images/logo.png" alt="Handgeev">
                        </div>
                        
                        <div class="relative z-10">
                            <div class="text-4xl font-bold mb-4 leading-tight">
                                Apresente-se como <span class="text-teal-400">quiser</span>.
                            </div>
                            <div class="text-3xl font-semibold text-gray-300 mb-8">
                                Mostre <span class="text-teal-400">porque veio</span>.
                            </div>
                            
                            <!-- Recursos destacados -->
                            <div class="space-y-4 mt-10">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-teal-400/10 flex items-center justify-center mr-4">
                                        <i class="fas fa-palette text-teal-400"></i>
                                    </div>
                                    <span>Designs personalizáveis</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-teal-400/10 flex items-center justify-center mr-4">
                                        <i class="fas fa-bolt text-teal-400"></i>
                                    </div>
                                    <span>Interface intuitiva e rápida</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-teal-400/10 flex items-center justify-center mr-4">
                                        <i class="fas fa-shield-alt text-teal-400"></i>
                                    </div>
                                    <span>Privacidade e segurança</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <div class="flex floating">
                                <div class="w-12 h-12 rounded-full bg-teal-400 flex items-center justify-center mr-3 teal-glow">
                                    <i class="fas fa-quote-left text-slate-900"></i>
                                </div>
                                <div class="flex-1 bg-slate-800/50 backdrop-blur-sm p-4 rounded-lg border border-slate-700">
                                    <p class="text-sm italic text-gray-300">"O Handgeev transformou completamente como apresento meu trabalho aos clientes."</p>
                                    <p class="text-xs mt-2 text-teal-400">— Ana Silva, Designer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lado direito - Formulário de cadastro -->
                    <div class="w-full h-dvh relative bg-slate-800 md:border-l-2 border-teal-400 md:mt-0 sm:max-w-md xl:p-0">
                        <div class="px-6 py-8 h-full flex items-center space-y-4 md:space-y-6">
                            <div class="w-full">
                                <div class="flex md:hidden w-full justify-center mb-8">
                                    <img class="w-48" src="assets/images/logo.png" alt="Handgeev">
                                </div>
                                
                                <div class="mb-2 flex items-center justify-center md:justify-start">
                                    <div class="w-10 h-10 rounded-full bg-teal-400/20 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-plus text-teal-400"></i>
                                    </div>
                                    <h1 class="text-2xl font-bold">
                                        Criar conta
                                    </h1>
                                </div>
                                
                                <p class="text-sm text-gray-400 mb-6 text-center md:text-left">
                                    Preencha os dados abaixo para começar sua jornada
                                </p>
                                
                                <p class="text-sm mt-2 mb-6 text-center md:text-left">
                                    Já tem uma conta? <a href="{{route('login.index')}}" class="underline text-teal-400 hover:text-teal-300 transition-colors">Fazer login</a>.
                                </p>
                                
                                <form class="space-y-4" action="{{route('register.store')}}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="name" class="block mb-2 text-sm font-medium text-gray-300">Nome</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <i class="fas fa-user text-gray-500"></i>
                                                </div>
                                                <input type="text" name="name" id="name" class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" placeholder="Ex: Maria" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="surname" class="block mb-2 text-sm font-medium text-gray-300">Sobrenome</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <i class="fas fa-users text-gray-500"></i>
                                                </div>
                                                <input type="text" name="surname" id="surname" class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" placeholder="Ex: Carvalho" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="email" class="block mb-2 text-sm font-medium text-gray-300">Email</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <i class="fas fa-envelope text-gray-500"></i>
                                            </div>
                                            <input type="email" name="email" id="email" class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" placeholder="Digite seu melhor e-mail" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="password" class="block mb-2 text-sm font-medium text-gray-300">Senha</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <i class="fas fa-lock text-gray-500"></i>
                                            </div>
                                            <input type="password" name="password" id="password" placeholder="••••••••" class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" required>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400">Use pelo menos 8 caracteres com letras e números</p>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="flex items-center h-5">
                                            <input id="terms" name="terms" type="checkbox" class="input-focus w-4 h-4 border border-slate-600 rounded bg-slate-700 focus:ring-3 focus:ring-teal-500 focus:ring-offset-slate-800" required>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="terms" class="text-gray-300">Eu concordo com os <a href="#" class="text-teal-400 hover:underline">Termos</a> e <a href="#" class="text-teal-400 hover:underline">Política de Privacidade</a></label>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2">
                                        <button type="submit" class="teal-glow w-full text-slate-900 bg-teal-400 hover:bg-teal-500 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-md px-5 py-3.5 text-center transition-colors">
                                            <i class="fas fa-rocket mr-2"></i> Criar minha conta
                                        </button>
                                        
                                        {{-- <div class="relative my-6">
                                            <div class="absolute inset-0 flex items-center">
                                                <div class="w-full border-t border-slate-600"></div>
                                            </div>
                                            <div class="relative flex justify-center text-sm">
                                                <span class="px-2 bg-slate-800 text-gray-400">Ou continue com</span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" class="text-white bg-slate-700 hover:bg-slate-600 focus:ring-4 focus:outline-none focus:ring-slate-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center transition-colors">
                                                <i class="fab fa-google mr-2"></i> Google
                                            </button>
                                            <button type="button" class="text-white bg-slate-700 hover:bg-slate-600 focus:ring-4 focus:outline-none focus:ring-slate-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center transition-colors">
                                                <i class="fab fa-github mr-2"></i> GitHub
                                            </button>
                                        </div> --}}
                                    </div>
                                </form>
                                
                                {{-- <div class="absolute bottom-0 left-0 w-full bg-slate-900/80 backdrop-blur-sm text-center text-gray-400 py-3">
                                    <p class="m-0 text-sm font-medium">Desenvolvido com <span class="text-teal-400">💚</span> por Jefferson Carvalho</p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <script>
            // Validação simples de senha
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('password');
                const form = document.querySelector('form');
                
                form.addEventListener('submit', function(e) {
                    if (passwordInput.value.length < 8) {
                        e.preventDefault();
                        alert('A senha deve ter pelo menos 8 caracteres.');
                        passwordInput.focus();
                    }
                });
                
                // Efeito de foco nos inputs
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('ring-1', 'ring-teal-400', 'rounded-lg');
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.classList.remove('ring-1', 'ring-teal-400', 'rounded-lg');
                    });
                });
            });
        </script>
    </body>
</html>