# Dreamlast CRM

CRM corporativo em PHP + MySQL, desenvolvido para portfólio e compatível com XAMPP.

## Instalação no XAMPP
1. Extraia a pasta `dreamlast_crm` dentro de `C:\xampp\htdocs\`.
2. Inicie **Apache** e **MySQL** no XAMPP Control Panel.
3. Abra no navegador: `http://localhost/dreamlast_crm/setup.php`.
4. Clique em **Instalar agora**.
5. Acesse o CRM com:
   - E-mail: `admin@dreamlast.com.br`
   - Senha: `Dream@123`

## Módulos
- Dashboard executivo
- Clientes
- Leads
- Pipeline / oportunidades
- Tarefas
- Agenda
- Projetos
- Atendimento / tickets
- Financeiro
- Relatórios
- Usuários e permissões
- Auditoria
- Configurações

## Segurança implementada
- `password_hash()` / `password_verify()`
- PDO com prepared statements
- Tokens CSRF
- Sessões PHP
- Controle de acesso por perfil
- Log de auditoria
- Escape de saída contra XSS

## Perfis
Administrador, Gerente, Vendas, Suporte e Financeiro.

> Para produção, altere a senha padrão, configure HTTPS, mova segredos para variáveis de ambiente e faça backups periódicos.
