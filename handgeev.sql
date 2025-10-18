-- NEW DATABASE HANDGEEV
CREATE DATABASE handgeev;
USE handgeev;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30),
    avatar VARCHAR(255) NULL,
    email VARCHAR(40) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    language VARCHAR(10) DEFAULT 'pt_BR',
    password TEXT NOT NULL,
    phone VARCHAR(20),
    global_key_api TEXT,
    email_verification_code VARCHAR(255) NULL,
    email_verification_sent_at TIMESTAMP NULL,
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    
    stripe_id VARCHAR(255) NULL,
    pm_type VARCHAR(255) NULL,
    pm_last_four VARCHAR(4) NULL,
    trial_ends_at TIMESTAMP NULL,
	 stripe_customer_id VARCHAR(255) NULL,
	 stripe_subscription_id VARCHAR(255) NULL,
    plan_expires_at TIMESTAMP TINYINT(1) NOT NULL DEFAULT 0,
	 status ENUM('active', 'inactive', 'suspended', 'past_due', 'unpaid', 'incomplete', 'trial') DEFAULT 'active',
	 
	 last_login_at TIMESTAMP NULL AFTER,
	 last_login_ip VARCHAR(45) NULL AFTER,
    
	 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_plan_id) REFERENCES plans(id),
    INDEX idx_email (email),
    INDEX idx_status (STATUS),
    INDEX `users_stripe_id_index` (`stripe_id`)
) ENGINE=INNODB;
SELECT * FROM users;
-- DROP TABLE users;


-- Criar tabela user_activities
CREATE TABLE user_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign key constraint
    CONSTRAINT user_activities_user_id_foreign 
        FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE,
    
    -- Índices
    INDEX user_activities_user_id_created_at_index (user_id, created_at),
    INDEX user_activities_action_index (ACTION),
    INDEX user_activities_created_at_index (created_at)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;




