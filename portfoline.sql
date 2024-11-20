CREATE DATABASE portfoline;

\c portfoline  -- Conectar ao banco de dados 'kyrios'

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
    hash_api text,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM users;
-- DROP TABLE users;

INSERT INTO users ("name",surname,email,"password",about_me,social,portfolio, hash_api,created_at,updated_at) VALUES
	 ('maria',NULL,'maria@gmail.com','$2y$12$Y62b7H5kMiaV7liM6AiEH.aTUjplLcRgXCmbcIqbXxU6lhBqe5FQ6',NULL,NULL,null, 'c4ca4238a0b923820dcc509a6f75849b','2024-10-14 21:57:19','2024-10-14 21:57:19'),
	 ('Jefferson','Carvalho','jcs@gmail.com','$2y$12$UQIjsl96TDvXLzf33HHWjuOU140OIDQ4Lx01lICb9LONBOP9FG8CO',NULL,NULL,NULL, 'c81e728d9d4c2f636f067f89cc14862c','2024-10-14 22:16:37','2024-10-14 22:16:37');


CREATE TABLE experiences (
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL,
    enterprise VARCHAR(30),
    responsibility VARCHAR(30),
    description VARCHAR(40),
    technologies_used TEXT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

drop table experiences;
select * from experiences;

CREATE TABLE courses (
    id BIGSERIAL PRIMARY KEY,
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
    user_id INT NOT NULL,               -- Relacionamento com o usuário (dono do projeto)
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


CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    id_product INT NOT NULL,
    dir_image TEXT NOT NULL
);

SELECT * FROM images;