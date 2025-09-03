CREATE DATABASE portfoline;
USE portfoline;

-- NEW DATABASE PORTFOLINE
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30),
    email VARCHAR(40) NOT NULL,
    password TEXT NOT NULL,
    phone VARCHAR(20),
    primary_hash_api text,
    secondary_hash_api text,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


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
	 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE workspaces

SELECT * FROM workspaces;

CREATE TABLE topics (
	id SERIAL PRIMARY KEY,
	workspace_id INTEGER REFERENCES workspaces(id) ON DELETE CASCADE NOT NULL,
	title VARCHAR(100) NOT NULL,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DROP TABLE topics;
SELECT * FROM topics;


CREATE TABLE fields (
	id SERIAL PRIMARY KEY,
	topic_id INTEGER REFERENCES topics(id) ON DELETE CASCADE,
	key_name VARCHAR(200),
	value TEXT,
	is_visible BOOLEAN DEFAULT TRUE,
	`order` INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM fields


-- DROPS

DROP TABLE fields;
DROP TABLE topics;
DROP TABLE workspaces;
DROP TABLE type_workspaces;
