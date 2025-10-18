<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Workspace - HandGeev</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0369a1;
            --danger: #ef4444;
            --success: #10b981;
            --dark: #1e293b;
            --darker: #0f172a;
            --light: #f1f5f9;
            --gray: #94a3b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--darker), var(--dark));
            color: var(--light);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        header {
            background: rgba(15, 23, 42, 0.95);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .workspace-name {
            color: var(--primary);
            font-weight: 600;
        }
        
        .description {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }
        
        .tab-navigation {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .tab-button {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .tab-button.active {
            color: var(--primary);
        }
        
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }
        
        .tab-content {
            padding: 2rem;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        .security-card {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .input-group {
            display: flex;
            gap: 0.5rem;
        }
        
        input[type="text"], input[type="password"] {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.5);
            color: var(--light);
            font-size: 1rem;
        }
        
        input:read-only {
            background: rgba(255, 255, 255, 0.1);
            color: var(--gray);
        }
        
        button {
            cursor: pointer;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .link-display {
            display: flex;
            margin-top: 1rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            word-break: break-all;
            align-items: center;
        }
        
        .link-text {
            flex: 1;
            padding-right: 1rem;
        }
        
        .access-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        .badge-public {
            background: var(--success);
            color: white;
        }
        
        .badge-private {
            background: var(--danger);
            color: white;
        }
        
        .share-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .access-controls {
            margin-top: 2rem;
        }
        
        .control-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .control-info {
            flex: 1;
        }
        
        .control-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .control-desc {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.2);
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--primary);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        
        .history-table th, .history-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .history-table th {
            font-weight: 600;
            color: var(--gray);
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .status-active {
            background: var(--success);
        }
        
        .status-revoked {
            background: var(--danger);
        }
        
        .action-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .action-btn:hover {
            color: var(--light);
        }
        
        footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--gray);
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            .control-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-share-alt"></i> Compartilhar Workspace</h1>
            <p class="description">Gerencie o acesso ao workspace <span class="workspace-name">Minha API de Produtos</span></p>
            
            <div class="tab-navigation">
                <button class="tab-button active" data-tab="interface">Interface API</button>
                <button class="tab-button" data-tab="rest">REST API</button>
                <button class="tab-button" data-tab="security">Segurança</button>
                <button class="tab-button" data-tab="history">Histórico</button>
            </div>
        </header>
        
        <div class="tab-content">
            <!-- Interface API Tab -->
            <div class="tab-pane active" id="interface">
                <div class="security-card">
                    <h3 class="card-title"><i class="fas fa-eye"></i> Link de Visualização</h3>
                    <p>Compartilhe este link para permitir que outras pessoas visualizem este workspace na Interface API.</p>
                    
                    <div class="form-group">
                        <label>Link de Compartilhamento</label>
                        <div class="input-group">
                            <input type="text" id="interface-link" value="https://handgeev.com/workspace/interface/api/8x3j9k2m5p6q7r4s" readonly>
                            <button class="btn-secondary" id="copy-interface"><i class="fas fa-copy"></i> Copiar</button>
                            <button class="btn-primary" id="regenerate-interface"><i class="fas fa-sync-alt"></i> Regenerar</button>
                        </div>
                    </div>
                    
                    <div class="share-actions">
                        <button class="btn-secondary"><i class="fas fa-envelope"></i> Enviar por Email</button>
                        <button class="btn-secondary"><i class="fab fa-slack"></i> Compartilhar no Slack</button>
                        <button class="btn-secondary"><i class="fab fa-whatsapp"></i> Compartilhar no WhatsApp</button>
                    </div>
                </div>
                
                <div class="access-controls">
                    <h3 class="card-title"><i class="fas fa-cog"></i> Configurações de Acesso</h3>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Acesso Público</div>
                            <div class="control-desc">Qualquer pessoa com o link pode visualizar este workspace</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Exigir Senha</div>
                            <div class="control-desc">Os visualizadores precisarão digitar uma senha para acessar</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="form-group" id="password-field" style="display: none;">
                        <label>Senha de Acesso</label>
                        <div class="input-group">
                            <input type="password" id="access-password" placeholder="Digite uma senha">
                            <button class="btn-primary">Salvar Senha</button>
                        </div>
                    </div>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Permitir Download</div>
                            <div class="control-desc">Os visualizadores podem baixar os dados em JSON</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- REST API Tab -->
            <div class="tab-pane" id="rest">
                <div class="security-card">
                    <h3 class="card-title"><i class="fas fa-code"></i> Endpoint da API</h3>
                    <p>Use este endpoint para acessar os dados deste workspace via REST API.</p>
                    
                    <div class="form-group">
                        <label>URL da API</label>
                        <div class="input-group">
                            <input type="text" id="api-endpoint" value="https://api.handgeev.com/workspace/8x3j9k2m5p6q7r4s/data" readonly>
                            <button class="btn-secondary" id="copy-api"><i class="fas fa-copy"></i> Copiar</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Chave de API</label>
                        <div class="input-group">
                            <input type="text" id="api-key" value="hg_5q7r4s8x3j9k2m5p6q7r4s8x3j9k2m" readonly>
                            <button class="btn-secondary" id="copy-key"><i class="fas fa-copy"></i> Copiar</button>
                            <button class="btn-primary" id="regenerate-key"><i class="fas fa-sync-alt"></i> Regenerar</button>
                        </div>
                        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--gray);">Mantenha esta chave em segredo. Ela fornece acesso completo aos seus dados.</p>
                    </div>
                </div>
                
                <div class="access-controls">
                    <h3 class="card-title"><i class="fas fa-plug"></i> Configurações da API</h3>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Habilitar CORS</div>
                            <div class="control-desc">Permitir solicitações de outros domínios</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Limitar Taxa de Requisições</div>
                            <div class="control-desc">Definir um limite de solicitações por minuto</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>Máximo de Requisições por Minuto</label>
                        <div class="input-group">
                            <input type="text" value="60">
                            <button class="btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Security Tab -->
            <div class="tab-pane" id="security">
                <div class="security-card">
                    <h3 class="card-title"><i class="fas fa-lock"></i> Chaves de Acesso</h3>
                    <p>Gerencie as chaves de acesso para este workspace. A global_hash é compartilhada entre todos os seus workspaces, enquanto a WORKSPACE_KEY é específica para este workspace.</p>
                    
                    <div class="form-group">
                        <label>Global Hash</label>
                        <div class="input-group">
                            <input type="text" id="global-hash" value="8x3j9k2m5p6q7r4s8x3j9k2m5p6q7r4s" readonly>
                            <button class="btn-secondary" id="copy-global"><i class="fas fa-copy"></i> Copiar</button>
                            <button class="btn-primary" id="regenerate-global"><i class="fas fa-sync-alt"></i> Alterar</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Workspace Key</label>
                        <div class="input-group">
                            <input type="text" id="workspace-hash" value="5p6q7r4s8x3j9k2m5p6q7r4s8x3j9k2m" readonly>
                            <button class="btn-secondary" id="copy-workspace"><i class="fas fa-copy"></i> Copiar</button>
                            <button class="btn-primary" id="regenerate-workspace"><i class="fas fa-sync-alt"></i> Alterar</button>
                        </div>
                    </div>
                </div>
                
                <div class="security-card">
                    <h3 class="card-title"><i class="fas fa-shield-alt"></i> Proteção Avançada</h3>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Autenticação de Dois Fatores</div>
                            <div class="control-desc">Exigir 2FA para acessar as configurações deste workspace</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Log de Acessos</div>
                            <div class="control-desc">Registrar todos os acessos a este workspace</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="control-item">
                        <div class="control-info">
                            <div class="control-title">Limitar por IP</div>
                            <div class="control-desc">Permitir acesso apenas de endereços IP específicos</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="form-group" id="ip-field" style="display: none;">
                        <label>Endereços IP Permitidos</label>
                        <div class="input-group">
                            <input type="text" placeholder="Ex: 192.168.1.1, 10.0.0.2">
                            <button class="btn-primary">Adicionar IP</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- History Tab -->
            <div class="tab-pane" id="history">
                <div class="security-card">
                    <h3 class="card-title"><i class="fas fa-history"></i> Histórico de Compartilhamento</h3>
                    <p>Registro de todas as atividades de compartilhamento para este workspace.</p>
                    
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Usuário/IP</th>
                                <th>Ação</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12/05/2025 14:30</td>
                                <td>user@example.com</td>
                                <td>Link de visualização compartilhado</td>
                                <td><span class="status-indicator status-active"></span> Ativo</td>
                                <td>
                                    <button class="action-btn" title="Revogar"><i class="fas fa-ban"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>10/05/2025 09:15</td>
                                <td>192.168.1.45</td>
                                <td>Chave de API regenerada</td>
                                <td><span class="status-indicator status-active"></span> Ativo</td>
                                <td>
                                    <button class="action-btn" title="Revogar"><i class="fas fa-ban"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>05/05/2025 16:40</td>
                                <td>user@example.com</td>
                                <td>Workspace Key alterado</td>
                                <td><span class="status-indicator status-active"></span> Ativo</td>
                                <td>
                                    <button class="action-btn" title="Detalhes"><i class="fas fa-info-circle"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>01/05/2025 11:20</td>
                                <td>user@client.com</td>
                                <td>Link de visualização acessado</td>
                                <td><span class="status-indicator status-revoked"></span> Expirado</td>
                                <td>
                                    <button class="action-btn" title="Detalhes"><i class="fas fa-info-circle"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <footer>
            <p>HandGeev &copy; 2025 - Sistema de Gerenciamento de APIs</p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            // Tab navigation
            $('.tab-button').click(function() {
                const tabId = $(this).data('tab');
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                $('.tab-pane').removeClass('active');
                $(`#${tabId}`).addClass('active');
            });
            
            // Toggle password field
            $('input[type="checkbox"]').change(function() {
                if ($(this).parent().siblings('.control-info').find('.control-title').text() === 'Exigir Senha') {
                    $('#password-field').toggle(this.checked);
                }
                
                if ($(this).parent().siblings('.control-info').find('.control-title').text() === 'Limitar por IP') {
                    $('#ip-field').toggle(this.checked);
                }
            });
            
            // Copy buttons functionality
            $('#copy-interface').click(function() {
                copyToClipboard('#interface-link');
                showNotification('Link copiado para a área de transferência!');
            });
            
            $('#copy-api').click(function() {
                copyToClipboard('#api-endpoint');
                showNotification('Endpoint copiado para a área de transferência!');
            });
            
            $('#copy-key').click(function() {
                copyToClipboard('#api-key');
                showNotification('Chave de API copiada para a área de transferência!');
            });
            
            $('#copy-global').click(function() {
                copyToClipboard('#global-hash');
                showNotification('Global Hash copiada para a área de transferência!');
            });
            
            $('#copy-workspace').click(function() {
                copyToClipboard('#workspace-hash');
                showNotification('Workspace Key copiada para a área de transferência!');
            });
            
            // Regenerate buttons
            $('#regenerate-interface').click(function() {
                if (confirm('Tem certeza que deseja regenerar o link de compartilhamento? O link atual será invalidado.')) {
                    showNotification('Link regenerado com sucesso!');
                }
            });
            
            $('#regenerate-key').click(function() {
                if (confirm('Tem certeza que deseja regenerar a chave de API? A chave atual será invalidada.')) {
                    showNotification('Chave de API regenerada com sucesso!');
                }
            });
            
            $('#regenerate-global').click(function() {
                if (confirm('Tem certeza que deseja alterar a Global Hash? Isso afetará todos os seus workspaces.')) {
                    showNotification('Global Hash alterada com sucesso!');
                }
            });
            
            $('#regenerate-workspace').click(function() {
                if (confirm('Tem certeza que deseja alterar a Workspace Key? O link atual será invalidado.')) {
                    showNotification('Workspace Key alterada com sucesso!');
                }
            });
            
            // Helper function to copy to clipboard
            function copyToClipboard(element) {
                const $temp = $('<input>');
                $('body').append($temp);
                $temp.val($(element).val()).select();
                document.execCommand('copy');
                $temp.remove();
            }
            
            // Helper function to show notification
            function showNotification(message) {
                const notification = $(`<div class="notification">${message}</div>`);
                $('body').append(notification);
                notification.css({
                    position: 'fixed',
                    bottom: '20px',
                    right: '20px',
                    padding: '10px 20px',
                    background: 'var(--success)',
                    color: 'white',
                    borderRadius: '8px',
                    zIndex: 1000,
                    animation: 'fadeIn 0.3s ease'
                });
                
                setTimeout(function() {
                    notification.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
</body>
</html>