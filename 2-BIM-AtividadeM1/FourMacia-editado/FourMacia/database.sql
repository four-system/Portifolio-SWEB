CREATE DATABASE IF NOT EXISTS farmacia;

USE farmacia;

CREATE TABLE IF NOT EXISTS produtos (

    id INT PRIMARY KEY AUTO_INCREMENT,

    nome VARCHAR(255) NOT NULL,

    fabricante VARCHAR(255) NOT NULL,

    preco DECIMAL(10,2) NOT NULL CHECK (preco >= 0),

    estoque INT NOT NULL CHECK (estoque >= 0)

);

/*
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    fabricante VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL
);

Cole isso no MySQL Admin, com o nome "farmacia"*/