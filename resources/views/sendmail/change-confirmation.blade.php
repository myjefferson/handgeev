<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme sua alteração de email - Handgeev</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .btn-confirm {
            display: inline-block;
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        h2 {
            color: #111827;
            margin-top: 0;
        }
        p {
            color: #374151;
            margin-bottom: 16px;
        }
        .text-center {
            text-align: center;
        }
        .email-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Handgeev</div>
            <p>Confirmação de Alteração de Email</p>
        </div>
        
        <div class="content">
            <h2 class="text-center">Confirme sua alteração de email</h2>
            
            <p>Olá, <strong>{{ $user->name }}</strong>!</p>
            
            <p>Recebemos uma solicitação para alterar o email da sua conta no Handgeev.</p>
            
            <div class="email-info">
                <p><strong>Email atual:</strong> {{ $user->email }}</p>
                <p><strong>Novo email:</strong> {{ $newEmail }}</p>
            </div>
            
            <p>Para confirmar esta alteração, clique no botão abaixo:</p>
            
            <div class="text-center">
                <a href="{{ route('email.confirm', $token) }}" class="btn-confirm">
                    Confirmar Alteração de Email
                </a>
            </div>
            
            <div class="security-note">
                <strong>⚠️ Importante:</strong> 
                <ul>
                    <li>Este link expira em <strong>24 horas</strong></li>
                    <li>Se você não solicitou esta alteração, ignore este email</li>
                    <li>Seu email atual continuará funcionando até a confirmação</li>
                </ul>
            </div>
            
            <p>Se o botão não funcionar, copie e cole este link no seu navegador:</p>
            <p style="word-break: break-all; font-size: 12px; color: #6b7280;">
                {{ route('email.confirm', $token) }}
            </p>
            
            <div class="footer">
                <p>© {{ date('Y') }} Handgeev. Todos os direitos reservados.</p>
                <p>Este é um e-mail automático, por favor não responda.</p>
            </div>
        </div>
    </div>
</body>
</html>