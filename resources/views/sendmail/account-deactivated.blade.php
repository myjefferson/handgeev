<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Conta Desativada</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .content {
                background: #f8f9fa;
                padding: 30px;
                border-radius: 0 0 10px 10px;
            }
            .countdown {
                background: white;
                border-radius: 10px;
                padding: 20px;
                text-align: center;
                margin: 20px 0;
                border-left: 4px solid #667eea;
            }
            .days {
                font-size: 48px;
                font-weight: bold;
                color: #667eea;
                margin: 10px 0;
            }
            .cta-button {
                display: inline-block;
                background: #667eea;
                color: white;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 25px;
                font-weight: bold;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                color: #666;
                font-size: 14px;
            }
            .highlight {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 5px;
                padding: 15px;
                margin: 15px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Até Breve, {{ $userName }}! 👋</h1>
                <p>Sua conta foi desativada, mas sua história ainda não acabou...</p>
            </div>
            
            <div class="content">
                <p>Olá <strong>{{ $userName }}</strong>,</p>
                
                <p>Recebemos sua solicitação para desativar a conta. Queremos que saiba que:</p>
                
                <div class="highlight">
                    <p><strong>✨ Seu legato está intacto:</strong><br>
                    Todos os seus workspaces, projetos e configurações foram preservados.</p>
                </div>

                <div class="countdown">
                    <h3>Tempo para reconsiderar:</h3>
                    <div class="days">{{ $daysRemaining }}</div>
                    <p>dias restantes para recuperar tudo</p>
                </div>

                <p style="text-align: center;">
                    <strong>"Seu legado ainda está aqui, esperando para ser retomado. Sua melhor versão merece um final diferente."</strong>
                </p>

                <div style="text-align: center;">
                    <a href="{{ route('login.show') }}" class="cta-button">
                        Recuperar Minha Conta
                    </a>
                </div>

                <p><strong>O que acontece depois dos {{ $daysRemaining }} dias?</strong><br>
                Sua conta e todos os dados associados serão permanentemente removidos do nosso sistema.</p>

                <p><strong>Precisa de ajuda?</strong><br>
                Se esta foi uma decisão equivocada ou se tiver dúvidas, nossa equipe de suporte está aqui para ajudar.</p>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="mailto:handgeev@gmail.com" style="color: #667eea; text-decoration: none;">
                        ✉️ Falar com o Suporte
                    </a>
                </div>
            </div>

            <div class="footer">
                <p>Com gratidão por fazer parte da nossa jornada 💫</p>
                <p><small>Esta é uma mensagem automática - por favor não responda este email</small></p>
            </div>
        </div>
    </body>
</html>