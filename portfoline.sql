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
    general_hash text,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM users;
-- DROP TABLE users;

INSERT INTO users ("name",surname,email,"password",about_me,social,portfolio, general_hash,created_at,updated_at) VALUES
	 ('maria',NULL,'maria@gmail.com','$2y$12$Y62b7H5kMiaV7liM6AiEH.aTUjplLcRgXCmbcIqbXxU6lhBqe5FQ6',NULL,NULL,null, 'c4ca4238a0b923820dcc509a6f75849b','2024-10-14 21:57:19','2024-10-14 21:57:19'),
	 ('Jefferson','Carvalho','jcs@gmail.com','$2y$12$UQIjsl96TDvXLzf33HHWjuOU140OIDQ4Lx01lICb9LONBOP9FG8CO',NULL,NULL,NULL, 'c81e728d9d4c2f636f067f89cc14862c','2024-10-14 22:16:37','2024-10-14 22:16:37');


CREATE TABLE experiences (
    id SERIAL PRIMARY KEY,
    id_user INT NOT NULL,
    enterprise VARCHAR(30),
    responsibility VARCHAR(30),
    description VARCHAR(40),
    technologies TEXT,
    entry_date DATE,
    departure_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--drop table experiences;
select * from experiences;

CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    id_product INT NOT NULL,
    dir_image TEXT NOT NULL
);

SELECT * FROM images;