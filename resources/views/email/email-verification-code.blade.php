<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Código de Verificação - Handgeev</title>
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
            .code-display {
                font-size: 32px;
                font-weight: bold;
                text-align: center;
                letter-spacing: 8px;
                background: #f3f4f6;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                color: #111827;
                border: 2px dashed #0d9488;
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
                <p>Verificação de Email</p>
            </div>
            
            <div class="content">
                <div class="icon">🔐</div>
                <h2 class="text-center">Seu Código de Verificação</h2>
                
                <p>Olá, <strong>{{ $user->name }}</strong>!</p>
                
                <p>Use o código abaixo para verificar seu email e ativar sua conta no Handgeev:</p>
                
                <div class="code-display">
                    {{ $verificationCode }}
                </div>
                
                <div class="security-note">
                    <strong>⚠️ Importante:</strong> 
                    <ul>
                        <li>Este código expira em <strong>30 minutos</strong></li>
                        <li>Não compartilhe este código com ninguém</li>
                        <li>Se você não solicitou este código, ignore este email</li>
                    </ul>
                </div>
                
                <p>Copie e cole o código na página de verificação do Handgeev para concluir seu cadastro.</p>
                
                <div class="footer">
                    <p>© {{ date('Y') }} Handgeev. Todos os direitos reservados.</p>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                </div>
            </div>
        </div>
    </body>
</html>