<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portfoline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6fffe',
                            100: '#b3fffc',
                            200: '#80fffa',
                            300: '#4dfff8',
                            400: '#1afff6',
                            500: '#08fff0',
                            600: '#00e6d8',
                            700: '#00b3a8',
                            800: '#008078',
                            900: '#004d48',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .login-container {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(8, 255, 240, 0.15);
            box-shadow: 0 15px 35px rgba(8, 255, 240, 0.15);
            border-radius: 16px;
            overflow: hidden;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
        }
        .input-field {
            transition: all 0.3s ease;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-field:focus {
            border-color: #08fff0;
            box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
            background: rgba(30, 41, 59, 0.8);
        }
        .btn-primary {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #00e6d8 0%, #008078 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(8, 255, 240, 0.3);
        }
        .footer {
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(8, 255, 240, 0.1);
            backdrop-filter: blur(10px);
        }
        .social-icon {
            transition: all 0.3s ease;
        }
        .social-icon:hover {
            color: #08fff0;
            transform: translateY(-3px);
        }
        .logo-text {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="login-container w-full max-w-md p-8 border border-cyan-400">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img class="w-52" src="assets/images/logo.png" alt="Portfoline Logo">
                </div>
                <p class="text-slate-300">Faça login para acessar sua conta</p>
            </div>

            <form class="space-y-6" action="{{ route('login.auth') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">E-mail</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-500"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="input-field appearance-none relative block w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-500"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="input-field appearance-none relative block w-full pl-10 pr-10 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="Sua senha">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-400">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-slate-600 rounded bg-slate-700">
                        <label for="remember-me" class="ml-2 block text-sm text-slate-400">
                            Lembrar-me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-primary-500 hover:text-primary-400 transition-colors">
                            Esqueceu a senha?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn-primary group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-md">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        Entrar
                    </button>
                </div>
            </form>

            {{-- <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-slate-800 text-slate-400">
                            Ou continue com
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-slate-700 rounded-md shadow-sm bg-slate-800 text-sm font-medium text-slate-300 hover:bg-slate-700 transition-colors">
                        <i class="fab fa-google text-red-400 mr-2"></i>
                        Google
                    </a>
                    <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-slate-700 rounded-md shadow-sm bg-slate-800 text-sm font-medium text-slate-300 hover:bg-slate-700 transition-colors">
                        <i class="fab fa-github text-slate-400 mr-2"></i>
                        GitHub
                    </a>
                </div>
            </div> --}}

            <div class="mt-6 text-center">
                <p class="text-sm text-slate-400">
                    Não tem uma conta?
                    <a href="{{ route('register.index') }}" class="font-medium text-primary-500 hover:text-primary-400 transition-colors ml-1">
                        Cadastre-se
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="footer py-6 px-4 mt-auto">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <a href="{{ route('landing.portfoline' )}}">
                    <img class="w-28 opacity-60 grayscale hover:grayscale-0 transition-all" src="assets/images/logo.png" alt="Portfoline Logo">
                    </a>
                </div>
                
                <div class="flex space-x-6 mb-4 md:mb-0">
                    <a href="#" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">Termos de Uso</a>
                    <a href="#" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">Privacidade</a>
                    <a href="#" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">Suporte</a>
                </div>
                
                <div class="flex space-x-4">
                    <a href="#" class="social-icon text-slate-400 text-lg">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon text-slate-400 text-lg">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon text-slate-400 text-lg">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon text-slate-400 text-lg">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-slate-700 mt-4 pt-4 text-center">
                <p class="text-xs text-slate-500">
                    &copy; 2023 Portfoline. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Função para alternar a visibilidade da senha
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>