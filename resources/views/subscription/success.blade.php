<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso - Handgeev</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #10b981;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #10b981;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        
        .check-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #fff;
            stroke-miterlimit: 10;
            margin: 10% auto;
            box-shadow: inset 0px 0px 0px #10b981;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        
        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #10b981;
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-white">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Ícone de Sucesso -->
            <div class="mb-8">
                <div class="success-checkmark">
                    <svg class="check-icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold mb-4">Pagamento Confirmado!</h1>
            <p class="text-gray-400 mb-8">
                Sua assinatura Pro foi ativada com sucesso. Agora você tem acesso a todos os recursos premium do Handgeev.
            </p>

            <div class="bg-slate-800/50 rounded-2xl p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">O que você ganhou:</h2>
                <ul class="text-left space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span>5 Workspaces</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span>Tópicos e Campos Ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span>API Completa</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span>Domínio Personalizado</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <span>Exportação de Dados</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard.home') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-rocket mr-2"></i>Começar a Usar
                </a>
                <a href="{{ route('billing.portal') }}" class="border border-gray-600 hover:border-gray-500 text-gray-300 hover:text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-receipt mr-2"></i>Ver Faturas
                </a>
            </div>

            <p class="text-sm text-gray-500 mt-8">
                Problemas com sua assinatura? 
                <a href="mailto:support@handgeev.com" class="text-teal-400 hover:text-teal-300">Entre em contato com o suporte</a>
            </p>
        </div>
    </div>
</body>
</html>