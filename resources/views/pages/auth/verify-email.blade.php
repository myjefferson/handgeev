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
    .verify-container {
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        overflow: hidden;
        max-width: 500px;
    }
    .code-input {
        background: rgba(30, 41, 59, 0.6);
        border: 2px solid rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        letter-spacing: 8px;
    }
    .code-input:focus {
        border-color: #08fff0;
        box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
    }
    .btn-primary {
        background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #00e6d8 0%, #008078 100%);
        transform: translateY(-2px);
    }
</style>

    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="verify-container w-full p-8 border border-teal-500">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img class="w-52" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                </div>
                <div class="text-6xl mb-4">📧</div>
                <h2 class="text-2xl font-bold text-slate-100 mb-2">Verifique seu Email</h2>
                <p class="text-slate-300">Enviamos um código de verificação para:</p>
                <p class="text-teal-400 font-semibold">{{ Auth::user()->email }}</p>
            </div>

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-400 bg-green-900/20 p-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-blue-400 bg-blue-900/20 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('verification.verify') }}" method="POST">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-3 text-center">
                        Digite o código de 6 dígitos:
                    </label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" 
                        class="code-input w-full py-4 px-3 rounded-lg focus:outline-none focus:ring-0"
                        placeholder="000000" required autofocus>
                    @error('code')
                        <p class="mt-2 text-sm text-red-400 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3 px-4 rounded-lg text-slate-900 font-medium text-lg">
                    <i class="fas fa-check-circle mr-2"></i> Verificar Código
                </button>
            </form>

            <div class="mt-6 space-y-4">
                <div class="text-center">
                    <form action="{{ route('verification.resend') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-teal-400 hover:text-teal-300 text-sm">
                            <i class="fas fa-redo mr-1"></i> Reenviar código
                        </button>
                    </form>
                    <span class="text-slate-500 mx-2">•</span>
                    <button onclick="showEmailModal()" class="text-slate-400 hover:text-slate-300 text-sm">
                        <i class="fas fa-edit mr-1"></i> Alterar email
                    </button>
                </div>

                <div class="text-center pt-4 border-t border-slate-700">
                    <form action="{{ route('logout') }}" method="GET" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-slate-400 text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sair
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500">
                <p>O código expira em 30 minutos</p>
            </div>
        </div>
    </div>

    <!-- Modal para alterar email -->
    <div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="verify-container p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Alterar Email</h3>
            <form action="{{ route('verification.update-email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-slate-300 mb-2">Novo email:</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" 
                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-teal-400" required>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideEmailModal()" class="flex-1 bg-slate-600 text-white py-2 rounded-lg hover:bg-slate-500">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-teal-500 text-slate-900 py-2 rounded-lg hover:bg-teal-400 font-semibold">
                        Alterar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function showEmailModal() {
        document.getElementById('emailModal').classList.remove('hidden');
    }

    function hideEmailModal() {
        document.getElementById('emailModal').classList.add('hidden');
    }

    // Auto-focus e auto-avanço para o código
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.querySelector('input[name="code"]');
        
        codeInput.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
        
        // Permitir apenas números
        codeInput.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
    </script>

    @include('components.footer.footer_login')
@endsection