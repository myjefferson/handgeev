CREATE DATABASE portfoline;
USE portfoline;

-- NEW DATABASE PORTFOLINE
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
    global_hash_api TEXT,
--    current_plan_id TINYINT UNSIGNED DEFAULT 1,
    plan_expires_at TIMESTAMP NULL,
	 status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- FOREIGN KEY (current_plan_id) REFERENCES plans(id),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

SELECT * FROM users;
-- DROP TABLE users;


CREATE TABLE type_workspaces (
	id SERIAL PRIMARY KEY,
	description VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



INSERT INTO type_workspaces (id, description) VALUES (1, 'Tópico Único');
INSERT INTO type_workspaces (id, description) VALUES (2, 'Um ou Mais Tópicos');


CREATE TABLE workspaces (
	 id SERIAL PRIMARY KEY,
	 user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
	 type_workspace_id INTEGER REFERENCES type_workspaces(id),
	 title VARCHAR(100) NOT NULL,
	 is_published BOOLEAN DEFAULT FALSE,
	 workspace_hash_api TEXT,
	 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DROP TABLE workspaces

SELECT * FROM workspaces;

CREATE TABLE topics (
	id SERIAL PRIMARY KEY,
	workspace_id INTEGER REFERENCES workspaces(id) ON DELETE CASCADE,
	title VARCHAR(100) NOT NULL,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- DROP TABLE topics;
SELECT * FROM topics;


CREATE TABLE fields (
	id SERIAL PRIMARY KEY,
	topic_id INTEGER REFERENCES topics(id) ON DELETE CASCADE,
	key_name VARCHAR(200),
	value TEXT,
	field_type ENUM('text', 'number', 'email', 'url', 'date', 'boolean', 'json') DEFAULT 'text',
	is_visible BOOLEAN DEFAULT TRUE,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE plans CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Inserir planos básicos
INSERT INTO plans (name, price, max_workspaces, max_topics, max_fields, can_export, can_use_api) VALUES
('free', 0.00, 3, 3, 10, FALSE, FALSE),
('pro', 29.00, 5, 0, 0, TRUE, TRUE),
('admin', 0.00, 0, 0, 0, TRUE, TRUE);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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