CREATE DATABASE portfoline;

\c portfoline  -- Conectar ao banco de dados 'kyrios'

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30),
    email VARCHAR(40) NOT NULL,
    password TEXT NOT NULL,
    about_me TEXT,
    social TEXT,
    portfolio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM users;
-- DROP TABLE users;

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    id_status INT NOT NULL,
    id_category INT,
    title VARCHAR(50) NOT NULL,
    description TEXT,
    url_video TEXT,
    price DOUBLE PRECISION NOT NULL,
    discount INT DEFAULT NULL,
    discount_expires TIMESTAMP DEFAULT NULL,
    sizes VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM products;
-- DROP TABLE products;

CREATE TABLE status (
    id SERIAL PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    description TEXT
);


SELECT * FROM status;

CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    id_product INT NOT NULL,
    dir_image TEXT NOT NULL
);

SELECT * FROM images;

CREATE TABLE site (
    id SERIAL PRIMARY KEY,
    telefone VARCHAR(15),
    instagram VARCHAR(20),
    email VARCHAR(20),
    cep VARCHAR(20),
    logradouro VARCHAR(30),
    bairro VARCHAR(30),
    localidade VARCHAR(30),
    estado VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM site;

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    title VARCHAR(50) NOT null,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


SELECT * FROM categories;
