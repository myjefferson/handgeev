@extends('template.template-site')

@section('title', $workspace->title)
@section('description', 'Workspace compartilhado por '.$user->name)

@section('content_site')
<div class="bg-slate-900 dark:bg-gray-900 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-teal-100 dark:bg-teal-900">
                <i class="fas fa-lock text-teal-600 dark:text-teal-300"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Workspace Protegido
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Este workspace está protegido por senha
            </p>
            <p class="mt-1 text-center text-sm text-gray-500 dark:text-gray-500">
                {{ $workspace->title }}
            </p>
            <p class="mt-1 text-center text-xs text-gray-400 dark:text-gray-600">
                Compartilhado por: {{ $user->name }}
            </p>
        </div>
        
        <form id="passwordForm" class="mt-8 space-y-6">
            @csrf
            <div>
                <label for="password" class="sr-only">Senha</label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    class="relative block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-teal-500 focus:border-teal-500 focus:z-10 sm:text-sm bg-white dark:bg-gray-700" 
                    placeholder="Digite a senha do workspace"
                >
                <div id="password-error" class="mt-2 text-sm text-red-600 hidden"></div>
            </div>

            <div>
                <button 
                    type="submit" 
                    id="submit-btn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-200"
                >
                    <span id="btn-text">Acessar Workspace</span>
                    <span id="btn-spinner" class="hidden ml-2">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Precisa de acesso? 
                <button 
                    onclick="requestAccess()" 
                    class="font-medium text-teal-600 hover:text-teal-500 dark:text-teal-400 dark:hover:text-teal-300"
                >
                    Solicite ao proprietário
                </button>
            </p>
        </div>
    </div>
</div>

@include('components.footer.footer')

<script>
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const passwordError = document.getElementById('password-error');
        
        // Reset states
        passwordError.classList.add('hidden');
        submitBtn.disabled = true;
        btnText.textContent = 'Verificando...';
        btnSpinner.classList.remove('hidden');
        
        const formData = new FormData(this);
        
        fetch("{{ route('workspace.shared.verify-password', ['global_key_api' => $globalHash, 'workspace_key_api' => $workspaceHash]) }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                passwordError.textContent = data.message;
                passwordError.classList.remove('hidden');
                submitBtn.disabled = false;
                btnText.textContent = 'Acessar Workspace';
                btnSpinner.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            passwordError.textContent = 'Erro ao verificar senha. Tente novamente.';
            passwordError.classList.remove('hidden');
            submitBtn.disabled = false;
            btnText.textContent = 'Acessar Workspace';
            btnSpinner.classList.add('hidden');
        });
    });

    function requestAccess() {
        // Aqui você pode implementar a solicitação de acesso
        alert('Entre em contato com o proprietário do workspace para solicitar acesso.');
    }
    </script>
@endsection