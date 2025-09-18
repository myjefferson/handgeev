<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $isExistingUser ? 'Convite para Workspace' : 'Bem-vindo ao Handgeev' }}</title>
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
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">Handgeev</div>
                <p>Gestão de Workspaces Colaborativos</p>
            </div>
            
            <div class="content">
                @if($isExistingUser)
                    <div class="icon">📋</div>
                    <h2>Você foi convidado para um workspace!</h2>
                    
                    <p>Olá! <strong>{{ $inviter->name ?? 'Um usuário' }}</strong> convidou você para colaborar no workspace <strong>"{{ $workspace->title }}"</strong>.</p>
                    
                    <div class="details">
                        <div class="detail-item"><strong>Workspace:</strong> {{ $workspace->title }}</div>
                        <div class="detail-item"><strong>Sua função:</strong> {{ ucfirst($collaborator->role) }}</div>
                        @if($workspace->description)
                        <div class="detail-item"><strong>Descrição:</strong> {{ $workspace->description }}</div>
                        @endif
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="{{ $acceptUrl }}" class="button">Acessar Workspace</a>
                    </div>
                    
                    <p>Se você não reconhece este convite, pode ignorar este e-mail com segurança.</p>
                    
                @else
                    <div class="icon">🎉</div>
                    <h2>Bem-vindo ao Handgeev!</h2>
                    
                    <p>Olá! <strong>{{ $inviter->name ?? 'Um usuário' }}</strong> convidou você para colaborar no workspace <strong>"{{ $workspace->title }}"</strong> no Handgeev.</p>
                    
                    <p>O Handgeev é uma plataforma poderosa para gerenciar workspaces e colaborar com sua equipe.</p>
                    
                    <div class="details">
                        <div class="detail-item"><strong>Workspace:</strong> {{ $workspace->title }}</div>
                        <div class="detail-item"><strong>Sua função:</strong> {{ ucfirst($collaborator->role) }}</div>
                        @if($workspace->description)
                        <div class="detail-item"><strong>Descrição:</strong> {{ $workspace->description }}</div>
                        @endif
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="{{ $acceptUrl }}" class="button">Criar Conta e Acessar</a>
                    </div>
                    
                    <p><strong>Importante:</strong> Você precisará criar uma conta para acessar o workspace. O processo é rápido e simples!</p>
                @endif
                
                <div class="footer">
                    <p>© {{ date('Y') }} Handgeev. Todos os direitos reservados.</p>
                    <p>Este é um e-mail automático, por favor não responda.</p>
                    <p>Se você não solicitou este convite, ignore este e-mail.</p>
                    <p>Este link de convite expirará em 7 dias por questões de segurança.</p>
                </div>
            </div>
        </div>
    </body>
</html>