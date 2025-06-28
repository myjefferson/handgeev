CREATE DATABASE portfoline;
USE portfoline;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30),
    email VARCHAR(40) NOT NULL,
    password TEXT NOT NULL,
    phone VARCHAR(20),
    nationality VARCHAR(30),
    marital_status VARCHAR(30),
	driver_license_category VARCHAR(5),
    about_me TEXT,
    birthdate DATE,
    social TEXT,
    personal_site TEXT,
    postal_code VARCHAR(20),
    street  VARCHAR(40),
    complement  VARCHAR(50),
    neighborhood  VARCHAR(30),
    city  VARCHAR(30),
    state  VARCHAR(30),
    portfolio VARCHAR(20),
    primary_hash_api text,
    secondary_hash_api text,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM users;
-- DROP TABLE users;

INSERT INTO users (name, surname, email, password, about_me, social, portfolio, primary_hash_api, secondary_hash_api, created_at, updated_at) 
VALUES ('Jefferson','Carvalho','jcs@gmail.com','$2y$12$UQIjsl96TDvXLzf33HHWjuOU140OIDQ4Lx01lICb9LONBOP9FG8CO',NULL,NULL,NULL, 'c81e728d9d4c2f636f067f89cc14862c', '6f067f89cc14862cc81e728d9d4c2f63','2024-10-14 22:16:37','2024-10-14 22:16:37');
-- Password: 78**


CREATE TABLE experiences (
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL,
    enterprise VARCHAR(80),
    responsibility VARCHAR(80),
    description TEXT,
    technologies_used TEXT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- drop table experiences;
select * from experiences;

CREATE TABLE courses (
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    duration VARCHAR(50),
--    certificate BOOLEAN DEFAULT FALSE,-- Indica se há certificado
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--drop table courses;
select * from courses;


CREATE TABLE projects (
    id SERIAL PRIMARY KEY,              -- Identificador único do projeto
    id_user INT NOT NULL,               -- Relacionamento com o usuário (dono do projeto)
    title VARCHAR(255) NOT NULL,        -- Título do projeto
    subtitle VARCHAR(255),              -- Subtítulo ou breve resumo do projeto
    description TEXT,                   -- Descrição detalhada do projeto
    start_date DATE,                    -- Data de início
    end_date DATE,                      -- Data de término (pode ser NULL para projetos em andamento)
    status VARCHAR(50) DEFAULT 'active',-- Status do projeto (ex.: 'active', 'completed', 'archived')
    technologies_used TEXT,             -- Tecnologias utilizadas (armazenadas como texto ou JSON)
    project_link VARCHAR(255),          -- Link do site do projeto
    git_repository_link VARCHAR(255),   -- Link do repositório Git
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação do registro
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Data de atualização do registro
);

--drop table projects;
select * from projects;


CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    id_product INT NOT NULL,
    dir_image TEXT NOT NULL
);

SELECT * FROM images;