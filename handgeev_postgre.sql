-- --------------------------------------------------------------------------------------
-- PostgreSQL Schema Conversion for 'handgeev' Database
-- --------------------------------------------------------------------------------------

-- Nota: O comando 'CREATE DATABASE handgeev;' e a conexão inicial devem ser feitos
-- fora deste script, ou seja, no terminal psql (\c handgeev) ou na ferramenta.

-- Desabilita/habilita a saída de mensagens para execução limpa
SET client_min_messages = WARNING;

-- ----------------------------------------------------------------------
-- PostgreSQL Schema para a Tabela 'sessions'
-- ----------------------------------------------------------------------

CREATE TABLE sessions (
    -- O ID da sessão é uma string (hash) e serve como chave primária
    id VARCHAR(255) NOT NULL PRIMARY KEY,

    -- Chave estrangeira opcional para o ID do usuário (pode ser NULL se não estiver logado)
    -- BIGINT é usado para consistência com o tipo BIGSERIAL da tabela users
    user_id BIGINT NULL REFERENCES users(id) ON DELETE CASCADE,

    -- Endereço IP do cliente
    ip_address VARCHAR(45) NULL,

    -- User Agent do navegador
    user_agent TEXT NULL,

    -- O payload da sessão (dados serializados)
    payload TEXT NOT NULL,

    -- Última atividade (UNIX timestamp)
    last_activity INTEGER NOT NULL,

    -- Índices para otimizar buscas
    -- Indexação por last_activity para limpeza de sessões expiradas
    CONSTRAINT sessions_last_activity_index
        UNIQUE (last_activity),
        
    -- Indexação por user_id para buscar sessões de um usuário específico (ex: "quem está logado?")
    CONSTRAINT sessions_user_id_index
        UNIQUE (user_id)
);

--------------------------------------------------------------------------------
-- 1. CRIAÇÃO DE TIPOS CUSTOMIZADOS (ENUMS)
--------------------------------------------------------------------------------

-- Tipo para a coluna 'status' da tabela users
CREATE TYPE user_status_enum AS ENUM (
    'active', 
    'inactive', 
    'suspended', 
    'past_due', 
    'unpaid', 
    'incomplete', 
    'trial'
);

-- Tipos para a tabela workspace_collaborators
CREATE TYPE collaborator_role_enum AS ENUM (
    'owner', 
    'admin', 
    'editor', 
    'viewer'
);

CREATE TYPE collaborator_status_enum AS ENUM (
    'pending', 
    'accepted', 
    'rejected'
);

CREATE TYPE collaborator_request_type_enum AS ENUM (
    'invitation', 
    'edit_request'
);

-- Tipo para a coluna 'type' da tabela fields
CREATE TYPE field_type_enum AS ENUM (
    'text', 
    'number', 
    'email', 
    'url', 
    'date', 
    'boolean', 
    'json'
);

--------------------------------------------------------------------------------
-- 2. FUNÇÃO TRIGGER PARA 'updated_at'
--------------------------------------------------------------------------------

-- Função para atualizar automaticamente a coluna updated_at
CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS '
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
' LANGUAGE plpgsql;

--------------------------------------------------------------------------------
-- 3. CRIAÇÃO DAS TABELAS (DDL)
--------------------------------------------------------------------------------

-- Tabela users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY, -- BIGSERIAL substitui BIGINT UNSIGNED AUTO_INCREMENT
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30),
    avatar VARCHAR(255) NULL,
    email VARCHAR(40) NOT NULL UNIQUE, -- Adicionando UNIQUE para emails
    email_verified_at TIMESTAMP WITH TIME ZONE NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    language VARCHAR(10) DEFAULT 'pt_BR',
    password TEXT NOT NULL,
    phone VARCHAR(20),
    global_key_api TEXT,
    email_verification_code VARCHAR(255) NULL,
    email_verification_sent_at TIMESTAMP WITH TIME ZONE NULL,
    email_verified BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    
    stripe_id VARCHAR(255) NULL,
    pm_type VARCHAR(255) NULL,
    pm_last_four VARCHAR(4) NULL,
    trial_ends_at TIMESTAMP WITH TIME ZONE NULL,
    stripe_customer_id VARCHAR(255) NULL,
    stripe_subscription_id VARCHAR(255) NULL,
    plan_expires_at BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    "status" user_status_enum DEFAULT 'active', -- Uso do tipo ENUM customizado. Coluna em aspas por ser palavra reservada.
    last_login_at TIMESTAMP WITH TIME ZONE NULL,
    last_login_ip VARCHAR(45) NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    delete_at TIMESTAMP WITH TIME ZONE NULL
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_users_updated_at
BEFORE UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();

