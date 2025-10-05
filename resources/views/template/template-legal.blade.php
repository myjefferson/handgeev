<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title') - HandGeev</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icon.png') }}">
        
        <!-- Meta Tags -->
        <meta name="description" content="@yield('description', 'HandGeev - Crie e gerencie suas APIs de forma simples')">
        <meta name="keywords" content="api, workspace, json, handgeev, desenvolvimento">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Custom Styles -->
        <style>
            .legal-content {
                @apply text-gray-700 dark:text-gray-300;
            }
            .legal-content h1 {
                @apply text-3xl font-bold text-gray-900 dark:text-white mb-6;
            }
            .legal-content h2 {
                @apply text-2xl font-bold text-gray-900 dark:text-white mt-12 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700;
            }
            .legal-content h3 {
                @apply text-xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4;
            }
            .legal-content h4 {
                @apply text-lg font-medium text-gray-800 dark:text-gray-200 mt-6 mb-3;
            }
            .legal-content p {
                @apply text-gray-700 dark:text-gray-300 mb-4 leading-relaxed;
            }
            .legal-content ul, .legal-content ol {
                @apply text-gray-700 dark:text-gray-300 mb-6 space-y-2;
            }
            .legal-content ul {
                @apply list-disc list-inside;
            }
            .legal-content ol {
                @apply list-decimal list-inside;
            }
            .legal-content li {
                @apply mb-2;
            }
            .legal-content strong {
                @apply font-semibold text-gray-900 dark:text-white;
            }
            .legal-content a {
                @apply text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 underline transition-colors;
            }
            .legal-content blockquote {
                @apply border-l-4 border-teal-500 dark:border-teal-400 pl-4 py-2 my-4 bg-teal-50 dark:bg-teal-900/20 text-gray-700 dark:text-gray-300;
            }
            .legal-content .lead {
                @apply text-lg text-gray-800 dark:text-gray-200 font-medium;
            }
        </style>
    </head>
    <body class="h-full bg-gray-900 transition-colors duration-200">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('landing.portfoline' )}}">
                            <img class="w-44" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex items-center space-x-6">
                        <button onclick="window.history.back()" 
                        class="text-lg flex bg-transparent hover:border-teal-500 items-center font-medium transition-colors px-4 py-2 text-white border border-white rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11H7.83l4.88-4.88c.39-.39.39-1.03 0-1.42a.996.996 0 0 0-1.41 0l-6.59 6.59a.996.996 0 0 0 0 1.41l6.59 6.59a.996.996 0 1 0 1.41-1.41L7.83 13H19c.55 0 1-.45 1-1s-.45-1-1-1"/></svg>
                            <span class="ml-2">Voltar</span>
                        </button>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-14">
            @yield('content_legal')
        </main>

        <!-- Footer -->
        @include('components.footer.footer')

        <!-- Script para controle do tema escuro -->
        <script>
            // Verificar preferência do usuário
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </body>
</html>