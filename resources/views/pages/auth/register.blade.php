@extends('template.template-site')

@section('title', __('register.title'))
@section('description', 'Boas-vindas ao HandGeev')

@section('content_site')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const planParam = urlParams.get('plan');
            
            if (planParam && planParam !== 'free') {
                showPlanSelection(planParam);
            }
        });

        function showPlanSelection(planName) {
            const formattedPlanName = planName.charAt(0).toUpperCase() + planName.slice(1);
            const planInfoDiv = document.getElementById('plan-selection-info');
            
            // Conteúdo HTML para o plano selecionado
            const planContent = `
                <div class="mb-6 p-4 bg-gradient-to-r from-teal-500/10 to-purple-500/10 border border-teal-400/30 rounded-xl">
                    <div class="flex items-center space-x-3">
                        <div>
                            <h3 class="text-teal-400 font-semibold">@lang('register.plan.selected', ['plan' => '${formattedPlanName}'])</h3>
                            <p class="text-teal-300 text-sm mt-1">
                                @lang('register.plan.payment_redirect')
                            </p>
                        </div>
                    </div>
                </div>
            `;
            
            // Inserir o conteúdo e mostrar a div
            planInfoDiv.innerHTML = planContent;
            planInfoDiv.classList.remove('hidden');
            
            // Atualizar o botão de submit
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<i class="fas fa-rocket mr-2"></i> @lang("register.form.submit_button_payment")';
                submitButton.classList.add('bg-gradient-to-r', 'from-teal-500', 'to-teal-600', 'hover:from-teal-600', 'hover:to-teal-700');
            }
            
            // Atualizar o título principal
            const mainTitle = document.querySelector('.text-2xl.font-bold');
            if (mainTitle) {
                mainTitle.innerHTML = `@lang('register.plan.create_account', ['plan' => '<span class="text-teal-400">${formattedPlanName}</span>'])`;
            }
        }
    </script>
    <div class="font-sans antialiased gradient-bg text-white">
        <section>
            <div class="w-full grid grid-cols-1 md:grid-cols-[auto_400px] items-start mx-auto h-min lg:py-0 text-white">
                <!-- Lado esquerdo - Apresentação visual -->
                <div class="p-8 hidden md:flex h-full flex-col justify-between gradient-bg relative overflow-hidden">
                    <!-- Elementos decorativos de fundo -->
                    <div class="absolute top-0 left-0 w-full h-full opacity-10">
                        <div class="absolute top-20 left-20 w-72 h-72 rounded-full bg-teal-400 filter blur-3xl"></div>
                        <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-purple-500 filter blur-3xl"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <img class="mb-5 w-48" src="{{asset('assets/images/logo.png')}}" alt="Handgeev">
                        <p class="text-teal-400 font-semibold text-lg">@lang('register.hero.platform_description')</p>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="text-4xl font-bold mb-4 leading-tight">
                            @lang('register.hero.title_line1', ['highlight' => '<span class="text-teal-400">'.__('register.hero.highlight').'</span>'])
                        </div>
                        <div class="text-xl text-gray-300 mb-8">
                            @lang('register.hero.title_line2')
                        </div>
                        
                        <!-- Benefícios do HandGeev -->
                        <div class="space-y-4 mt-6">
                            <div class="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center mr-4">
                                    <i class="fas fa-bolt text-green-400"></i>
                                </div>
                                <div>
                                    <div class="text-white font-semibold">@lang('register.features.instant_setup')</div>
                                    <div class="text-gray-400 text-sm">@lang('register.features.instant_setup_desc')</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mr-4">
                                    <i class="fas fa-sliders-h text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="text-white font-semibold">@lang('register.features.total_flexibility')</div>
                                    <div class="text-gray-400 text-sm">@lang('register.features.total_flexibility_desc')</div>
                                </div>
                            </div>
                            
                            <div class="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center mr-4">
                                    <i class="fas fa-shield-alt text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-white font-semibold">@lang('register.features.robust_security')</div>
                                    <div class="text-gray-400 text-sm">@lang('register.features.robust_security_desc')</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 rounded-full bg-teal-400 flex items-center justify-center teal-glow flex-shrink-0">
                                <i class="fas fa-quote-left text-slate-900"></i>
                            </div>
                            <div class="flex-1 bg-slate-800/50 backdrop-blur-sm p-4 rounded-lg border border-slate-700">
                                <p class="text-sm italic text-gray-300">
                                    @lang('register.hero.testimonial')
                                </p>
                                <p class="text-xs mt-2 text-teal-400">@lang('register.hero.testimonial_author')</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lado direito - Formulário de cadastro -->
                <div class="flex items-center w-full min-h-svh relative bg-slate-800 md:border-l-2 border-teal-400 md:mt-0 sm:max-w-md xl:p-0">
                    <div class="px-6 py-8 h-full w-full flex items-center  space-y-4 md:space-y-6">
                        <div class="w-full">
                            <div class="flex md:hidden w-full justify-center mb-8">
                                <img class="mt-4 mb-3 w-48" src="{{asset('assets/images/logo.png')}}" alt="Handgeev">
                            </div>
                            
                            <div class="mb-2 flex items-center justify-center md:justify-start">
                                <div class="w-10 h-10 rounded-full bg-teal-400/20 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-plus text-teal-400"></i>
                                </div>
                                <h1 class="text-2xl font-bold">
                                    {{ __('register.title') }}
                                </h1>
                            </div>
                            
                            <p class="text-sm text-gray-400 mb-6 text-center md:text-left">
                                {{ __('register.subtitle') }}
                            </p>
                            
                            <p class="text-sm mt-2 mb-6 text-center md:text-left">
                                {{ __('register.already_account') }} 
                                <a href="{{route('login.show')}}" class="underline text-teal-400 hover:text-teal-300 transition-colors">
                                    {{ __('register.login_link') }}
                                </a>.
                            </p>

                            <!-- Mensagens de Sucesso -->
                            @if(session('success'))
                                <div class="mb-4 p-3 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <!-- Mensagens de Erro -->
                            @if($errors->any())
                                <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div id="plan-selection-info" class="hidden"></div>
                            
                            <form class="space-y-4" action="{{route('register.store')}}" method="POST">
                                @csrf 
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-300">
                                            {{ __('register.form.name') }}
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                                class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                placeholder="{{ __('register.form.name_placeholder') }}" required>
                                        </div>
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="surname" class="block mb-2 text-sm font-medium text-gray-300">
                                            {{ __('register.form.surname') }}
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <i class="fas fa-users text-gray-500"></i>
                                            </div>
                                            <input type="text" name="surname" id="surname" value="{{ old('surname') }}"
                                                class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                placeholder="{{ __('register.form.surname_placeholder') }}" required>
                                        </div>
                                        @error('surname')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="email" class="block mb-2 text-sm font-medium text-gray-300">
                                        {{ __('register.form.email') }}
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fas fa-envelope text-gray-500"></i>
                                        </div>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                            class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                            placeholder="{{ __('register.form.email_placeholder') }}" required>
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password" class="block mb-2 text-sm font-medium text-gray-300">
                                        {{ __('register.form.password') }}
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fas fa-lock text-gray-500"></i>
                                        </div>
                                        <input type="password" name="password" id="password" 
                                            placeholder="{{ __('register.form.password_placeholder') }}" 
                                            class="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">{{ __('register.form.password_hint') }}</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="flex items-center h-5">
                                        <input id="terms" name="terms" type="checkbox" 
                                            class="input-focus w-4 h-4 border border-slate-600 rounded bg-slate-700 focus:ring-3 focus:ring-teal-500 focus:ring-offset-slate-800" required>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="text-gray-300">
                                            {!! __('register.form.terms', [
                                                'terms' => '<a href="'.route('legal.terms').'" class="text-teal-400 hover:underline">'.__('register.form.terms_link').'</a>',
                                                'privacy' => '<a href="'.route('legal.privacy').'" class="text-teal-400 hover:underline">'.__('register.form.privacy_link').'</a>'
                                            ]) !!}
                                        </label>
                                    </div>
                                </div>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <div class="pt-2">
                                    <button type="submit" class="teal-glow w-full text-slate-900 bg-teal-400 hover:bg-teal-500 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-md px-5 py-3.5 text-center transition-colors">
                                        <i class="fas fa-rocket mr-2"></i> {{ __('register.form.submit_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @include('components.footer.footer')
    </div>
@endsection

@push('style')
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

        /* Animação suave para a div do plano */
        #plan-selection-info {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('script')
    <script>
        // Validação simples de senha
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                if (passwordInput.value.length < 8) {
                    e.preventDefault();
                    alert('{{ __("register.javascript.password_validation") }}');
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
@endpush