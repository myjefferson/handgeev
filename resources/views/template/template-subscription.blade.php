<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HandGeev') }} - Assinatura</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .plan-card {
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }
            .plan-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }
            .plan-card.popular {
                border-color: #10B981;
                position: relative;
            }
            .popular-badge {
                position: absolute;
                top: -10px;
                right: 20px;
                background: #10B981;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
            }
            .feature-list {
                list-style: none;
                padding: 0;
            }
            .feature-list li {
                padding: 8px 0;
                border-bottom: 1px solid #E5E7EB;
            }
            .feature-list li:last-child {
                border-bottom: none;
            }
            .feature-list li::before {
                content: "✓";
                color: #10B981;
                font-weight: bold;
                margin-right: 8px;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                            <span class="ml-2 text-xl font-bold text-gray-900">HandGeev</span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Dashboard
                        </a>
                        @auth
                            <span class="text-sm text-gray-500">{{ Auth::user()->email }}</span>
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            @yield('content_subscription')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} HandGeev. Todos os direitos reservados.
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script>
            // Feedback messages
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-hide alerts after 5 seconds
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert-auto-hide');
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);

                // Plan selection
                const planCards = document.querySelectorAll('.plan-card');
                planCards.forEach(card => {
                    card.addEventListener('click', function() {
                        const priceId = this.dataset.priceId;
                        if (priceId) {
                            document.getElementById('price_id').value = priceId;
                            document.getElementById('checkout-form').submit();
                        }
                    });
                });
            });
        </script>
    </body>
</html>