CREATE DATABASE farmacia;

USE farmacia;

CREATE TABLE produtos (

    id INT PRIMARY KEY AUTO_INCREMENT,

    nome VARCHAR(255) NOT NULL,

    fabricante VARCHAR(255) NOT NULL,

    preco DECIMAL(10,2) NOT NULL,

    estoque INT NOT NULL

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