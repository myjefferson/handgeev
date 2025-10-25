@extends('template.template-site')

@section('title', 'Login')
@section('description', 'Entre na sua conta')

@section('content_site')
    <div class="flex flex-col min-h-screen">
        <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class=" w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <img class="w-52" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                    </div>
                    <p class="text-slate-300">{{ __('login.login.title') }}</p>
                </div>

                {{-- Mensagens --}}
                @include('components.alerts.alert')

                <form class="space-y-6" action="{{ route('login.auth') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('login.login.email') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-500"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                value="{{ old('email') }}"
                                class="input-field appearance-none relative block bg-slate-700/50 w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                                placeholder="{{ __('login.login.email_placeholder') }}">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            {{ __('login.login.password') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="input-field appearance-none relative block bg-slate-700/50 w-full pl-10 pr-10 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                                placeholder="{{ __('login.login.password_placeholder') }}">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-200">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-slate-600 rounded bg-slate-700">
                            <label for="remember-me" class="ml-2 block text-sm text-slate-400">
                                {{ __('login.login.remember_me') }}
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('recovery.account.show') }}" class="font-medium text-primary-500 hover:text-primary-400 transition-colors">
                                {{ __('login.login.forgot_password') }}
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-md">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            {{ __('login.login.submit_button') }}
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-400">
                        {{ __('login.login.no_account') }}
                        <a href="{{ route('register.show') }}" class="font-medium text-primary-500 hover:text-primary-400 transition-colors ml-1">
                            {{ __('login.login.signup_link') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer.footer')
@endsection

@push('scripts_end')
    <script>
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
@endpush

@push('style')
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
            border-radius: 16px;
            overflow: hidden;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-field:focus {
            border-color: #08fff0;
            box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
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
@endpush