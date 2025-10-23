<div id="email-change-modal" tabindex="-1" aria-hidden="true" 
     class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full mx-auto">
        <!-- Modal content -->
        <div class="relative bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-700">
                <h3 class="text-xl font-semibold text-white">
                    <i class="fas fa-envelope mr-2 text-teal-400"></i>
                    Alterar E-mail
                </h3>
                <button type="button" 
                        class="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                        data-modal-hide="email-change-modal">
                    <i class="fas fa-times"></i>
                    <span class="sr-only">Fechar modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <!-- Aviso se email não está confirmado -->
                @unless(Auth::user()->email_verified_at)
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                        <div class="flex items-center text-amber-300 text-sm">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>Seu email atual não está confirmado. Recomendamos confirmá-lo antes de alterar.</span>
                        </div>
                    </div>
                @endunless
                
                <p class="text-slate-300 text-sm">
                    Para alterar seu e-mail, digite o novo endereço abaixo. Enviaremos um link de confirmação para o novo email.
                </p>
                
                <form id="email-change-form" action="{{ route('email.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="new_email" class="block text-sm font-medium text-gray-400 mb-2">
                            Novo E-mail
                        </label>
                        <input type="email" 
                               id="new_email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full bg-slate-700 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                               placeholder="seu.novo.email@exemplo.com"
                               required>
                        @error('email')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-400 mb-2">
                            Senha Atual
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password"
                               class="w-full bg-slate-700 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                               placeholder="Digite sua senha atual"
                               required>
                        @error('current_password')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
            
            <!-- Modal footer -->
            <div class="flex items-center justify-end p-6 space-x-3 border-t border-slate-700">
                <button type="button" 
                        data-modal-hide="email-change-modal"
                        class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-700 hover:bg-slate-600 rounded-xl transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                        form="email-change-form"
                        class="px-4 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-500 rounded-xl transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Enviar Link</span>
                </button>
            </div>
        </div>
    </div>
</div>