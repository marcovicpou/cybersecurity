USE dreamlast_pdv;

CREATE TABLE IF NOT EXISTS sale_payments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 sale_id INT NOT NULL,
 method ENUM('cash','pix','debit','credit','voucher','crediario') NOT NULL,
 amount DECIMAL(12,2) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(sale_id) REFERENCES sales(id) ON DELETE CASCADE,
 INDEX(sale_id), INDEX(method)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 action VARCHAR(80) NOT NULL,
 entity VARCHAR(80) NOT NULL,
 entity_id VARCHAR(80) NULL,
 details TEXT NULL,
 ip VARCHAR(64) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX(created_at), INDEX(action), INDEX(entity)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_counts (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 notes VARCHAR(255) NULL,
 status ENUM('open','completed') NOT NULL DEFAULT 'open',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 completed_at DATETIME NULL,
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_count_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 inventory_count_id INT NOT NULL,
 product_id INT NOT NULL,
 system_qty INT NOT NULL,
 counted_qty INT NOT NULL,
 difference_qty INT NOT NULL,
 FOREIGN KEY(inventory_count_id) REFERENCES inventory_counts(id) ON DELETE CASCADE,
 FOREIGN KEY(product_id) REFERENCES products(id),
 UNIQUE KEY uniq_count_product(inventory_count_id,product_id)
) ENGINE=InnoDB;

ALTER TABLE products
 ADD COLUMN IF NOT EXISTS brand VARCHAR(100) NULL AFTER name,
 ADD COLUMN IF NOT EXISTS unit VARCHAR(20) NOT NULL DEFAULT 'UN' AFTER category,
 ADD COLUMN IF NOT EXISTS ncm VARCHAR(20) NULL AFTER unit,
 ADD COLUMN IF NOT EXISTS location VARCHAR(80) NULL AFTER min_stock;

INSERT IGNORE INTO settings(`key`,value) VALUES
 ('receipt_footer','Obrigado pela preferência!'),
 ('pix_key',''),
 ('store_number','001'),
 ('terminal_number','001'),
 ('scanner_sound','1');

INSERT INTO sale_payments(sale_id,method,amount)
SELECT s.id,s.payment_method,s.total
FROM sales s
LEFT JOIN sale_payments sp ON sp.sale_id=s.id
WHERE sp.id IS NULL AND s.status <> 'cancelled';
