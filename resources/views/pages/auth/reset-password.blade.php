@extends('template.template-site')

@section('content_site')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .reset-container {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
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
    </style>

    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="reset-container w-full max-w-md p-8 border border-teal-500">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img class="w-52" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                </div>
                <h2 class="text-2xl font-bold text-slate-100 mb-2">Redefinir Senha</h2>
                <p class="text-slate-300">Crie uma nova senha para sua conta</p>
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-400 bg-green-900/20 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('recovery.password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">E-mail</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-500"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="input-field appearance-none relative block w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="seu@email.com" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Nova Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-500"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                            class="input-field appearance-none relative block w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="Mínimo 8 caracteres">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirmar Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-500"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                            class="input-field appearance-none relative block w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="Digite novamente a senha">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn-primary group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-md">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                        Redefinir Senha
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login.show') }}" class="inline-flex items-center text-sm font-medium text-primary-500 hover:text-primary-400 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar para o login
                </a>
            </div>
        </div>
    </div>

    @include('components.footer.footer_login')
@endsection