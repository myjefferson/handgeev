<div class="hidden p-6 rounded-lg bg-slate-800/50 border border-slate-700" id="permissions-tab" role="tabpanel">
    <div class="flex items-center mb-6">
        <h3 class="text-xl font-semibold text-white">🔐 Permissões de Métodos HTTP</h3>
    </div>

    <div class="space-y-6">
        <!-- Workspace Permissions -->
        <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
            <h4 class="text-cyan-400 text-lg font-semibold mb-4">📁 Workspace</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="workspacePermissions">
                <!-- Gerado via JavaScript -->
            </div>
        </div>

        <!-- Topics Permissions -->
        <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
            <h4 class="text-cyan-400 text-lg font-semibold mb-4">🗂️ Tópicos</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="topicsPermissions">
                <!-- Gerado via JavaScript -->
            </div>
        </div>

        <!-- Fields Permissions -->
        <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
            <h4 class="text-cyan-400 text-lg font-semibold mb-4">🔤 Campos</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3" id="fieldsPermissions">
                <!-- Gerado via JavaScript -->
            </div>
        </div>

        @if(auth()->user()->isFree() || auth()->user()->isStart())
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h5 class="text-amber-400 font-semibold">Configuração Avançada de Permissões</h5>
                    <p class="text-amber-300 text-sm mt-1">
                        A configuração granular de métodos HTTP está disponível apenas para planos Pro e Premium.
                        <a href="{{ route('subscription.pricing') }}" class="underline hover:text-amber-200">Faça upgrade para desbloquear este recurso.</a>
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script type="module">
    import '/js/modules/alert.js'

    document.addEventListener('DOMContentLoaded', function() {
        loadPermissions();
    });

    // Sistema de Permissões
    async function loadPermissions() {
        try {
            await $.ajax({
                url: "{{ route( 'api.get.permissions', $workspace ) }}",
                method: 'GET',
                success: function(res){
                    currentPermissions = res.permissions;
                    renderPermissions();
                }
            })
        } catch (error) {
            console.error('Erro ao carregar permissões:', error);
            // Usar permissões padrão baseadas no plano
            currentPermissions = {
                workspace: ['GET'],
                topics: ['GET'],
                fields: ['GET']
            };
            renderPermissions();
        }
    }

    async function updatePermission(endpoint, method, isAllowed) {
        if (!['pro', 'premium', 'admin'].includes(USER_PLAN.toLowerCase())) {
            alertManager.show('Este recurso está disponível apenas para planos Pro e Premium', 'error');
            return;
        }

        try {
            const currentMethods = [...(currentPermissions[endpoint] || [])];
            let newMethods;

            if (isAllowed) {
                newMethods = [...new Set([...currentMethods, method])];
            } else {
                newMethods = currentMethods.filter(m => m !== method);
            }

            const response = await fetch(`{{ route('api.get.permissions', ['workspace' => $workspace->id]) }}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${workspace_key}`,
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    endpoint: endpoint,
                    methods: newMethods
                })
            });

            if (response.ok) {
                currentPermissions[endpoint] = newMethods;
                alertManager.show('Permissões atualizadas com sucesso!', 'success');
                loadPermissions();
            } else {
                const error = await response.json();
                alertManager.show(error.message || 'Erro ao atualizar permissões', 'error');
                // Recarregar permissões
                loadPermissions();
            }
        } catch (error) {
            console.error('Erro ao atualizar permissão:', error);
            alertManager.show('Erro de conexão', 'error');
            loadPermissions();
        }
    }

    function renderPermissions() {
        renderPermissionSection('workspacePermissions', 'workspace');
        renderPermissionSection('topicsPermissions', 'topics');
        renderPermissionSection('fieldsPermissions', 'fields');
    }

    function renderPermissionSection(containerId, endpoint) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        const allowedMethods = currentPermissions[endpoint] || [];
        const canEdit = ['pro', 'premium', 'admin'].includes(USER_PLAN.toLowerCase());

        container.innerHTML = methods.map(method => {
            const isAllowed = allowedMethods.includes(method);
            const isDisabled = !canEdit;
            
            return `
                <label class="relative flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all ${
                    isAllowed 
                        ? 'bg-cyan-500/10 border-cyan-500/50 text-cyan-400' 
                        : 'bg-slate-800 border-slate-700 text-slate-400'
                } ${isDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:border-cyan-400/50'}"}>
                    <input 
                        type="checkbox" 
                        value="${method}" 
                        ${isAllowed ? 'checked' : ''}
                        ${isDisabled ? 'disabled' : ''}
                        onchange="updatePermission('${endpoint}', '${method}', this.checked)"
                        class="hidden"
                    >
                    <span class="font-mono text-sm font-medium">${method}</span>
                    ${isDisabled ? `
                        <div class="absolute -top-1 -right-1" title="Recurso disponível apenas para planos Pro e Premium">
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    ` : ''}
                </label>
            `;
        }).join('');
    }
</script>