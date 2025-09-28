<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperação de Senha - Handgeev</title>
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
            .logo {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .content {
                background-color: white;
                padding: 30px;
                border-radius: 0 0 10px 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            }
            .button {
                display: inline-block;
                padding: 14px 28px;
                background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                margin: 20px 0;
                transition: transform 0.2s ease;
            }
            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
            }
            .details {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                border-left: 4px solid #0d9488;
            }
            .detail-item {
                margin-bottom: 8px;
                color: #374151;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e5e7eb;
                font-size: 12px;
                color: #6b7280;
            }
            .icon {
                font-size: 48px;
                margin-bottom: 20px;
                text-align: center;
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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">Handgeev</div>
                <p>Recuperação de Senha</p>
            </div>
            
            <div class="content">
                <div class="icon">🔐</div>
                <h2 class="text-center">Redefinição de Senha</h2>
                
                <p>Olá, <strong>{{ $user->name }}</strong>!</p>
                
                <p>Recebemos uma solicitação para redefinir a senha da sua conta Handgeev. Clique no botão abaixo para criar uma nova senha segura:</p>
                
                <div class="text-center">
                    <a href="{{ $resetUrl }}" class="button">Redefinir Minha Senha</a>
                </div>
                
                <div class="security-note">
                    <strong>⚠️ Importante:</strong> Por questões de segurança, este link expirará em <strong>60 minutos</strong>. Se você não solicitou esta redefinição, ignore este e-mail.
                </div>
                
                <p><strong>Dica de segurança:</strong> Escolha uma senha forte com letras maiúsculas, minúsculas, números e caracteres especiais.</p>
                
                <div class="footer">
                    <p>© {{ date('Y') }} Handgeev. Todos os direitos reservados.</p>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>Se você não solicitou esta redefinição, sua conta está segura - apenas ignore este e-mail.</p>
                </div>
            </div>
        </div>
    </body>
</html>