CREATE TABLE type_workspaces (
	id SERIAL PRIMARY KEY,
	description VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO type_workspaces (id, description) VALUES (1, 'Tópico Único');
INSERT INTO type_workspaces (id, description) VALUES (2, 'Um ou Mais Tópicos');

CREATE TABLE type_views_workspaces (
	id SERIAL PRIMARY KEY,
	description VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO type_views_workspaces (id, description) VALUES (1, 'Interface da API');
INSERT INTO type_views_workspaces (id, description) VALUES (2, 'API REST JSON');



CREATE TABLE workspaces (
	 id SERIAL PRIMARY KEY,
	 user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
	 type_workspace_id INTEGER REFERENCES type_workspaces(id),
	 type_view_workspace_id INTEGER DEFAULT 1 REFERENCES type_views_workspaces(id),
	 title VARCHAR(100) NOT NULL,
	 description VARCHAR(250) NULL,
	 is_published BOOLEAN DEFAULT FALSE,
	 password TEXT DEFAULT NULL,
	 workspace_key_api TEXT,
	 api_enabled TINYINT(1) NOT NULL DEFAULT 0,
	 api_domain_restriction TINYINT(1) NOT NULL DEFAULT 0,
	 api_jwt_required TINYINT(1) NOT NULL DEFAULT 0,
	 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- DROP TABLE workspaces
SELECT * FROM workspaces;


-- Criação da tabela workspace_api_permissions
CREATE TABLE workspace_api_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workspace_id BIGINT UNSIGNED NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    allowed_methods JSON NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    -- Chave estrangeira
    CONSTRAINT workspace_api_permissions_workspace_id_foreign 
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    
    -- Índice único
    UNIQUE KEY workspace_api_permissions_workspace_id_endpoint_unique (workspace_id, endpoint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Índices adicionais para melhor performance
CREATE INDEX workspace_api_permissions_workspace_id_index ON workspace_api_permissions (workspace_id);
CREATE INDEX workspace_api_permissions_endpoint_index ON workspace_api_permissions (ENDPOINT);




CREATE TABLE workspace_allowed_domains (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    workspace_id BIGINT UNSIGNED NOT NULL,
    domain VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id) ON DELETE CASCADE,
    UNIQUE KEY unique_workspace_domain (workspace_id, domain)
);
-- Índice para buscas por domínio
CREATE INDEX idx_domain ON workspace_allowed_domains(domain);
SELECT * FROM workspace_allowed_domains;


-- Tabela workspace_collaborators
CREATE TABLE `workspace_collaborators` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `role` enum('owner','admin','editor','viewer') NOT NULL DEFAULT 'viewer',
  `invitation_email` varchar(255) DEFAULT NULL,
  `invitation_token` varchar(64) DEFAULT NULL,
  `invited_by` bigint UNSIGNED NOT NULL,
  `invited_at` timestamp NULL DEFAULT NULL,
  `joined_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `request_message` TEXT NULL,
  `requested_at` TIMESTAMP NULL,
  `responded_at` TIMESTAMP NULL,
  `response_reason` TEXT NULL,
  `request_type` ENUM('invitation', 'edit_request') NOT NULL DEFAULT 'invitation',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspace_collaborators_invitation_token_unique` (`invitation_token`),
  KEY `workspace_collaborators_workspace_id_user_id_index` (`workspace_id`,`user_id`),
  KEY `workspace_collaborators_invitation_token_index` (`invitation_token`),
  KEY `workspace_collaborators_status_index` (`status`),
  KEY `workspace_collaborators_user_id_foreign` (`user_id`),
  KEY `workspace_collaborators_invited_by_foreign` (`invited_by`),
  KEY `workspace_collaborators_invitation_email_index` (`invitation_email`),
  CONSTRAINT `workspace_collaborators_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workspace_collaborators_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workspace_collaborators_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;
SELECT * FROM workspace_collaborators;




CREATE TABLE topics (
	id SERIAL PRIMARY KEY,
	workspace_id INTEGER REFERENCES workspaces(id) ON DELETE CASCADE,
	title VARCHAR(100) NOT NULL,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DROP TABLE topics;
SELECT * FROM topics;


CREATE TABLE fields (
	id SERIAL PRIMARY KEY,
	topic_id INTEGER REFERENCES topics(id) ON DELETE CASCADE,
	key_name VARCHAR(200),
	value TEXT,
	type ENUM('text', 'number', 'email', 'url', 'date', 'boolean', 'json') DEFAULT 'text',
	is_visible BOOLEAN DEFAULT TRUE,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT * FROM FIELDS


-- Tabela de planos simplificada
CREATE TABLE plans (
    id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10,2) DEFAULT 0.00,
    max_workspaces INT UNSIGNED DEFAULT 1,
    max_topics INT UNSIGNED DEFAULT 3,
    max_fields INT UNSIGNED DEFAULT 10,
    can_export BOOLEAN DEFAULT FALSE,
    can_use_api BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    api_requests_per_minute INT UNSIGNED DEFAULT 60,
    api_requests_per_hour INT UNSIGNED DEFAULT 1000,
    api_requests_per_day INT UNSIGNED DEFAULT 10000,
    burst_requests INT UNSIGNED DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;
SELECT * FROM plans;


-- Inserir planos básicos
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
-- Plano Free: para teste básico
('free', 0.00, 1, 3, 10, FALSE, FALSE, 30, 500, 2000, 5),
-- Plano Start: pequenos negócios
('start', 10.00, 3, 10, 50, TRUE, TRUE, 60, 2000, 10000, 15),
-- Plano Pro: negócios estabelecidos  
('pro', 32.00, 10, 30, 200, TRUE, TRUE, 120, 5000, 50000, 25),
-- Plano Premium: empresas
('premium', 70.90, NULL, NULL, NULL, TRUE, TRUE, 250, 25000, 250000, 50),
-- Admin: uso interno
('admin', 0.00, NULL, NULL, NULL, TRUE, TRUE, 1000, NULL, NULL, 200);



SELECT * FROM plans;
SELECT * FROM users;
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
SELECT * FROM roles;




CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
SELECT * FROM model_has_roles;


CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;
SELECT * FROM notifications;


CREATE TABLE `password_reset_tokens` (
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`email`),
    KEY `password_reset_tokens_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_UNICODE_CI;

SELECT * FROM password_reset_tokens;


CREATE TABLE `api_request_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `workspace_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `method` VARCHAR(10) NOT NULL,
    `endpoint` VARCHAR(255) NOT NULL,
    `response_code` INT NOT NULL,
    `response_time` INT NOT NULL COMMENT 'em milissegundos',
    `user_agent` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    CONSTRAINT `api_request_logs_user_id_foreign` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE,
        
    CONSTRAINT `api_request_logs_workspace_id_foreign` 
        FOREIGN KEY (`workspace_id`) 
        REFERENCES `workspaces` (`id`) 
        ON DELETE CASCADE,
    
    -- Indexes
    INDEX `api_request_logs_user_id_created_at_index` (`user_id`, `created_at`),
    INDEX `api_request_logs_ip_address_created_at_index` (`ip_address`, `created_at`),
    INDEX `api_request_logs_created_at_index` (`created_at`),
    
    -- Indexes adicionais para otimização
    INDEX `api_request_logs_response_code_index` (`response_code`),
    INDEX `api_request_logs_method_index` (`method`),
    INDEX `api_request_logs_user_id_index` (`user_id`),
    INDEX `api_request_logs_workspace_id_index` (`workspace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Comentários para documentação
ALTER TABLE `api_request_logs` 
    COMMENT = 'Tabela para logging de requisições da API com rate limiting';
    
    
    
    
    
-- Índices adicionais para queries de analytics
-- CREATE INDEX `api_request_logs_date_method_index` ON `api_request_logs` (DATE(created_at), method);
CREATE INDEX `api_request_logs_workspace_response_index` ON `api_request_logs` (workspace_id, response_code, created_at);
-- Índice para limpeza de logs antigos
-- CREATE INDEX `api_request_logs_old_logs_index` ON `api_request_logs` (created_at) WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);



DELIMITER //

CREATE PROCEDURE `CleanupOldApiLogs`(IN retention_days INT)
BEGIN
    DELETE FROM `api_request_logs` 
    WHERE `created_at` < DATE_SUB(NOW(), INTERVAL retention_days DAY);
END //

DELIMITER ;



-- CREATE VIEW `api_usage_stats` AS
SELECT 
    DATE(created_at) as date,
    user_id,
    workspace_id,
    COUNT(*) as total_requests,
    COUNT(CASE WHEN response_code >= 400 THEN 1 END) as failed_requests,
    AVG(response_time) as avg_response_time,
    MAX(response_time) as max_response_time
FROM `api_request_logs`
GROUP BY DATE(created_at), user_id, workspace_id;
CREATE VIEW `api_usage_stats` AS
SELECT 
    DATE(created_at) as date,
    user_id,
    workspace_id,
    COUNT(*) as total_requests,
    COUNT(CASE WHEN response_code >= 400 THEN 1 END) as failed_requests,
    AVG(response_time) as avg_response_time,
    MAX(response_time) as max_response_time
FROM `api_request_logs`
GROUP BY DATE(created_at), user_id, workspace_id;





SELECT * FROM notifications;
SELECT * FROM workspace_collaborators;
SELECT 
  w.id as workspace_id,
  w.user_id as user_id,
  'owner' as role,
  w.user_id as invited_by,
  w.created_at as invited_at,
  w.created_at as joined_at,
  'accepted' as status,
  NOW() as created_at,
  NOW() as updated_at
FROM `workspaces` w
LEFT JOIN `workspace_collaborators` wc ON w.id = wc.workspace_id AND w.user_id = wc.user_id
WHERE wc.id IS NULL;
SELECT * FROM model_has_permissions;
SELECT * FROM role_has_permissions;
SELECT * FROM permissions;
-- DROPS
-- DROP TABLE fields;
-- DROP TABLE topics;
-- DROP TABLE workspaces;
-- DROP TABLE type_workspaces;
-- Verificar usuários e seus perfis
SELECT 
	mhr.model_id,
    u.id as user_id,
    u.email,
    u.name,
    r.name as role_name
FROM users u
LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\Models\User'
LEFT JOIN roles r ON r.id = mhr.role_id;
-- Verificar quais roles cada usuário tem
SELECT * FROM roles WHERE name = 'admin';