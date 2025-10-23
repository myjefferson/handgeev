<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Conta Restaurada</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 40px 30px;
                text-align: center;
            }
            .content {
                padding: 30px;
            }
            .welcome-box {
                background: #f0fdf4;
                border-radius: 10px;
                padding: 25px;
                text-align: center;
                margin: 25px 0;
                border-left: 4px solid #10b981;
            }
            .cta-button {
                display: inline-block;
                background: #10b981;
                color: white;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 25px;
                font-weight: bold;
                margin: 20px 0;
                transition: background 0.3s;
            }
            .cta-button:hover {
                background: #0da271;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                color: #666;
                font-size: 14px;
                padding: 20px;
                background: #f8f9fa;
            }
            .features {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin: 25px 0;
            }
            .feature {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
            }
            .highlight {
                background: #ecfdf5;
                border: 1px solid #d1fae5;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 Que bom ter você de volta!</h1>
                <p>Sua conta foi restaurada com sucesso!</p>
            </div>
            
            <div class="content">
                <p>Olá <strong>{{ $userName }}</strong>,</p>
                
                <p>É com grande alegria que informamos que <strong>sua conta foi restaurada com sucesso!</strong></p>

                <div class="welcome-box">
                    <h2 style="margin: 0; color: #059669;">Bem-vindo(a) de volta! 🚀</h2>
                    <p style="margin: 10px 0 0 0; font-size: 18px;">
                        Sua jornada continua de onde parou!
                    </p>
                </div>

                <div class="highlight">
                    <p><strong>✨ Tudo está exatamente como você deixou:</strong></p>
                    <ul style="text-align: left; margin: 15px 0;">
                        <li>Todos os seus workspaces</li>
                        <li>Projetos e configurações</li>
                        <li>Histórico de atividades</li>
                        <li>Preferências pessoais</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('login.show') }}" class="cta-button">
                        🚀 Acessar Minha Conta
                    </a>
                </div>

                <div class="features">
                    <div class="feature">
                        <strong>📊 Seus Dados</strong>
                        <p style="margin: 5px 0; font-size: 14px;">Totalmente preservados</p>
                    </div>
                    <div class="feature">
                        <strong>🔒 Segurança</strong>
                        <p style="margin: 5px 0; font-size: 14px;">Conta reativada com segurança</p>
                    </div>
                    <div class="feature">
                        <strong>⏰ Histórico</strong>
                        <p style="margin: 5px 0; font-size: 14px;">Atividades mantidas</p>
                    </div>
                    <div class="feature">
                        <strong>💫 Continuidade</strong>
                        <p style="margin: 5px 0; font-size: 14px;">Jornada sem interrupções</p>
                    </div>
                </div>

                <p><strong>Precisa de ajuda?</strong><br>
                Nossa equipe está à disposição para qualquer dúvida ou assistência que precisar.</p>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="mailto:handgeev@gmail.com" style="color: #10b981; text-decoration: none; font-weight: bold;">
                        💬 Falar com o Suporte
                    </a>
                </div>

                <p style="text-align: center; font-style: italic; color: #666;">
                    "Grandes histórias merecem ser continuadas. Estamos felizes em fazer parte da sua!"
                </p>
            </div>

            <div class="footer">
                <p>Equipe {{ config('app.name') }} 💫</p>
                <p><small>Esta é uma mensagem automática - por favor não responda este email</small></p>
                <p><small>Conta restaurada em: {{ $restoredAt->format('d/m/Y \à\s H:i') }}</small></p>
            </div>
        </div>
    </body>
</html>