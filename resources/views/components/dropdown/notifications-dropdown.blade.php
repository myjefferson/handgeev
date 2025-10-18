<!-- Dropdown de Notificações -->
<div class="relative" id="notifications-dropdown">
    <!-- Botão do sino -->
    <button 
        id="notifications-button"
        class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-200"
        type="button"
    >
        <!-- Ícone do sino -->
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
        </svg>
        
        <!-- Badge de contagem -->
        @if($unreadCount > 0)
            <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">
                {{ $unreadCount }}
            </div>
        @endif
    </button>

    <!-- Dropdown menu -->
    <div 
        id="notifications-menu"
        class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-lg shadow-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 hidden"
    >
        <!-- Cabeçalho -->
        <div class="px-4 py-3">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notificações</h3>
        </div>
        
        <!-- Lista de notificações -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="flex px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <div class="flex-shrink-0 mr-3">
                        @if($notification->type === 'App\Notifications\WorkspaceInviteNotification')
                            <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {!! $notification->data['message'] !!}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </p>
                        
                        @if($notification->type === 'App\Notifications\WorkspaceInviteNotification' && !$notification->read())
                            <div class="mt-2 flex space-x-2">
                                <form action="{{ route('collaboration.invite.accept', $notification->data['invitation_id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-teal-600 hover:bg-teal-700 text-white px-3 py-1.5 rounded-lg font-medium transition-colors duration-200">
                                        Aceitar
                                    </button>
                                </form>
                                <form action="{{ route('collaboration.invite.reject', $notification->data['invitation_id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg font-medium transition-colors duration-200">
                                        Recusar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    @if(!$notification->read())
                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="flex-shrink-0 ml-2">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="px-4 py-6 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma notificação</p>
                </div>
            @endforelse
        </div>
        
        <!-- Rodapé -->
        @if($notifications->count() > 0)
            <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50">
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-xs font-medium text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300 text-center transition-colors duration-200">
                        Marcar todas como lidas
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript puro para controlar o dropdown -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('notifications-dropdown');
    const button = document.getElementById('notifications-button');
    const menu = document.getElementById('notifications-menu');
    let isOpen = false;

    // Abrir/fechar dropdown
    button.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleDropdown();
    });

    // Fechar ao clicar fora
    document.addEventListener('click', function(e) {
        if (isOpen && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

    // Fechar ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (isOpen && e.key === 'Escape') {
            closeDropdown();
        }
    });

    function toggleDropdown() {
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    function openDropdown() {
        menu.classList.remove('hidden');
        menu.classList.add('animate-fade-in', 'animate-scale-in');
        isOpen = true;
    }

    function closeDropdown() {
        menu.classList.add('animate-fade-out', 'animate-scale-out');
        
        setTimeout(() => {
            menu.classList.add('hidden');
            menu.classList.remove('animate-fade-in', 'animate-scale-in', 'animate-fade-out', 'animate-scale-out');
        }, 200);
        
        isOpen = false;
    }

    // Prevenir fechamento ao clicar dentro do menu
    menu.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

// Adicionar estilos de animação no CSS
const style = document.createElement('style');
style.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    
    .animate-scale-in {
        animation: scaleIn 0.2s ease-out;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.2s ease-in;
    }
    
    .animate-scale-out {
        animation: scaleOut 0.2s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes scaleOut {
        from { transform: scale(1); opacity: 1; }
        to { transform: scale(0.95); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>