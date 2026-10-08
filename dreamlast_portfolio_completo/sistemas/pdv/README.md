# Dreamlast PDV Pro v3

Sistema de ponto de venda em PHP + MySQL/MariaDB, projetado para XAMPP e uso local em comércio.

## Requisitos

- XAMPP com Apache + MySQL/MariaDB
- PHP 8.1+ recomendado
- Navegador moderno (Chrome/Edge recomendado para câmera)
- Leitor USB/Bluetooth de código de barras opcional

## Instalação nova

1. Extraia a pasta `dreamlast_pdv` em `C:\xampp\htdocs\`.
2. Inicie Apache e MySQL no XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe `sql/dreamlast_pdv.sql`.
5. Acesse `http://localhost/dreamlast_pdv/`.

Login inicial:

- E-mail: `admin@dreamlast.com`
- Senha: `admin123`

Troque a senha do administrador após o primeiro acesso.

## Atualização da v2 para v3

1. Faça backup do banco `dreamlast_pdv`.
2. Substitua os arquivos do projeto pelos da v3.
3. No phpMyAdmin, selecione o banco `dreamlast_pdv`.
4. Importe `sql/migration_v3.sql`.
5. Atualize a página do sistema.

O `migration_v3.sql` cria pagamentos múltiplos, auditoria, estrutura de inventário, novos campos de produto e faz o backfill das vendas antigas na tabela de pagamentos.

## Leitor de código de barras USB/Bluetooth

A maioria dos leitores comerciais funciona em modo HID, como se fosse um teclado. Por isso não é necessário instalar SDK no Dreamlast.

1. Conecte o leitor ao computador.
2. Abra a Frente de Caixa.
3. Bipar um código cadastrado adiciona o produto ao carrinho.
4. Bipar o mesmo item novamente aumenta a quantidade.
5. Também é possível clicar no campo de busca, digitar/bipar o EAN e pressionar Enter.

Configure o leitor para enviar `Enter` ao final da leitura (configuração padrão da maioria dos modelos).

## Leitura pela câmera

Na Frente de Caixa clique em **Ler com câmera**. No cadastro de produto, use **Câmera** ao lado do código de barras.

O sistema usa a API `BarcodeDetector` do navegador quando disponível, sem enviar imagens para servidor. Em navegadores sem suporte nativo, o sistema informa para usar Chrome/Edge atualizado ou leitor USB/Bluetooth. `localhost` normalmente é tratado como contexto seguro para acesso à câmera; autorize a permissão quando o navegador solicitar.

Formatos priorizados: EAN-13, EAN-8, UPC-A, UPC-E, Code 128 e Code 39.

## Recursos principais

- Frente de caixa responsiva
- Scanner físico USB/Bluetooth
- Scanner por câmera
- Código EAN/GTIN no cadastro
- Carrinho e baixa automática de estoque
- Múltiplas formas de pagamento na mesma venda
- Dinheiro, PIX, débito, crédito, vale e crediário
- Cálculo de troco
- Cliente/consumidor final
- Crediário e contas a receber
- Produtos, marca, categoria, unidade, NCM e localização de estoque
- Fornecedores e compras
- Entrada automática de estoque por compra
- Contas a pagar
- Orçamentos
- Devolução parcial e cancelamento com recomposição do estoque
- Abertura, suprimento, sangria e fechamento de caixa
- Resumo do caixa por forma de pagamento
- Inventário/ajuste de estoque com histórico
- Relatórios gerenciais
- Comprovante otimizado para impressora térmica 80 mm
- Usuários administrador, gerente e operador
- Auditoria de ações críticas
- Configuração de empresa, loja, terminal, PIX e rodapé do cupom
- CSRF, prepared statements, transações e password_hash/password_verify

## Observação fiscal e adquirentes

NFC-e/NF-e, TEF, PIX automático com confirmação, Mercado Pago/PagBank/Stone/Cielo/Rede e emissão fiscal real dependem de credenciais, certificados, homologação, UF e/ou provedor escolhido. A v3 deixa o PDV estruturado para evoluir nessas integrações, mas não simula autorização fiscal ou transação de cartão como se fossem reais.

## Segurança

Para produção, além de trocar a senha inicial:

- não exponha phpMyAdmin publicamente;
- use HTTPS se o sistema sair de `localhost`;
- crie backups periódicos do banco;
- use usuários individuais para auditoria;
- restrinja acesso à rede do caixa;
- valide obrigações fiscais com contador e integrador fiscal.

## Dreamlast PDV Pro v4 — correção da câmera e operação de balcão

### Atualizando da v3
1. Faça backup do banco.
2. Substitua os arquivos do projeto pelos da v4.
3. Importe `sql/migration_v4.sql` no banco `dreamlast_pdv`.
4. No navegador use `Ctrl + F5` para limpar o cache dos arquivos CSS/JS.

### Leitor de código de barras
- **Produtos:** o botão azul `📷 Abrir câmera` fica ao lado do campo Código de barras.
- **Frente de Caixa:** o botão azul `📷 Ler código com câmera` fica no topo e existe outro botão `📷 Câmera` ao lado da busca.
- Leitores USB/Bluetooth em modo HID continuam funcionando como teclado. Configure o leitor para enviar Enter após o código.
- A câmera usa `BarcodeDetector`, disponível principalmente em Chrome/Edge modernos. Em `localhost`, o navegador pode solicitar permissão de câmera; permita o acesso.

### Novidades v4
- Venda suspensa e recuperação do cupom no mesmo navegador.
- Atalhos F2 (busca), F4 (câmera), F8 (pagamento), F9 (finalizar) e Esc (limpar busca).
- Descontos rápidos no caixa.
- Limpeza completa do cupom com confirmação.
- Parcelas de cartão de crédito de 1x a 24x registradas no banco.
- Observação da venda persistida no banco.
- Gerador rápido de SKU no cadastro de produto.
- Destaque visual e instruções explícitas para câmera e leitor físico.
- Cache-busting de CSS/JS para evitar que o navegador continue mostrando a interface da versão anterior.
