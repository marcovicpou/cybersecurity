CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('Administrador','Gerente','Vendas','Suporte','Financeiro') NOT NULL DEFAULT 'Vendas',
 active TINYINT(1) NOT NULL DEFAULT 1, last_login DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clients (
 id INT AUTO_INCREMENT PRIMARY KEY, type ENUM('Empresa','Pessoa') DEFAULT 'Empresa', name VARCHAR(160) NOT NULL,
 document VARCHAR(30), email VARCHAR(160), phone VARCHAR(40), segment VARCHAR(80), city VARCHAR(90), state VARCHAR(2),
 status ENUM('Ativo','Prospect','Inativo') DEFAULT 'Prospect', owner_id INT NULL, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS leads (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, company VARCHAR(150), email VARCHAR(160), phone VARCHAR(40),
 source VARCHAR(80), status ENUM('Novo','Contato','Qualificado','Descartado','Convertido') DEFAULT 'Novo', score INT DEFAULT 50,
 owner_id INT NULL, next_contact DATE NULL, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS opportunities (
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, client_id INT NULL, value DECIMAL(12,2) DEFAULT 0,
 stage ENUM('Prospecção','Qualificação','Proposta','Negociação','Ganho','Perdido') DEFAULT 'Prospecção', probability INT DEFAULT 10,
 expected_close DATE NULL, owner_id INT NULL, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL, FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tasks (
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, description TEXT, due_date DATETIME NULL,
 priority ENUM('Baixa','Média','Alta','Urgente') DEFAULT 'Média', status ENUM('Pendente','Em andamento','Concluída','Cancelada') DEFAULT 'Pendente',
 assigned_to INT NULL, related_type VARCHAR(30), related_id INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, client_id INT NULL, manager_id INT NULL,
 status ENUM('Planejamento','Em andamento','Pausado','Concluído','Cancelado') DEFAULT 'Planejamento', progress INT DEFAULT 0,
 budget DECIMAL(12,2) DEFAULT 0, start_date DATE NULL, end_date DATE NULL, description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL, FOREIGN KEY(manager_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tickets (
 id INT AUTO_INCREMENT PRIMARY KEY, subject VARCHAR(180) NOT NULL, client_id INT NULL, category VARCHAR(80),
 priority ENUM('Baixa','Média','Alta','Crítica') DEFAULT 'Média', status ENUM('Aberto','Em atendimento','Aguardando cliente','Resolvido','Fechado') DEFAULT 'Aberto',
 assigned_to INT NULL, description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL, FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS finance (
 id INT AUTO_INCREMENT PRIMARY KEY, description VARCHAR(180) NOT NULL, type ENUM('Receita','Despesa') NOT NULL,
 category VARCHAR(80), amount DECIMAL(12,2) NOT NULL, due_date DATE NULL, paid_date DATE NULL,
 status ENUM('Pendente','Pago','Atrasado','Cancelado') DEFAULT 'Pendente', client_id INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (
 id INT AUTO_INCREMENT PRIMARY KEY, client_id INT NOT NULL, name VARCHAR(140) NOT NULL, position VARCHAR(100), email VARCHAR(160), phone VARCHAR(40), is_primary TINYINT DEFAULT 0,
 FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activities (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, client_id INT NULL, type VARCHAR(40), title VARCHAR(180), details TEXT, activity_date DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL, FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, action VARCHAR(60) NOT NULL, details TEXT, ip_address VARCHAR(60), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (`key` VARCHAR(80) PRIMARY KEY, `value` TEXT);

INSERT IGNORE INTO settings(`key`,`value`) VALUES ('company_name','Dreamlast'),('company_email','contato.dreamlast@gmail.com'),('currency','BRL');

INSERT INTO clients(type,name,document,email,phone,segment,city,state,status,notes)
SELECT 'Empresa','Clínica Vittale','12.345.678/0001-10','contato@vittale.demo','(11) 98888-1001','Saúde','São Paulo','SP','Ativo','Cliente de demonstração' WHERE NOT EXISTS(SELECT 1 FROM clients WHERE name='Clínica Vittale');
INSERT INTO clients(type,name,document,email,phone,segment,city,state,status,notes)
SELECT 'Empresa','Acelera Fitness','23.456.789/0001-20','contato@acelera.demo','(11) 97777-2002','Fitness','São Paulo','SP','Prospect','Prospect de demonstração' WHERE NOT EXISTS(SELECT 1 FROM clients WHERE name='Acelera Fitness');
INSERT INTO clients(type,name,email,phone,segment,city,state,status)
SELECT 'Empresa','Vet Fuzzi','contato@vetfuzzi.demo','(11) 96666-3003','Pet','São Paulo','SP','Ativo' WHERE NOT EXISTS(SELECT 1 FROM clients WHERE name='Vet Fuzzi');

INSERT INTO leads(name,company,email,phone,source,status,score,next_contact)
SELECT 'Carla Mendes','Studio Essenza','carla@demo.com','(11) 95555-1111','Instagram','Qualificado',82,CURDATE()+INTERVAL 2 DAY WHERE NOT EXISTS(SELECT 1 FROM leads WHERE email='carla@demo.com');
INSERT INTO leads(name,company,email,phone,source,status,score,next_contact)
SELECT 'Rafael Lima','RL Auto Center','rafael@demo.com','(11) 94444-2222','Google Maps','Contato',65,CURDATE()+INTERVAL 1 DAY WHERE NOT EXISTS(SELECT 1 FROM leads WHERE email='rafael@demo.com');

INSERT INTO opportunities(title,client_id,value,stage,probability,expected_close)
SELECT 'Novo site + CRM',id,7800,'Negociação',80,CURDATE()+INTERVAL 10 DAY FROM clients WHERE name='Acelera Fitness' AND NOT EXISTS(SELECT 1 FROM opportunities WHERE title='Novo site + CRM');
INSERT INTO opportunities(title,client_id,value,stage,probability,expected_close)
SELECT 'Portal de agendamentos',id,12500,'Proposta',60,CURDATE()+INTERVAL 18 DAY FROM clients WHERE name='Clínica Vittale' AND NOT EXISTS(SELECT 1 FROM opportunities WHERE title='Portal de agendamentos');
INSERT INTO opportunities(title,client_id,value,stage,probability,expected_close)
SELECT 'Manutenção anual',id,4800,'Ganho',100,CURDATE()-INTERVAL 5 DAY FROM clients WHERE name='Vet Fuzzi' AND NOT EXISTS(SELECT 1 FROM opportunities WHERE title='Manutenção anual');

INSERT INTO tasks(title,due_date,priority,status)
SELECT 'Follow-up proposta Acelera',NOW()+INTERVAL 1 DAY,'Alta','Pendente' WHERE NOT EXISTS(SELECT 1 FROM tasks WHERE title='Follow-up proposta Acelera');
INSERT INTO tasks(title,due_date,priority,status)
SELECT 'Revisar briefing do portal',NOW()+INTERVAL 2 DAY,'Média','Em andamento' WHERE NOT EXISTS(SELECT 1 FROM tasks WHERE title='Revisar briefing do portal');

INSERT INTO projects(name,client_id,status,progress,budget,start_date,end_date)
SELECT 'Portal Vet Fuzzi',id,'Em andamento',68,15000,CURDATE()-INTERVAL 20 DAY,CURDATE()+INTERVAL 15 DAY FROM clients WHERE name='Vet Fuzzi' AND NOT EXISTS(SELECT 1 FROM projects WHERE name='Portal Vet Fuzzi');

INSERT INTO tickets(subject,client_id,category,priority,status,description)
SELECT 'Ajuste no formulário de contato',id,'Sistema','Média','Em atendimento','Cliente solicitou alteração no formulário.' FROM clients WHERE name='Vet Fuzzi' AND NOT EXISTS(SELECT 1 FROM tickets WHERE subject='Ajuste no formulário de contato');

INSERT INTO finance(description,type,category,amount,due_date,status,client_id)
SELECT 'Projeto Portal Vittale','Receita','Projetos',4500,CURDATE()+INTERVAL 5 DAY,'Pendente',id FROM clients WHERE name='Clínica Vittale' AND NOT EXISTS(SELECT 1 FROM finance WHERE description='Projeto Portal Vittale');
INSERT INTO finance(description,type,category,amount,due_date,paid_date,status)
SELECT 'Servidor VPS','Despesa','Infraestrutura',289.90,CURDATE(),CURDATE(),'Pago' WHERE NOT EXISTS(SELECT 1 FROM finance WHERE description='Servidor VPS');
