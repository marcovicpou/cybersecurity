CREATE DATABASE IF NOT EXISTS dreamlast_pdv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dreamlast_pdv;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(160) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,
 role ENUM('admin','manager','cashier') NOT NULL DEFAULT 'cashier',active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE customers (
 id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,document VARCHAR(30),phone VARCHAR(30),email VARCHAR(160),address TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(name),INDEX(document)
) ENGINE=InnoDB;
CREATE TABLE suppliers (
 id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(160) NOT NULL,document VARCHAR(30),contact VARCHAR(120),phone VARCHAR(30),email VARCHAR(160),address TEXT,active TINYINT(1) DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(name),INDEX(document)
) ENGINE=InnoDB;
CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,sku VARCHAR(60) NOT NULL UNIQUE,barcode VARCHAR(80) UNIQUE,name VARCHAR(160) NOT NULL,brand VARCHAR(100),category VARCHAR(100) DEFAULT 'Geral',unit VARCHAR(20) NOT NULL DEFAULT 'UN',ncm VARCHAR(20),cost DECIMAL(12,2) NOT NULL DEFAULT 0,price DECIMAL(12,2) NOT NULL DEFAULT 0,stock INT NOT NULL DEFAULT 0,min_stock INT NOT NULL DEFAULT 5,location VARCHAR(80),active TINYINT(1) NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX(name),INDEX(barcode)
) ENGINE=InnoDB;
CREATE TABLE cash_sessions (
 id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,opening_amount DECIMAL(12,2) NOT NULL DEFAULT 0,closing_amount DECIMAL(12,2),expected_amount DECIMAL(12,2),difference_amount DECIMAL(12,2),opened_at DATETIME NOT NULL,closed_at DATETIME,status ENUM('open','closed') NOT NULL DEFAULT 'open',FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE sales (
 id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT NULL,user_id INT NOT NULL,cash_session_id INT NOT NULL,subtotal DECIMAL(12,2) NOT NULL,discount DECIMAL(12,2) NOT NULL DEFAULT 0,total DECIMAL(12,2) NOT NULL,
 payment_method ENUM('cash','pix','debit','credit','voucher','crediario') NOT NULL,amount_received DECIMAL(12,2) NOT NULL DEFAULT 0,change_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
 status ENUM('completed','partially_returned','returned','cancelled') NOT NULL DEFAULT 'completed',notes VARCHAR(255) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES customers(id),FOREIGN KEY(user_id) REFERENCES users(id),FOREIGN KEY(cash_session_id) REFERENCES cash_sessions(id),INDEX(created_at)
) ENGINE=InnoDB;
CREATE TABLE sale_items (
 id INT AUTO_INCREMENT PRIMARY KEY,sale_id INT NOT NULL,product_id INT NOT NULL,quantity INT NOT NULL,unit_price DECIMAL(12,2) NOT NULL,total DECIMAL(12,2) NOT NULL,FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;
CREATE TABLE stock_movements (
 id INT AUTO_INCREMENT PRIMARY KEY,product_id INT NOT NULL,user_id INT NOT NULL,type ENUM('sale','purchase','adjustment','return') NOT NULL,quantity INT NOT NULL,reference VARCHAR(80),notes VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(product_id) REFERENCES products(id),FOREIGN KEY(user_id) REFERENCES users(id),INDEX(created_at)
) ENGINE=InnoDB;
CREATE TABLE cash_movements (
 id INT AUTO_INCREMENT PRIMARY KEY,cash_session_id INT NOT NULL,user_id INT NOT NULL,type ENUM('income','expense') NOT NULL,amount DECIMAL(12,2) NOT NULL,description VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(cash_session_id) REFERENCES cash_sessions(id),FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE purchases (
 id INT AUTO_INCREMENT PRIMARY KEY,supplier_id INT NOT NULL,user_id INT NOT NULL,invoice_number VARCHAR(80),total DECIMAL(12,2) NOT NULL DEFAULT 0,status ENUM('received','cancelled') DEFAULT 'received',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(supplier_id) REFERENCES suppliers(id),FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE purchase_items (
 id INT AUTO_INCREMENT PRIMARY KEY,purchase_id INT NOT NULL,product_id INT NOT NULL,quantity INT NOT NULL,unit_cost DECIMAL(12,2) NOT NULL,total DECIMAL(12,2) NOT NULL,FOREIGN KEY(purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;
CREATE TABLE accounts_payable (
 id INT AUTO_INCREMENT PRIMARY KEY,supplier_id INT NULL,purchase_id INT NULL,description VARCHAR(180) NOT NULL,amount DECIMAL(12,2) NOT NULL,due_date DATE NOT NULL,status ENUM('open','paid','cancelled') DEFAULT 'open',paid_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(supplier_id) REFERENCES suppliers(id),FOREIGN KEY(purchase_id) REFERENCES purchases(id),INDEX(due_date),INDEX(status)
) ENGINE=InnoDB;
CREATE TABLE accounts_receivable (
 id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT NULL,sale_id INT NULL,description VARCHAR(180) NOT NULL,amount DECIMAL(12,2) NOT NULL,due_date DATE NOT NULL,status ENUM('open','received','cancelled') DEFAULT 'open',received_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(customer_id) REFERENCES customers(id),FOREIGN KEY(sale_id) REFERENCES sales(id),INDEX(due_date),INDEX(status)
) ENGINE=InnoDB;
CREATE TABLE quotes (
 id INT AUTO_INCREMENT PRIMARY KEY,customer_id INT NULL,user_id INT NOT NULL,subtotal DECIMAL(12,2) NOT NULL,discount DECIMAL(12,2) NOT NULL DEFAULT 0,total DECIMAL(12,2) NOT NULL,valid_until DATE NOT NULL,status ENUM('open','approved','expired','cancelled') DEFAULT 'open',notes TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(customer_id) REFERENCES customers(id),FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE quote_items (
 id INT AUTO_INCREMENT PRIMARY KEY,quote_id INT NOT NULL,product_id INT NOT NULL,quantity INT NOT NULL,unit_price DECIMAL(12,2) NOT NULL,total DECIMAL(12,2) NOT NULL,FOREIGN KEY(quote_id) REFERENCES quotes(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;
CREATE TABLE sale_returns (
 id INT AUTO_INCREMENT PRIMARY KEY,sale_id INT NOT NULL,user_id INT NOT NULL,type ENUM('return','cancel') NOT NULL,total DECIMAL(12,2) NOT NULL DEFAULT 0,reason VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(sale_id) REFERENCES sales(id),FOREIGN KEY(user_id) REFERENCES users(id),INDEX(created_at)
) ENGINE=InnoDB;
CREATE TABLE sale_return_items (
 id INT AUTO_INCREMENT PRIMARY KEY,return_id INT NOT NULL,product_id INT NOT NULL,quantity INT NOT NULL,unit_price DECIMAL(12,2) NOT NULL,total DECIMAL(12,2) NOT NULL,FOREIGN KEY(return_id) REFERENCES sale_returns(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;
CREATE TABLE settings (`key` VARCHAR(80) PRIMARY KEY,value TEXT) ENGINE=InnoDB;


CREATE TABLE sale_payments (
 id INT AUTO_INCREMENT PRIMARY KEY,sale_id INT NOT NULL,method ENUM('cash','pix','debit','credit','voucher','crediario') NOT NULL,amount DECIMAL(12,2) NOT NULL,installments TINYINT UNSIGNED NOT NULL DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE CASCADE,INDEX(sale_id),INDEX(method)
) ENGINE=InnoDB;
CREATE TABLE audit_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,action VARCHAR(80) NOT NULL,entity VARCHAR(80) NOT NULL,entity_id VARCHAR(80),details TEXT,ip VARCHAR(64),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,INDEX(created_at),INDEX(action),INDEX(entity)
) ENGINE=InnoDB;
CREATE TABLE inventory_counts (
 id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,notes VARCHAR(255),status ENUM('open','completed') NOT NULL DEFAULT 'open',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,completed_at DATETIME,FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE inventory_count_items (
 id INT AUTO_INCREMENT PRIMARY KEY,inventory_count_id INT NOT NULL,product_id INT NOT NULL,system_qty INT NOT NULL,counted_qty INT NOT NULL,difference_qty INT NOT NULL,FOREIGN KEY(inventory_count_id) REFERENCES inventory_counts(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id),UNIQUE KEY uniq_count_product(inventory_count_id,product_id)
) ENGINE=InnoDB;

INSERT INTO users(name,email,password,role) VALUES ('Administrador','admin@dreamlast.com','$2y$12$bXDqwnQJ/BWZhcdkSgPTbeWD/I.WX2ileoeGWfUBU1w.mgnAOKzf6','admin');
INSERT INTO settings(`key`,value) VALUES ('company_name','Dreamlast Comércio'),('company_document',''),('company_phone',''),('company_address',''),('receipt_footer','Obrigado pela preferência!'),('pix_key',''),('store_number','001'),('terminal_number','001'),('scanner_sound','1'),('system_version','4.0.0');
INSERT INTO customers(name,document,phone,email) VALUES ('Cliente Exemplo','123.456.789-00','(11) 99999-9999','cliente@exemplo.com');
INSERT INTO suppliers(name,document,contact,phone,email) VALUES ('Fornecedor Exemplo','12.345.678/0001-99','Comercial','(11) 4000-0000','fornecedor@exemplo.com');
INSERT INTO products(sku,barcode,name,category,cost,price,stock,min_stock) VALUES
('CAM-001','789000000001','Camiseta Dreamlast Essential','Vestuário',35.00,79.90,30,5),
('BON-001','789000000002','Boné Dreamlast Classic','Acessórios',28.00,59.90,18,4),
('MOL-001','789000000003','Moletom Dreamlast Urban','Vestuário',75.00,149.90,12,3),
('CAN-001','789000000004','Caneca Dreamlast','Acessórios',18.00,39.90,25,5);