-- Tabela user_activities
CREATE TABLE user_activities (
    id BIGSERIAL PRIMARY KEY, -- BIGSERIAL substitui BIGINT UNSIGNED AUTO_INCREMENT
    user_id BIGINT NOT NULL, -- BIGINT substitui BIGINT UNSIGNED
    action VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    metadata JSONB NULL, -- JSONB substitui JSON
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE NULL,
    
    CONSTRAINT user_activities_user_id_foreign 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE
);

-- Tabela type_workspaces
CREATE TABLE type_workspaces (
    id SERIAL PRIMARY KEY,
    description VARCHAR(50) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP -- Não precisa de trigger (sem ON UPDATE no original)
);

-- Tabela type_views_workspaces
CREATE TABLE type_views_workspaces (
    id SERIAL PRIMARY KEY,
    description VARCHAR(50) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP -- Não precisa de trigger
);

-- Tabela workspaces
CREATE TABLE workspaces (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE, -- BIGINT substitui INTEGER (para FK)
    type_workspace_id INTEGER REFERENCES type_workspaces(id),
    type_view_workspace_id INTEGER DEFAULT 1 REFERENCES type_views_workspaces(id),
    title VARCHAR(100) NOT NULL,
    description VARCHAR(250) NULL,
    is_published BOOLEAN DEFAULT FALSE,
    password TEXT DEFAULT NULL,
    workspace_key_api TEXT,
    api_enabled BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    api_domain_restriction BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    api_jwt_required BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_workspaces_updated_at
BEFORE UPDATE ON workspaces
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();


-- Tabela workspace_api_permissions
CREATE TABLE workspace_api_permissions (
    id BIGSERIAL PRIMARY KEY, -- BIGSERIAL substitui BIGINT UNSIGNED AUTO_INCREMENT
    workspace_id BIGINT NOT NULL REFERENCES workspaces(id) ON DELETE CASCADE,
    endpoint VARCHAR(255) NOT NULL,
    allowed_methods JSONB NOT NULL, -- JSONB substitui JSON
    created_at TIMESTAMP WITH TIME ZONE NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE NULL,
    
    UNIQUE (workspace_id, endpoint) -- Restrição UNIQUE
);

-- Tabela workspace_allowed_domains
CREATE TABLE workspace_allowed_domains (
    id BIGSERIAL PRIMARY KEY,
    workspace_id BIGINT NOT NULL REFERENCES workspaces(id) ON DELETE CASCADE,
    domain VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE (workspace_id, domain)
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_workspace_allowed_domains_updated_at
BEFORE UPDATE ON workspace_allowed_domains
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();


-- Tabela workspace_collaborators
CREATE TABLE workspace_collaborators (
    id BIGSERIAL PRIMARY KEY,
    workspace_id BIGINT NOT NULL REFERENCES workspaces(id) ON DELETE CASCADE,
    user_id BIGINT DEFAULT NULL REFERENCES users(id) ON DELETE CASCADE, -- BIGINT substitui bigint UNSIGNED
    "role" collaborator_role_enum NOT NULL DEFAULT 'viewer', -- Uso do tipo ENUM customizado
    invitation_email VARCHAR(255) DEFAULT NULL,
    invitation_token VARCHAR(64) UNIQUE, -- UNIQUE KEY convertido para UNIQUE constraint
    invited_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- BIGINT substitui bigint UNSIGNED
    invited_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL,
    joined_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL,
    "status" collaborator_status_enum NOT NULL DEFAULT 'pending', -- Uso do tipo ENUM customizado
    request_message TEXT NULL,
    requested_at TIMESTAMP WITH TIME ZONE NULL,
    responded_at TIMESTAMP WITH TIME ZONE NULL,
    response_reason TEXT NULL,
    request_type collaborator_request_type_enum NOT NULL DEFAULT 'invitation', -- Uso do tipo ENUM customizado
    created_at TIMESTAMP WITH TIME ZONE NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE NULL -- Não precisa de trigger
);


-- Tabela topics
CREATE TABLE topics (
    id BIGSERIAL PRIMARY KEY, -- BIGSERIAL substitui SERIAL (para FK consistency)
    workspace_id BIGINT REFERENCES workspaces(id) ON DELETE CASCADE,
    title VARCHAR(100) NOT NULL,
    "order" INTEGER NOT NULL, -- Coluna em aspas por ser palavra reservada
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP -- Não precisa de trigger
);


-- Tabela fields
CREATE TABLE fields (
    id BIGSERIAL PRIMARY KEY,
    topic_id BIGINT REFERENCES topics(id) ON DELETE CASCADE, -- BIGINT substitui INTEGER (para FK consistency)
    key_name VARCHAR(200),
    value TEXT,
    "type" field_type_enum DEFAULT 'text', -- Uso do tipo ENUM customizado
    is_visible BOOLEAN DEFAULT TRUE,
    "order" INTEGER NOT NULL, -- Coluna em aspas por ser palavra reservada
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_fields_updated_at
BEFORE UPDATE ON fields
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();


-- Tabela plans
CREATE TABLE plans (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10,2) DEFAULT 0.00,
    max_workspaces INT, -- NULL para planos ilimitados, INT substitui INT UNSIGNED
    max_topics INT, -- INT substitui INT UNSIGNED
    max_fields INT, -- INT substitui INT UNSIGNED
    can_export BOOLEAN DEFAULT FALSE,
    can_use_api BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    api_requests_per_minute INT, -- INT substitui INT UNSIGNED
    api_requests_per_hour INT, -- INT substitui INT UNSIGNED
    api_requests_per_day INT, -- INT substitui INT UNSIGNED
    burst_requests INT, -- INT substitui INT UNSIGNED
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_plans_updated_at
BEFORE UPDATE ON plans
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();


-- Tabela roles
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP WITH TIME ZONE NULL,
    updated_at TIMESTAMP WITH TIME ZONE NULL -- Não precisa de trigger
);

-- Tabela model_has_roles
CREATE TABLE model_has_roles (
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type)
);


-- Tabela notifications
CREATE TABLE notifications (
    id CHAR(36) NOT NULL PRIMARY KEY,
    "type" VARCHAR(255) NOT NULL, -- Coluna em aspas por ser palavra reservada
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL,
    updated_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL
);


-- Tabela password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NULL DEFAULT NULL
);


