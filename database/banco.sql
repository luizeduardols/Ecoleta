-- Pontos por Reciclagem | MySQL 5.7+/MariaDB 10.3+
CREATE DATABASE IF NOT EXISTS ppr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ppr;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  cpf VARCHAR(14) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  data_nascimento DATE NULL,
  telefone VARCHAR(15) NOT NULL,
  cidade VARCHAR(80) NULL,
  perfil ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  pontos INT UNSIGNED NOT NULL DEFAULT 0,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE materiais (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(60) NOT NULL UNIQUE,
  descricao VARCHAR(255) NULL,
  pontos_por_kg INT UNSIGNED NOT NULL,
  icone VARCHAR(8) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE parceiros (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  categoria VARCHAR(60) NULL,
  cidade VARCHAR(80) NULL,
  endereco VARCHAR(160) NULL,
  telefone VARCHAR(20) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE beneficios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parceiro_id INT UNSIGNED NULL,
  titulo VARCHAR(120) NOT NULL,
  descricao VARCHAR(255) NULL,
  custo_pontos INT UNSIGNED NOT NULL,
  estoque INT UNSIGNED NULL,
  validade DATE NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (parceiro_id) REFERENCES parceiros(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE entregas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  material_id INT UNSIGNED NOT NULL,
  parceiro_id INT UNSIGNED NULL,
  peso_kg DECIMAL(8,2) NOT NULL,
  pontos INT UNSIGNED NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (material_id) REFERENCES materiais(id),
  FOREIGN KEY (parceiro_id) REFERENCES parceiros(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE resgates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  beneficio_id INT UNSIGNED NOT NULL,
  pontos_gastos INT UNSIGNED NOT NULL,
  codigo CHAR(10) NOT NULL UNIQUE,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (beneficio_id) REFERENCES beneficios(id)
) ENGINE=InnoDB;

INSERT INTO materiais (nome, descricao, pontos_por_kg, icone) VALUES
 ('Papel','Jornais, caixas e papelão limpos',20,'📄'),
 ('Plástico','Garrafas PET, embalagens e potes',30,'🧴'),
 ('Vidro','Garrafas e potes sem tampa',40,'🍾'),
 ('Metal','Latas de alumínio e aço',50,'🥫'),
 ('Eletrônico','Pilhas, baterias e pequenos aparelhos',100,'🔋'),
 ('Orgânico','Restos de alimentos para compostagem',10,'🍌');

INSERT INTO parceiros (nome, categoria, cidade, endereco) VALUES
 ('Cine Boa Viagem','Cinema','Recife','Av. Boa Viagem, 1000'),
 ('Espaço Zen Olinda','Bem-estar','Olinda','Rua do Amparo, 45'),
 ('Casa de Shows Frevo','Shows','Recife','Rua da Moeda, 12'),
 ('Ecoponto Marco Zero','Ponto de coleta','Recife','Praça do Marco Zero');

INSERT INTO beneficios (parceiro_id, titulo, descricao, custo_pontos, estoque) VALUES
 (1,'2 ingressos de cinema','Válido de segunda a quinta',800,50),
 (2,'Sessão de massagem relaxante','30 minutos com agendamento',1200,20),
 (3,'Ingresso para show local','Setor pista',1500,30),
 (1,'Combo pipoca + refrigerante','Tamanho médio',300,100);
