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
    email_verified BOOLEAN NOT NULL DEFAULT FALSE, -- BOOLEAN substitui TINYINT(1)
    email_verified_at TIMESTAMP WITH TIME ZONE NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    language VARCHAR(10) DEFAULT 'pt_BR',
    password TEXT NOT NULL,
    remember_token VARCHAR(100) NULL,
    phone VARCHAR(20),
    global_key_api TEXT,
    email_verification_code VARCHAR(255) NULL,
    email_verification_sent_at TIMESTAMP WITH TIME ZONE NULL,

    -- Colunas para autenticação social (Google)
    google_id VARCHAR(255) NULL,
    provider_name VARCHAR(50) NULL,
    
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

-- Índices para autenticação social
CREATE INDEX idx_users_google_id ON users(google_id);
CREATE INDEX idx_users_email_provider ON users(email, provider_name);

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
    structure_id BIGINT REFERENCES structures(id) ON DELETE SET NULL,
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
    workspaces INT, -- NULL para planos ilimitados, INT substitui INT UNSIGNED
    topics INT, -- INT substitui INT UNSIGNED
    fields INT, -- INT substitui INT UNSIGNED
    structures INT, -- INT substitui INT UNSIGNED
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
(2, 'API REST JSON')
ON CONFLICT (id) DO UPDATE SET description = EXCLUDED.description;

SELECT setval('type_views_workspaces_id_seq', (SELECT MAX(id) FROM type_views_workspaces));


-- Inserir data into plans
INSERT INTO plans (
    name, 
    price, 
    structures,
    workspaces, 
    topics, 
    fields,
    domains, 
    can_export, 
    can_use_api, 
    api_requests_per_minute, 
    api_requests_per_hour, 
    api_requests_per_day, 
    burst_requests
) VALUES
-- Plano Free
('free', 0.00, 1, 3, 10, 1, FALSE, FALSE, 30, 500, 2000, 5),
-- Plano Start
('start', 10.00, 3, 10, 50, 5, TRUE, TRUE, 60, 2000, 10000, 15),
-- Plano Pro
('pro', 32.00, 10, 30, 200, 20, TRUE, TRUE, 120, 5000, 50000, 25),
-- Plano Premium: usa NULL para limites ilimitados/altos
('premium', 70.90, NULL, NULL, NULL, NULL, TRUE, TRUE, 250, 25000, 250000, 50),
-- Admin: usa NULL para limites ilimitados/altos
('admin', 0.00, NULL, NULL, NULL, TRUE, TRUE, 1000, NULL, NULL, 200)
ON CONFLICT (name) DO UPDATE SET 
    price = EXCLUDED.price,
    structures = EXCLUDED.structures,
    workspaces = EXCLUDED.workspaces,
    topics = EXCLUDED.topics,
    fields = EXCLUDED.fields,
    domains = EXCLUDED.domains,
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




-- ==============================================
-- SISTEMA DE ESTRUTURAS (MODELOS) DO HANDGEEV
-- ==============================================

-- ----------------------------------------------------------------------
-- FUNÇÃO UPDATE_TIMESTAMP (CORREÇÃO)
-- ----------------------------------------------------------------------

CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ----------------------------------------------------------------------
-- TIPOS ENUM PARA AS ESTRUTURAS
-- ----------------------------------------------------------------------

CREATE TYPE field_data_type_enum AS ENUM (
    'text', 
    'number', 
    'decimal', 
    'boolean', 
    'date', 
    'datetime', 
    'email', 
    'url', 
    'json'
);

-- ----------------------------------------------------------------------
-- TABELA PRINCIPAL DE ESTRUTURAS (MODELOS)
-- ----------------------------------------------------------------------