-- Tabela api_request_logs
CREATE TABLE api_request_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE CASCADE,
    workspace_id BIGINT NULL REFERENCES workspaces(id) ON DELETE CASCADE,
    ip_address VARCHAR(45) NOT NULL,
    method VARCHAR(10) NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    response_code INT NOT NULL,
    response_time INT NOT NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Aplica o trigger ON UPDATE CURRENT_TIMESTAMP
CREATE TRIGGER update_api_request_logs_updated_at
BEFORE UPDATE ON api_request_logs
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();

-- Adiciona o comentário/descrição da tabela
COMMENT ON TABLE api_request_logs IS 'Tabela para logging de requisições da API com rate limiting';
COMMENT ON COLUMN api_request_logs.response_time IS 'em milissegundos';


--------------------------------------------------------------------------------
-- 4. CRIAÇÃO DE ÍNDICES ADICIONAIS
--------------------------------------------------------------------------------

-- user_activities
CREATE INDEX user_activities_user_id_created_at_index ON user_activities (user_id, created_at);
CREATE INDEX user_activities_action_index ON user_activities (action);
CREATE INDEX user_activities_created_at_index ON user_activities (created_at);

-- workspace_api_permissions
CREATE INDEX workspace_api_permissions_workspace_id_index ON workspace_api_permissions (workspace_id);
CREATE INDEX workspace_api_permissions_endpoint_index ON workspace_api_permissions (endpoint);

-- workspace_allowed_domains
CREATE INDEX idx_domain ON workspace_allowed_domains(domain);

-- workspace_collaborators
CREATE INDEX workspace_collaborators_workspace_id_user_id_index ON workspace_collaborators (workspace_id, user_id);
CREATE INDEX workspace_collaborators_invitation_token_index ON workspace_collaborators (invitation_token);
CREATE INDEX workspace_collaborators_status_index ON workspace_collaborators ("status");
CREATE INDEX workspace_collaborators_user_id_foreign ON workspace_collaborators (user_id);
CREATE INDEX workspace_collaborators_invited_by_foreign ON workspace_collaborators (invited_by);
CREATE INDEX workspace_collaborators_invitation_email_index ON workspace_collaborators (invitation_email);

-- notifications
CREATE INDEX notifications_notifiable_type_notifiable_id_index ON notifications (notifiable_type, notifiable_id);

-- password_reset_tokens
CREATE INDEX password_reset_tokens_token_index ON password_reset_tokens (token);

-- api_request_logs
CREATE INDEX api_request_logs_user_id_created_at_index ON api_request_logs (user_id, created_at);
CREATE INDEX api_request_logs_ip_address_created_at_index ON api_request_logs (ip_address, created_at);
CREATE INDEX api_request_logs_created_at_index ON api_request_logs (created_at);
CREATE INDEX api_request_logs_response_code_index ON api_request_logs (response_code);
CREATE INDEX api_request_logs_method_index ON api_request_logs (method);
CREATE INDEX api_request_logs_user_id_index ON api_request_logs (user_id);
CREATE INDEX api_request_logs_workspace_id_index ON api_request_logs (workspace_id);
CREATE INDEX api_request_logs_workspace_response_index ON api_request_logs (workspace_id, response_code, created_at);

--------------------------------------------------------------------------------
-- 5. INSERÇÃO DE DADOS INICIAIS (DML)
--------------------------------------------------------------------------------

