@extends('template.template-site')

@section('content_site')
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img class="w-52" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                </div>
                <h2 class="text-2xl font-bold text-slate-100 mb-2">{{ __('reset_password.title') }}</h2>
                <p class="text-slate-300">{{ __('reset_password.subtitle') }}</p>
            </div>

            <!-- Card do Formulário -->
            <div class="bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-500/30 rounded-lg flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span class="text-green-300 text-sm">{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-500/30 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                            <span class="text-red-300 font-medium">{{ __('reset_password.error') }}</span>
                        </div>
                        <ul class="text-red-300 text-sm list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="resetPasswordForm" method="POST" action="{{ route('recovery.password.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-teal-400"></i>{{ __('reset_password.email') }}
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                readonly
                                value="{{ $email ?? old('email') }}"
                                class="bg-slate-700/50 border border-slate-600 text-slate-200 text-sm rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 block w-full p-3.5 placeholder-slate-400 cursor-not-allowed"
                                placeholder="{{ __('reset_password.email_placeholder') }}">
                        </div>
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-lock mr-2 text-teal-400"></i>{{ __('reset_password.new_password') }}
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="new-password" 
                                required
                                class="bg-slate-700/50 border border-slate-600 text-slate-200 text-sm rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 block w-full p-3.5 pr-10 placeholder-slate-400 transition-colors"
                                placeholder="{{ __('reset_password.password_placeholder') }}"
                                onkeyup="checkPasswordStrength()">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <!-- Indicador de Força da Senha -->
                        <div class="mt-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-slate-400">{{ __('reset_password.password_strength') }}:</span>
                                <span class="text-xs font-medium" id="passwordStrengthText">{{ __('reset_password.type_password') }}</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div id="passwordStrength" class="h-2 rounded-full transition-all duration-300"></div>
                            </div>
                        </div>

                        <!-- Requisitos da Senha -->
                        <div class="mt-3 space-y-2 text-xs text-slate-400">
                            <div class="flex items-center" id="reqLength">
                                <i class="fas fa-circle mr-2 text-slate-600"></i>
                                <span>{{ __('reset_password.requirements.length') }}</span>
                            </div>
                            <div class="flex items-center" id="reqUpper">
                                <i class="fas fa-circle mr-2 text-slate-600"></i>
                                <span>{{ __('reset_password.requirements.uppercase') }}</span>
                            </div>
                            <div class="flex items-center" id="reqLower">
                                <i class="fas fa-circle mr-2 text-slate-600"></i>
                                <span>{{ __('reset_password.requirements.lowercase') }}</span>
                            </div>
                            <div class="flex items-center" id="reqNumber">
                                <i class="fas fa-circle mr-2 text-slate-600"></i>
                                <span>{{ __('reset_password.requirements.number') }}</span>
                            </div>
                            <div class="flex items-center" id="reqSpecial">
                                <i class="fas fa-circle mr-2 text-slate-600"></i>
                                <span>{{ __('reset_password.requirements.special') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-lock mr-2 text-teal-400"></i>{{ __('reset_password.confirm_password') }}
                        </label>
                        <div class="relative">
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                autocomplete="new-password" 
                                required
                                class="bg-slate-700/50 border border-slate-600 text-slate-200 text-sm rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 block w-full p-3.5 pr-10 placeholder-slate-400 transition-colors"
                                placeholder="{{ __('reset_password.confirm_placeholder') }}"
                                onkeyup="checkPasswordMatch()">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-xs text-red-400 mt-2 hidden" id="passwordMatchError">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ __('reset_password.password_mismatch') }}
                        </div>
                    </div>

                    <!-- Botão de Submit -->
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 focus:ring-4 focus:ring-teal-500/30 font-medium rounded-lg text-sm px-5 py-3.5 text-center text-white transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-lg">
                        <i class="fas fa-sync-alt mr-2"></i>{{ __('reset_password.reset_button') }}
                    </button>
                </form>

                <!-- Link para Login -->
                <div class="text-center mt-6 pt-6 border-t border-slate-700">
                    <a href="{{ route('login.show') }}" class="inline-flex items-center text-sm font-medium text-teal-400 hover:text-teal-300 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('reset_password.back_to_login') }}
                    </a>
                </div>
            </div>

            <!-- Informações de Segurança -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center text-xs text-slate-400 bg-slate-800/50 backdrop-blur-sm rounded-full px-4 py-2 border border-slate-700">
                    <i class="fas fa-shield-alt mr-2 text-teal-400"></i>
                    {{ __('reset_password.security_message') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        const translations = {
            password_strength: {
                type_password: "{{ __('reset_password.strength_messages.type_password') }}",
                very_weak: "{{ __('reset_password.strength_messages.very_weak') }}",
                weak: "{{ __('reset_password.strength_messages.weak') }}",
                good: "{{ __('reset_password.strength_messages.good') }}",
                strong: "{{ __('reset_password.strength_messages.strong') }}",
                very_strong: "{{ __('reset_password.strength_messages.very_strong') }}"
            }
        };

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.parentNode.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');
            const submitBtn = document.getElementById('submitBtn');
            
            // Reset requirements
            document.querySelectorAll('[id^="req"]').forEach(el => {
                el.querySelector('i').className = 'fas fa-circle mr-2 text-slate-600';
            });

            let strength = 0;

            // Check length
            if (password.length >= 8) {
                strength += 1;
                document.getElementById('reqLength').querySelector('i').className = 'fas fa-check-circle mr-2 text-green-500';
            }

            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength += 1;
                document.getElementById('reqUpper').querySelector('i').className = 'fas fa-check-circle mr-2 text-green-500';
            }

            // Check lowercase
            if (/[a-z]/.test(password)) {
                strength += 1;
                document.getElementById('reqLower').querySelector('i').className = 'fas fa-check-circle mr-2 text-green-500';
            }

            // Check numbers
            if (/[0-9]/.test(password)) {
                strength += 1;
                document.getElementById('reqNumber').querySelector('i').className = 'fas fa-check-circle mr-2 text-green-500';
            }

            // Check special characters
            if (/[@$!%*?&]/.test(password)) {
                strength += 1;
                document.getElementById('reqSpecial').querySelector('i').className = 'fas fa-check-circle mr-2 text-green-500';
            }

            // Update strength bar and text
            let strengthClass = '';
            let strengthMessage = '';
            
            switch(strength) {
                case 0:
                    strengthClass = 'bg-red-500 w-0';
                    strengthMessage = translations.password_strength.type_password;
                    strengthText.className = 'text-xs font-medium text-slate-400';
                    break;
                case 1:
                    strengthClass = 'bg-red-500 w-1/4';
                    strengthMessage = translations.password_strength.very_weak;
                    strengthText.className = 'text-xs font-medium text-red-400';
                    break;
                case 2:
                    strengthClass = 'bg-orange-500 w-1/2';
                    strengthMessage = translations.password_strength.weak;
                    strengthText.className = 'text-xs font-medium text-orange-400';
                    break;
                case 3:
                    strengthClass = 'bg-yellow-500 w-3/4';
                    strengthMessage = translations.password_strength.good;
                    strengthText.className = 'text-xs font-medium text-yellow-400';
                    break;
                case 4:
                    strengthClass = 'bg-blue-500 w-4/4';
                    strengthMessage = translations.password_strength.strong;
                    strengthText.className = 'text-xs font-medium text-blue-400';
                    break;
                case 5:
                    strengthClass = 'bg-green-500 w-4/4';
                    strengthMessage = translations.password_strength.very_strong;
                    strengthText.className = 'text-xs font-medium text-green-400';
                    break;
            }

            strengthBar.className = `h-2 rounded-full transition-all duration-300 ${strengthClass}`;
            strengthText.textContent = strengthMessage;

            checkFormValidity();
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchError = document.getElementById('passwordMatchError');
            const confirmField = document.getElementById('password_confirmation');

            if (confirmPassword && password !== confirmPassword) {
                matchError.classList.remove('hidden');
                confirmField.classList.add('border-red-500');
                confirmField.classList.remove('border-slate-600', 'focus:border-teal-500');
            } else {
                matchError.classList.add('hidden');
                confirmField.classList.remove('border-red-500');
                confirmField.classList.add('border-slate-600', 'focus:border-teal-500');
            }

            checkFormValidity();
        }

        function checkFormValidity() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('submitBtn');

            // Check if all requirements are met
            const requirementsMet = 
                password.length >= 8 &&
                /[A-Z]/.test(password) &&
                /[a-z]/.test(password) &&
                /[0-9]/.test(password) &&
                /[@$!%*?&]/.test(password) &&
                password === confirmPassword;

            submitBtn.disabled = !requirementsMet;
        }

        // Add input event listeners for real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('password_confirmation');
            
            passwordField.addEventListener('input', checkPasswordStrength);
            confirmField.addEventListener('input', checkPasswordMatch);
            
            // Initial check
            checkPasswordStrength();
            checkPasswordMatch();
        });
    </script>

    @include('components.footer.footer')
@endsection