CREATE TABLE structures (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Comentários descritivos
COMMENT ON TABLE structures IS 'Modelos de estrutura de dados criados por usuários no HandGeev';
COMMENT ON COLUMN structures.id IS 'Identificador único da estrutura';
COMMENT ON COLUMN structures.user_id IS 'Usuário criador da estrutura';
COMMENT ON COLUMN structures.name IS 'Nome da estrutura (ex: Produto, Cliente, Pedido)';
COMMENT ON COLUMN structures.description IS 'Descrição opcional sobre a finalidade da estrutura';
COMMENT ON COLUMN structures.is_public IS 'Se a estrutura pode ser usada por outros usuários';
COMMENT ON COLUMN structures.created_at IS 'Data de criação da estrutura';
COMMENT ON COLUMN structures.updated_at IS 'Data da última atualização da estrutura';

-- ----------------------------------------------------------------------
-- TABELA DE CAMPOS DAS ESTRUTURAS
-- ----------------------------------------------------------------------

CREATE TABLE structure_fields (
    id BIGSERIAL PRIMARY KEY,
    structure_id BIGINT NOT NULL REFERENCES structures(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    type field_data_type_enum NOT NULL DEFAULT 'text',
    default_value TEXT,
    is_required BOOLEAN DEFAULT FALSE,
    "order" INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Comentários descritivos
COMMENT ON TABLE structure_fields IS 'Campos que definem a estrutura (modelo) de dados';
COMMENT ON COLUMN structure_fields.structure_id IS 'Estrutura à qual este campo pertence';
COMMENT ON COLUMN structure_fields.name IS 'Nome do campo (ex: nome, preco, ativo)';
COMMENT ON COLUMN structure_fields.type IS 'Tipo de dados do campo';
COMMENT ON COLUMN structure_fields.default_value IS 'Valor padrão do campo';
COMMENT ON COLUMN structure_fields.is_required IS 'Se o campo é obrigatório';
COMMENT ON COLUMN structure_fields."order" IS 'Ordem de exibição do campo';
COMMENT ON COLUMN structure_fields.created_at IS 'Data de criação do campo';
COMMENT ON COLUMN structure_fields.updated_at IS 'Data da última atualização do campo';

-- ----------------------------------------------------------------------
-- TABELA DE VALORES DOS TÓPICOS BASEADOS EM ESTRUTURAS
-- ----------------------------------------------------------------------

CREATE TABLE topic_field_values (
    id BIGSERIAL PRIMARY KEY,
    topic_id BIGINT NOT NULL REFERENCES topics(id) ON DELETE CASCADE,
    structure_field_id BIGINT NOT NULL REFERENCES structure_fields(id) ON DELETE CASCADE,
    
    field_value TEXT, -- sempre texto, mesmo que numerico ou boolean
    
    is_visible BOOLEAN DEFAULT TRUE,
    order_index INTEGER DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_topic_field_values_topic_id ON topic_field_values(topic_id);
CREATE INDEX idx_topic_field_values_structure_field_id ON topic_field_values(structure_field_id);

-- ----------------------------------------------------------------------
-- TABELA PARA GRAVAR DADOS DOS CAMPOS PREENCHIDOS NOS TOPICOS
-- ----------------------------------------------------------------------

CREATE TABLE topic_records (
    id BIGSERIAL PRIMARY KEY,
    topic_id BIGINT NOT NULL,
    "order" INTEGER DEFAULT 1,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_topic_records_topic_id
        FOREIGN KEY (topic_id)
        REFERENCES topics(id)
        ON DELETE CASCADE
);

-- ----------------------------------------------------------------------
-- ATUALIZAÇÃO DA TABELA TOPICS PARA VINCULAR ESTRUTURAS
-- ----------------------------------------------------------------------

-- Remove a coluna structure_id existente se houver (para recriar corretamente)
ALTER TABLE topics DROP COLUMN IF EXISTS structure_id;

-- Adiciona a coluna structure_id corretamente
ALTER TABLE topics 
ADD COLUMN structure_id BIGINT REFERENCES structures(id) ON DELETE SET NULL;

-- Comentário da coluna
COMMENT ON COLUMN topics.structure_id IS 'Estrutura vinculada ao tópico (NULL para tópicos livres)';

-- ----------------------------------------------------------------------
-- ÍNDICES PARA PERFORMANCE
-- ----------------------------------------------------------------------

-- Índices para structures
CREATE INDEX idx_structures_user_id ON structures(user_id);
CREATE INDEX idx_structures_name ON structures(name);
CREATE INDEX idx_structures_created_at ON structures(created_at);

-- Índices para structure_fields
CREATE INDEX idx_structure_fields_structure_id ON structure_fields(structure_id);
CREATE INDEX idx_structure_fields_order ON structure_fields(structure_id, "order");
CREATE INDEX idx_structure_fields_name ON structure_fields(name);

-- Índice para topics com structure_id
CREATE INDEX idx_topics_structure_id ON topics(structure_id);
CREATE INDEX idx_topics_workspace_structure ON topics(workspace_id, structure_id);

-- ----------------------------------------------------------------------
-- TRIGGERS PARA ATUALIZAÇÃO AUTOMÁTICA DO updated_at
-- ----------------------------------------------------------------------

-- Trigger para structures
CREATE TRIGGER update_structures_updated_at
    BEFORE UPDATE ON structures
    FOR EACH ROW
    EXECUTE FUNCTION update_timestamp();

-- Trigger para structure_fields
CREATE TRIGGER update_structure_fields_updated_at
    BEFORE UPDATE ON structure_fields
    FOR EACH ROW
    EXECUTE FUNCTION update_timestamp();
    
-- ----------------------------------------------------------------------
-- VIEWS ÚTEIS PARA CONSULTAS
-- ----------------------------------------------------------------------

-- View para estruturas com contagem de campos e tópicos
CREATE OR REPLACE VIEW structures_with_counts AS
SELECT 
    s.*,
    COUNT(DISTINCT sf.id) as fields_count,
    COUNT(DISTINCT t.id) as topics_count,
    u.name as user_name,
    u.email as user_email
FROM structures s
LEFT JOIN structure_fields sf ON sf.structure_id = s.id
LEFT JOIN topics t ON t.structure_id = s.id
LEFT JOIN users u ON u.id = s.user_id
GROUP BY s.id, u.id;


-- ----------------------------------------------------------------------
-- TABLE RECORD FIELD VALUES
-- ----------------------------------------------------------------------

CREATE TABLE public.record_field_values (
	id bigserial NOT NULL,
	record_id int8 NOT NULL,
	structure_field_id int8 NOT NULL,
	field_value text NULL,
	created_at timestamptz DEFAULT CURRENT_TIMESTAMP NULL,
	updated_at timestamptz DEFAULT CURRENT_TIMESTAMP NULL,
	CONSTRAINT record_field_values_pkey PRIMARY KEY (id)
);
CREATE INDEX idx_record_field_values_field_id ON public.record_field_values USING btree (structure_field_id);
CREATE INDEX idx_record_field_values_record_id ON public.record_field_values USING btree (record_id);

-- Table Triggers

create trigger update_record_field_values_updated_at before
update
    on
    public.record_field_values for each row execute function update_timestamp();


-- public.record_field_values chaves estrangeiras

ALTER TABLE public.record_field_values ADD CONSTRAINT record_field_values_record_id_fkey FOREIGN KEY (record_id) REFERENCES public.topic_records(id) ON DELETE CASCADE;

-- ----------------------------------------------------------------------
-- FUNÇÕES ÚTEIS
-- ----------------------------------------------------------------------

-- Função para clonar uma estrutura
CREATE OR REPLACE FUNCTION clone_structure(
    source_structure_id BIGINT,
    new_name VARCHAR(150),
    target_user_id BIGINT
) 
RETURNS BIGINT
LANGUAGE plpgsql
AS $$
DECLARE
    new_structure_id BIGINT;
BEGIN
    -- Insere a nova estrutura
    INSERT INTO structures (user_id, name, description, is_public)
    SELECT target_user_id, new_name, description, false
    FROM structures 
    WHERE id = source_structure_id
    RETURNING id INTO new_structure_id;
    
    -- Clona os campos
    INSERT INTO structure_fields (structure_id, name, type, default_value, is_required, "order")
    SELECT new_structure_id, name, type, default_value, is_required, "order"
    FROM structure_fields 
    WHERE structure_id = source_structure_id;
    
    RETURN new_structure_id;
END;
$$;


-- Tabela input_connections
CREATE TABLE input_connections (
    id SERIAL PRIMARY KEY,
    workspace_id INTEGER NOT NULL REFERENCES workspaces(id) ON DELETE CASCADE,
    structure_id INTEGER NOT NULL REFERENCES structures(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    trigger_field_id INTEGER REFERENCES structure_fields(id) ON DELETE SET NULL,
    execution_order INTEGER DEFAULT 1,
    timeout_seconds INTEGER DEFAULT 30,
    prevent_loops BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Índices para input_connections
CREATE INDEX idx_input_connections_workspace_id ON input_connections(workspace_id);
CREATE INDEX idx_input_connections_structure_id ON input_connections(structure_id);
CREATE INDEX idx_input_connections_trigger_field_id ON input_connections(trigger_field_id);

-- Migration para input_connection_sources
CREATE TABLE IF NOT EXISTS input_connection_sources (
    id SERIAL PRIMARY KEY,
    input_connection_id INTEGER NOT NULL REFERENCES input_connections(id) ON DELETE CASCADE,
    source_type VARCHAR(50) NOT NULL,
    config JSONB NOT NULL DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para input_connection_sources
CREATE INDEX idx_input_connection_sources_input_connection_id ON input_connection_sources(input_connection_id);
CREATE INDEX idx_input_connection_sources_type ON input_connection_sources(type);

-- Tabela input_connection_mappings
CREATE TABLE input_connection_mappings (
    id SERIAL PRIMARY KEY,
    input_connection_id INTEGER NOT NULL REFERENCES input_connections(id) ON DELETE CASCADE,
    source_field VARCHAR(255) NOT NULL,
    target_field_id INTEGER NOT NULL REFERENCES structure_fields(id) ON DELETE CASCADE,
    transformation_type VARCHAR(50),
    is_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

-- Índices para input_connection_mappings
CREATE INDEX idx_input_connection_mappings_input_connection_id ON input_connection_mappings(input_connection_id);
CREATE INDEX idx_input_connection_mappings_target_field_id ON input_connection_mappings(target_field_id);

-- Tabela input_connection_logs
CREATE TABLE input_connection_logs (
    id SERIAL PRIMARY KEY,
    input_connection_id INTEGER NOT NULL REFERENCES input_connections(id) ON DELETE CASCADE,
    topic_id INTEGER NOT NULL REFERENCES topics(id) ON DELETE CASCADE,
    status VARCHAR(20) NOT NULL CHECK (status IN ('success', 'error', 'pending')),
    response_data JSONB,
    error_message TEXT,
    executed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
);

-- Índices para input_connection_logs
CREATE INDEX idx_input_connection_logs_input_connection_id ON input_connection_logs(input_connection_id);
CREATE INDEX idx_input_connection_logs_topic_id ON input_connection_logs(topic_id);
CREATE INDEX idx_input_connection_logs_status ON input_connection_logs(status);
CREATE INDEX idx_input_connection_logs_executed_at ON input_connection_logs(executed_at);


-- ----------------------------------------------------------------------
-- INSERÇÃO DE DADOS EXEMPLO (OPCIONAL)
-- ----------------------------------------------------------------------

-- Inserir estruturas de exemplo (apenas se não existirem)
INSERT INTO structures (user_id, name, description, is_public) VALUES 
(1, 'Produto', 'Modelo para cadastro de produtos', true),
(1, 'Cliente', 'Modelo para cadastro de clientes', true),
(1, 'Pedido', 'Modelo para registro de pedidos', false)
ON CONFLICT DO NOTHING;

-- Inserir campos para a estrutura "Produto"
INSERT INTO structure_fields (structure_id, name, type, default_value, is_required, "order") VALUES 
(1, 'nome', 'text', NULL, true, 1),
(1, 'preco', 'decimal', '0.00', true, 2),
(1, 'ativo', 'boolean', 'true', false, 3),
(1, 'categoria', 'text', NULL, false, 4),
(1, 'descricao', 'text', NULL, false, 5)
ON CONFLICT DO NOTHING;

-- Inserir campos para a estrutura "Cliente"
INSERT INTO structure_fields (structure_id, name, type, default_value, is_required, "order") VALUES 
(2, 'nome', 'text', NULL, true, 1),
(2, 'email', 'email', NULL, true, 2),
(2, 'telefone', 'text', NULL, false, 3),
(2, 'endereco', 'text', NULL, false, 4)
ON CONFLICT DO NOTHING;

-- ----------------------------------------------------------------------
-- CONSULTAS DE EXEMPLO
-- ----------------------------------------------------------------------

-- Exemplo 1: Listar todas as estruturas de um usuário com contagens
SELECT 
    name,
    description,
    fields_count,
    topics_count,
    created_at
FROM structures_with_counts 
WHERE user_id = 1;

-- Exemplo 2: Ver campos de uma estrutura específica
SELECT 
    name,
    type,
    default_value,
    is_required,
    "order"
FROM structure_fields 
WHERE structure_id = 1 
ORDER BY "order";

-- Exemplo 3: Dados completos de um tópico estruturado
SELECT 
    field_name,
    field_type,
    field_value,
    is_required
FROM structured_topic_data 
WHERE topic_id = 1;

-- Exemplo 4: Tópicos livres vs estruturados por workspace
SELECT 
    w.title as workspace,
    COUNT(CASE WHEN t.structure_id IS NULL THEN 1 END) as topicos_livres,
    COUNT(CASE WHEN t.structure_id IS NOT NULL THEN 1 END) as topicos_estruturados,
    COUNT(*) as total_topicos
FROM workspaces w
LEFT JOIN topics t ON t.workspace_id = w.id
GROUP BY w.id, w.title;