-- Inserir data into type_workspaces
INSERT INTO type_workspaces (id, description) VALUES 
(1, 'Tópico Único'),
(2, 'Um ou Mais Tópicos')
ON CONFLICT (id) DO UPDATE SET description = EXCLUDED.description;

-- Garante que o sequence do SERIAL continue do maior ID inserido
SELECT setval('type_workspaces_id_seq', (SELECT MAX(id) FROM type_workspaces));

-- Inserir data into type_views_workspaces
INSERT INTO type_views_workspaces (id, description) VALUES 
(1, 'Interface da API'),
(2, 'Geev API JSON')
ON CONFLICT (id) DO UPDATE SET description = EXCLUDED.description;

SELECT setval('type_views_workspaces_id_seq', (SELECT MAX(id) FROM type_views_workspaces));


-- Inserir data into plans
INSERT INTO plans (
    name, 
    price, 
    max_workspaces, 
    max_topics, 
    max_fields, 
    can_export, 
    can_use_api, 
    api_requests_per_minute, 
    api_requests_per_hour, 
    api_requests_per_day, 
    burst_requests
) VALUES
-- Plano Free
('free', 0.00, 1, 3, 10, FALSE, FALSE, 30, 500, 2000, 5),
-- Plano Start
('start', 10.00, 3, 10, 50, TRUE, TRUE, 60, 2000, 10000, 15),
-- Plano Pro
('pro', 32.00, 10, 30, 200, TRUE, TRUE, 120, 5000, 50000, 25),
-- Plano Premium: usa NULL para limites ilimitados/altos
('premium', 70.90, NULL, NULL, NULL, TRUE, TRUE, 250, 25000, 250000, 50),
-- Admin: usa NULL para limites ilimitados/altos
('admin', 0.00, NULL, NULL, NULL, TRUE, TRUE, 1000, NULL, NULL, 200)
ON CONFLICT (name) DO UPDATE SET 
    price = EXCLUDED.price,
    max_workspaces = EXCLUDED.max_workspaces,
    max_topics = EXCLUDED.max_topics,
    max_fields = EXCLUDED.max_fields,
    can_export = EXCLUDED.can_export,
    can_use_api = EXCLUDED.can_use_api,
    api_requests_per_minute = EXCLUDED.api_requests_per_minute,
    api_requests_per_hour = EXCLUDED.api_requests_per_hour,
    api_requests_per_day = EXCLUDED.api_requests_per_day,
    burst_requests = EXCLUDED.burst_requests;


--------------------------------------------------------------------------------
-- 6. CRIAÇÃO DE FUNÇÕES E VIEWS
--------------------------------------------------------------------------------

-- FUNCTION: CleanupOldApiLogs
-- Converte o PROCEDURE MySQL em uma FUNCTION PL/pgSQL
CREATE OR REPLACE FUNCTION CleanupOldApiLogs(retention_days INT)
RETURNS void
LANGUAGE plpgsql
AS $$
BEGIN
    DELETE FROM api_request_logs 
    -- Usa subtração de intervalo para calcular datas
    WHERE created_at < NOW() - (retention_days || ' days')::INTERVAL; 
END;
$$;


-- VIEW: api_usage_stats
CREATE OR REPLACE VIEW api_usage_stats AS
SELECT 
    DATE(created_at) as date,
    user_id,
    workspace_id,
    COUNT(*) as total_requests,
    COUNT(CASE WHEN response_code >= 400 THEN 1 END) as failed_requests,
    AVG(response_time) as avg_response_time,
    MAX(response_time) as max_response_time
FROM api_request_logs
GROUP BY DATE(created_at), user_id, workspace_id;


--------------------------------------------------------------------------------
-- 7. EXEMPLOS DE QUERIES (SELECTS)
--------------------------------------------------------------------------------

SELECT * FROM users LIMIT 10;
SELECT * FROM user_activities LIMIT 10;
SELECT * FROM workspaces LIMIT 10;
SELECT * FROM fields LIMIT 10;
SELECT * FROM plans ORDER BY price ASC;
SELECT * FROM roles LIMIT 10;
SELECT * FROM model_has_roles LIMIT 10;
SELECT * FROM notifications LIMIT 10;
SELECT * FROM password_reset_tokens LIMIT 10;
SELECT * FROM workspace_collaborators LIMIT 10;
SELECT * FROM api_request_logs LIMIT 10;

-- Exemplo de query para verificar roles do usuário (simulação de joins)
SELECT 
    u.id as user_id,
    u.email,
    u.name,
    r.name as role_name
FROM users u
LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\Models\User'
LEFT JOIN roles r ON r.id = mhr.role_id;

-- FIM DO SCRIPT
RESET client_min_messages;