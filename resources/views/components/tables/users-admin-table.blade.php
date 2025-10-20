@forelse ($users as $user)
    <tr class="hover:bg-slate-750 transition-colors user-row" id="user-row-{{ $user->id }}">
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="h-10 w-10 flex-shrink-0 bg-teal-500 rounded-full flex items-center justify-center">
                    <span class="font-medium text-slate-900">
                        {{ substr($user->name, 0, 1) }}{{ substr($user->surname, 0, 1) }}
                    </span>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-white">{{ $user->name }} {{ $user->surname }}</div>
                    <div class="text-sm text-slate-400">{{ $user->email }}</div>
                    <div class="text-xs text-slate-500">
                        Cadastro: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="role-badge 
                @if($user->plan_name == 'admin') bg-blue-100 text-blue-800
                @elseif($user->plan_name == 'premium') bg-purple-100 text-purple-800
                @elseif($user->plan_name == 'pro') bg-indigo-100 text-indigo-800
                @elseif($user->plan_name == 'start') bg-green-100 text-green-800
                @else bg-slate-100 text-slate-800 @endif">
                {{ $user->plan_name }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($user->status === "active")
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                Ativo
            </span>
            @elseif($user->status === "suspended")
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Suspenso
            </span>
            @elseif($user->status === "inactive")
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                Inativo
            </span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i class="fas fa-layer-group text-xs text-teal-400"></i>
                    <span>{{ $user->workspaces_count ?? 0 }} workspaces</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-tags text-xs text-blue-400"></i>
                    <span>{{ $user->topics_count ?? 0 }} tópicos</span>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="user-actions">
                <button class="text-teal-400 hover:text-teal-300 edit-user" 
                    data-user-id="{{ $user->id }}"
                    data-user-data="{{ json_encode($user) }}"
                    title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                
                @if($user->status === 'active')
                <button class="action-btn btn-suspend toggle-status" 
                    data-user-id="{{ $user->id }}"
                    data-action="suspend"
                    title="Suspender">
                    <i class="fas fa-pause"></i>
                </button>
                @else
                <button class="action-btn btn-activate toggle-status" 
                    data-user-id="{{ $user->id }}"
                    data-action="activate"
                    title="Ativar">
                    <i class="fas fa-play"></i>
                </button>
                @endif
                
                <button class="action-btn btn-reset reset-password" 
                    data-user-id="{{ $user->id }}"
                    title="Redefinir Senha">
                    <i class="fas fa-key"></i>
                </button>
                
                <button class="text-red-400 hover:text-red-300 delete-user" 
                    data-user-id="{{ $user->id }}"
                    title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
                
                <button class="text-purple-400 hover:text-purple-300 view-activities" 
                    data-user-id="{{ $user->id }}"
                    title="Ver Atividades">
                    <i class="fas fa-history"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
            <i class="fas fa-users text-2xl mb-2"></i>
            <p>Nenhum usuário encontrado</p>
        </td>
    </tr>
@endforelse