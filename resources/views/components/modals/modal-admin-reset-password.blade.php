<div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-md border border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Resetar Senha</h3>
            <button onclick="closeResetPasswordModal()" class="text-slate-400 hover:text-slate-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-slate-300">Uma nova senha será gerada e exibida aqui. Deseja continuar?</p>
        </div>
        
        <div class="bg-slate-900 rounded-lg p-4 mb-4 hidden" id="newPasswordResult">
            <p class="text-slate-300">Nova senha: <span id="newPassword" class="font-mono text-teal-400"></span></p>
            <p class="text-sm text-slate-400 mt-2">Salve esta senha em um local seguro!</p>
        </div>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeResetPasswordModal()" class="px-4 py-2 bg-slate-700 text-slate-300 rounded-lg hover:bg-slate-600 transition-colors">
                Cancelar
            </button>
            <button onclick="confirmResetPassword()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition-colors">
                Resetar Senha
            </button>
        </div>
    </div>
</div>

<script>
let resetPasswordUserId = null;

function openResetPasswordModal(userId) {
    resetPasswordUserId = userId;
    document.getElementById('resetPasswordModal').classList.remove('hidden');
    document.getElementById('newPasswordResult').classList.add('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    resetPasswordUserId = null;
}

function confirmResetPassword() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/users/${resetPasswordUserId}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newPassword').textContent = data.new_password;
            document.getElementById('newPasswordResult').classList.remove('hidden');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao resetar senha');
    });
}
</script>