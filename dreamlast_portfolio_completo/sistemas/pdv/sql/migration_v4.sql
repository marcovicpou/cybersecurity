USE dreamlast_pdv;

ALTER TABLE sales ADD COLUMN IF NOT EXISTS notes VARCHAR(255) NULL AFTER status;
ALTER TABLE sale_payments ADD COLUMN IF NOT EXISTS installments TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER amount;
INSERT INTO settings(`key`,value) VALUES ('system_version','4.0.0') ON DUPLICATE KEY UPDATE value='4.0.0';
