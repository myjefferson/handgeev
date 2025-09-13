<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Handgeev</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite('resources/css/app.css')
        @vite('resources/views/template/css/dashboard.css')
        
        @vite('resources/js/app.js')

        {{-- jQuery --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        {{-- jQuery Mask Plugin --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

        {{-- Flowbite JS --}}
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    </head>
    <body class="font-sans antialiased text-white">

        @stack('style')

        <!-- Top User Bar -->
        <div class="user-menu fixed top-0 right-0 left-0 z-30 flex items-center justify-end px-4 md:px-6">
            <div class="flex items-center space-x-4">
                <!-- User profile dropdown -->
                <div class="relative">
                    <button id="userDropdownButton" data-dropdown-toggle="userDropdown" class="flex items-center space-x-2 pl-4 text-sm rounded-full focus:ring-2 focus:ring-teal-400">
                        <span class="hidden md:block text-gray-300">{{ Auth::user()->name ?? 'Usuário' }}</span>
                        <div class="user-avatar w-10 h-10 rounded-full bg-teal-400/10 flex items-center justify-center border border-teal-400/20">
                            <i class="fas fa-user text-teal-400"></i>
                        </div>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div id="userDropdown" class="z-40 hidden bg-slate-800 divide-y divide-slate-700 rounded-lg shadow w-44 border border-slate-700">
                        <div class="px-4 py-3 text-sm text-gray-300">
                            <a href="{{route('user.profile')}}" class="user-dropdown-option">
                                <div class="font-medium hover:text-teal-500">{{ Auth::user()->name ?? 'Usuário' }}</div>
                                <div class="truncate text-gray-400">{{ Auth::user()->email ?? 'email@exemplo.com' }}</div>
                            </a>
                            @auth
                                @free
                                    <p class="bg-primary-600 w-max text-black text-sm rounded-md px-2 py-1 mt-2">
                                        Conta Free
                                    </p>
                                @endfree

                                @pro
                                    <div class="flex items-center bg-purple-600 w-max text-white text-sm rounded-md px-2 py-1 mt-2">                                    
                                        <i class="fas fa-crown text-white w-3 h-3 mr-2 p-0"></i>
                                        <p>Conta Pro</p>
                                    </div>
                                @endpro
                                
                                @admin
                                    <div class="flex items-center bg-slate-900 w-max text-white text-sm rounded-md px-2 py-1 mt-2">                                    
                                        <p>Admin</p>
                                    </div>
                                @endadmin
                            @endauth
                        </div>
                        <ul class="py-2 text-sm text-gray-300">
                            <li>
                                <a href="{{ route('dashboard.settings') }}" class="user-dropdown-option block px-4 py-2">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="user-dropdown-option block px-4 py-2">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Exit
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar toggle -->
        <button data-drawer-target="cta-button-sidebar" data-drawer-toggle="cta-button-sidebar" aria-controls="cta-button-sidebar" type="button" class="fixed top-2 left-4 z-50 inline-flex items-center py-1 px-2 mt-2 text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 teal-glow">
            <span class="sr-only">Abrir menu</span>
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Sidebar -->
        <aside id="cta-button-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 sidebar-gradient" aria-label="Sidebar">
            <div class="h-full px-5 py-6 overflow-y-auto">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <img class="w-40" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev">
                </div>
                
                <!-- Navigation -->
                <ul class="space-y-1 font-medium">
                    <li>
                        @if(auth()->user()->canCreateWorkspace())
                            <button data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace" class="flex items-center w-full p-3 text-white rounded-lg border border-teal-500 teal-glow bg-teal-400/10 hover:bg-teal-400/20 transition-colors group mb-4">
                                <div class="w-8 h-8 rounded-full bg-teal-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-plus text-slate-900"></i>
                                </div>
                                <span class="font-semibold">Add Workspace</span>
                            </button>
                        @else
                            @include('components.buttons.button-upgrade-pro', ['subtitle' => 'Unlock unlimited workspaces'])
                        @endif
                    </li>
                    <li>
                        <a href="{{route('dashboard.home')}}" class="nav-item flex items-center p-3 text-gray-300 rounded-lg group {{ request()->routeIs('dashboard.home') ? 'active' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center mr-3">
                                <i class="fas fa-home text-teal-400"></i>
                            </div>
                            <span class="font-medium">Início</span>
                        </a>
                    </li>

                    <!-- Apenas Admin -->
                    
                    
                    <!-- Workspaces Section -->
                    <li class="pt-4">
                        @admin
                            <div class="border-t border-gray-700 pb-4">
                                <div class="flex items-center mb-2 px-3 pt-4">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administração</p>
                                </div>
                                <div class="items-center justify-between w-full text-gray-300 rounded-lg group">
                                    <a href="{{ route('admin.users') }}" class="button-item flex w-full items-center p-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center mr-3">
                                            <i class="fas fa-users"></i> 
                                        </div>
                                        <span class="text-sm font-medium truncate">Usuários</span>
                                    </a>
                                    <a href="{{ route('admin.plans') }}" class="button-item flex w-full items-center p-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center mr-3">
                                            <i class="fas fa-crown"></i> 
                                        </div>
                                        <span class="text-sm font-medium truncate">Planos</span>
                                    </a>
                                </div>
                            </div>
                        @endadmin

                        <div class="flex items-center mb-2 px-3">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Meus Workspaces</span>
                            <span class="ml-2 text-xs bg-teal-400/20 text-teal-400 px-2 py-0.5 rounded-full">{{ count($workspaces ?? []) }}</span>
                        </div>                        
                        @isset($workspaces)
                            @foreach($workspaces as $workspace)
                                <div class="button-item mb-1 animate-fade-in ">
                                    <div class="flex items-center justify-between w-full text-gray-300 rounded-lg group">
                                        <a href="{{ route('workspace.index', ['id' => $workspace->id]) }}" class="flex w-full items-center p-3">       
                                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center mr-3">
                                                <i class="fas fa-folder text-teal-400"></i>
                                            </div>
                                            <span class="text-sm font-medium truncate">{{ $workspace->title }}</span>
                                        </a>
                                        <div>
                                            <button id="optionsButton" data-dropdown-toggle="dropdown-workspace-{{$workspace->id }}" class="text-gray-400 hover:text-teal-400 focus:ring-2 focus:ring-teal-400 rounded-full h-7 w-7 mr-3 transition-colors" type="button">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            @include('components.dropdown.dropdown-options-workspace', ['workspace' => $workspace])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </li>
                </ul>
                
                <!-- Beta Banner -->
                <div id="dropdown-cta" class="p-4 mt-8 rounded-lg bg-teal-400/10 border border-teal-400/20" role="alert">
                    <div class="flex items-center mb-2">
                        <span class="bg-teal-400 text-slate-900 text-xs font-bold me-2 px-2.5 py-0.5 rounded">BETA</span>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 inline-flex justify-center items-center w-6 h-6 text-teal-400 rounded-lg focus:ring-2 focus:ring-teal-400 p-1 hover:bg-teal-400/20" data-dismiss-target="#dropdown-cta" aria-label="Close">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <p class="text-xs text-teal-300 mb-3">
                        Handgeev ainda está em versão de testes.
                    </p>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-400">{{ env('APP_VERSION') }}</span>
                        <a href="{{route('dashboard.about')}}" class="text-teal-400 hover:text-teal-300 transition-colors">Sobre</a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- <!-- Card de Estatísticas (apenas Pro/Admin) -->
        @can('export-data')
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-semibold mb-4">Estatísticas</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Visualizações</span>
                    <span class="font-medium">{{ $totalViews }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tópicos</span>
                    <span class="font-medium">{{ $topicsCount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Campos</span>
                    <span class="font-medium">{{ $fieldsCount }}</span>
                </div>
            </div>
        </div>
        @endcan --}}


        <!-- Main content -->
        <div class="main-content min-h-screen p-5">
            <div class=" backdrop-blur-sm rounded-2xl p-8 animate-fade-in mt-5">
            {{-- <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 animate-fade-in mt-5"> --}}
                @if (Auth::check())
                    @yield('content_dashboard')
                @else
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
                            <p class="text-gray-400">Faça login para acessar o dashboard</p>
                            <a href="{{ route('login.index') }}" class="inline-block mt-4 px-4 py-2 bg-teal-400 text-slate-900 rounded-lg font-medium hover:bg-teal-300 transition-colors">Fazer Login</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @stack('scripts')

        @include('components.modals.modal-add-workspace')
        @include('components.modals.modal-input-text')
        @include('components.modals.modal-confirm')
        {{-- @include('components.modals.modal-edit-workspace') --}}

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Adicionar classe active baseado na URL atual
                const currentPath = window.location.pathname;
                document.querySelectorAll('.nav-item').forEach(item => {
                    const link = item.querySelector('a');
                    if (link && link.getAttribute('href') === currentPath) {
                        item.classList.add('active');
                    }
                });
                
                // Melhorar a experiência mobile
                const sidebar = document.getElementById('cta-button-sidebar');
                const sidebarToggle = document.querySelector('[data-drawer-toggle="cta-button-sidebar"]');
                
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                });
                
                // Fechar sidebar ao clicar fora (em mobile)
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 768 && !sidebar.contains(e.target) && e.target !== sidebarToggle && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.add('-translate-x-full');
                    }
                });
                
                // Animações suaves para os elementos
                setTimeout(() => {
                    document.querySelectorAll('.workspace-item').forEach((item, index) => {
                        item.style.animationDelay = `${index * 0.1}s`;
                    });
                }, 100);
            });
        </script>
    </body>
</html>