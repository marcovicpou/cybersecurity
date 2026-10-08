CREATE DATABASE IF NOT EXISTS dreamlast_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dreamlast_erp;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  perfil ENUM('ADMIN','GESTOR','VENDEDOR','FINANCEIRO','ESTOQUE') DEFAULT 'ADMIN',
  ativo TINYINT(1) DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  documento VARCHAR(30), email VARCHAR(160), telefone VARCHAR(30), cidade VARCHAR(100),
  status ENUM('ATIVO','INATIVO') DEFAULT 'ATIVO', criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fornecedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razao_social VARCHAR(160) NOT NULL,
  documento VARCHAR(30), contato VARCHAR(120), email VARCHAR(160), telefone VARCHAR(30),
  status ENUM('ATIVO','INATIVO') DEFAULT 'ATIVO', criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) NOT NULL UNIQUE,
  nome VARCHAR(160) NOT NULL,
  categoria VARCHAR(100),
  preco_custo DECIMAL(12,2) DEFAULT 0,
  preco_venda DECIMAL(12,2) DEFAULT 0,
  estoque_atual DECIMAL(12,2) DEFAULT 0,
  estoque_minimo DECIMAL(12,2) DEFAULT 0,
  status ENUM('ATIVO','INATIVO') DEFAULT 'ATIVO', criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE funcionarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  cargo VARCHAR(100), departamento VARCHAR(100), email VARCHAR(160), telefone VARCHAR(30),
  salario DECIMAL(12,2) DEFAULT 0,
  status ENUM('ATIVO','FERIAS','INATIVO') DEFAULT 'ATIVO', criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NULL, usuario_id INT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('ABERTA','CONCLUIDA','CANCELADA') DEFAULT 'CONCLUIDA',
  forma_pagamento VARCHAR(40), data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
CREATE TABLE venda_itens (
  id INT AUTO_INCREMENT PRIMARY KEY, venda_id INT NOT NULL, produto_id INT NOT NULL,
  quantidade DECIMAL(12,2) NOT NULL, preco_unitario DECIMAL(12,2) NOT NULL, subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fornecedor_id INT NULL, usuario_id INT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('PEDIDA','RECEBIDA','CANCELADA') DEFAULT 'RECEBIDA',
  data_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE SET NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
CREATE TABLE compra_itens (
  id INT AUTO_INCREMENT PRIMARY KEY, compra_id INT NOT NULL, produto_id INT NOT NULL,
  quantidade DECIMAL(12,2) NOT NULL, preco_unitario DECIMAL(12,2) NOT NULL, subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE movimentacoes_estoque (
  id INT AUTO_INCREMENT PRIMARY KEY, produto_id INT NOT NULL,
  tipo ENUM('ENTRADA','SAIDA','AJUSTE') NOT NULL,
  quantidade DECIMAL(12,2) NOT NULL, observacao VARCHAR(255), usuario_id INT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (produto_id) REFERENCES produtos(id), FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE financeiro (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo ENUM('RECEITA','DESPESA') NOT NULL,
  descricao VARCHAR(180) NOT NULL, valor DECIMAL(12,2) NOT NULL,
  vencimento DATE NOT NULL, status ENUM('PENDENTE','PAGO','ATRASADO','CANCELADO') DEFAULT 'PENDENTE',
  categoria VARCHAR(100), criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE configuracoes (
  id INT AUTO_INCREMENT PRIMARY KEY, chave VARCHAR(100) NOT NULL UNIQUE, valor TEXT
);

INSERT INTO usuarios(nome,email,senha,perfil) VALUES ('Administrador Dreamlast','admin@dreamlast.com','$2y$12$CWjWl5./815kdIdPGT4vF.R.a6QISgAKCh4a0eKWmgC3jN6n1VxmG','ADMIN');
INSERT INTO clientes(nome,documento,email,telefone,cidade) VALUES
('Cliente Demonstração','123.456.789-00','cliente@demo.com','(11) 99999-0000','São Paulo'),
('Empresa Alpha LTDA','12.345.678/0001-90','compras@alpha.com','(11) 4000-1000','Mogi das Cruzes');
INSERT INTO fornecedores(razao_social,documento,contato,email,telefone) VALUES
('Fornecedor Prime','98.765.432/0001-10','Carlos Silva','vendas@prime.com','(11) 3000-2000');
INSERT INTO produtos(sku,nome,categoria,preco_custo,preco_venda,estoque_atual,estoque_minimo) VALUES
('DL-001','Camiseta Dreamlast Essential','Vestuário',45.00,99.90,35,10),
('DL-002','Moletom Dreamlast Core','Vestuário',95.00,199.90,18,6),
('DL-003','Boné Dreamlast Street','Acessórios',35.00,79.90,8,10);
INSERT INTO funcionarios(nome,cargo,departamento,email,telefone,salario) VALUES
('Mariana Costa','Analista Financeiro','Financeiro','mariana@dreamlast.com','(11) 98888-1111',4200),
('Lucas Almeida','Vendedor','Comercial','lucas@dreamlast.com','(11) 97777-2222',2800);
INSERT INTO financeiro(tipo,descricao,valor,vencimento,status,categoria) VALUES
('RECEITA','Venda corporativa prevista',2500,CURDATE(),'PENDENTE','VENDAS'),
('DESPESA','Aluguel do escritório',1800,CURDATE(),'PENDENTE','ADMINISTRATIVO');
INSERT INTO configuracoes(chave,valor) VALUES ('empresa_nome','Dreamlast'),('empresa_email','contato@dreamlast.com');
