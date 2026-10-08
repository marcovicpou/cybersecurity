# Dreamlast ERP

ERP demonstrativo/profissional em PHP + MySQL para XAMPP.

## Instalação no XAMPP
1. Copie a pasta `dreamlast_erp` para `C:\xampp\htdocs\`.
2. Inicie **Apache** e **MySQL** no painel do XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe o arquivo `sql/dreamlast_erp.sql`.
5. Acesse `http://localhost/dreamlast_erp`.
6. Login padrão: `admin@dreamlast.com` / `admin123`.

## Módulos incluídos
- Dashboard executivo com KPIs
- Autenticação segura com `password_hash`/`password_verify`
- Clientes
- Fornecedores
- Produtos
- Estoque e movimentações
- Vendas com baixa automática de estoque e lançamento financeiro
- Compras com entrada automática no estoque e contas a pagar
- Financeiro (receitas, despesas e resultado)
- Funcionários
- Relatórios e gráficos com Chart.js
- Configurações da empresa
- Layout responsivo com Bootstrap 5
- PDO + prepared statements

## Observação
Este pacote foi pensado como base forte de portfólio e ambiente local. Para produção pública, acrescente HTTPS, CSRF, controle granular de permissões, logs de auditoria, rotina de backup, política de senha e validações fiscais conforme o negócio.
