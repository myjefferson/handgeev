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
    primary_hash_api text,
    secondary_hash_api text,
    current_plan_id TINYINT UNSIGNED DEFAULT 1,
    plan_expires_at TIMESTAMP NULL,
	 status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_plan_id) REFERENCES plans(id),
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
) ENGINE=InnoDB;

-- Inserir planos básicos
INSERT INTO plans (name, price, max_workspaces, max_topics, max_fields, can_export, can_use_api) VALUES
('free', 0.00, 3, 3, 10, FALSE, FALSE),
('premium', 29.00, 5, 0, 0, TRUE, TRUE),
('admin', 0.00, 0, 0, 0, TRUE, TRUE);

SELECT * FROM plans;


-- DROPS
DROP TABLE fields;
DROP TABLE topics;
DROP TABLE workspaces;
DROP TABLE type_workspaces;
