# Replica Architect

PHP 8.1+, PDO SQLite e JavaScript sem framework. Stack compatível com o fluxo local/XAMPP anterior. Sem instalação npm, build ou serviço externo. Banco único fora da pasta pública, criado e semeado automaticamente; dinheiro inteiro em centavos BRL, datas ISO UTC e IDs UUID.

## Fluxos → API

- GET api.php?action=bootstrap: catálogo, carrinho, endereço, favoritos, token CSRF.
- POST action=address: F01, valida endereço do visitante.
- POST action=favorite: F02, altera favorito próprio.
- POST action=cart: F03, adiciona/edita/remove; cálculo no servidor; versão de carrinho para concorrência.
- POST action=coupon: F04, aplica/remove código local, recalcula.
- POST action=checkout: F04, sessão + CSRF + chave UUID de idempotência; transação SQLite; valida disponibilidade/mínimo e congela valores.
- GET action=orders: F05, apenas pedidos próprios.
- GET action=order&id=UUID: F05, 404 para UUID de outro visitante.
- POST action=cancel: F05, apenas pedido próprio em estado permitido.
- POST action=reset: F06, remove todos os dados próprios e mantém catálogo.

## Tabelas

visitors, restaurants, products, favorites, carts, cart_items, orders, order_items, checkout_attempts. Dez tabelas. Cart_items têm unicidade carrinho/produto/opção/observação; pedidos têm unicidade visitante/idempotency_key. FK explícitas, constraints, indexes, transações com BEGIN IMMEDIATE e optimistic locking da versão do carrinho.

## Autorização e dados

Sem cadastro com senha: identidade de visitante por sessão PHP, cookie HttpOnly/SameSite e Secure em HTTPS. Todos os objetos privados são filtrados por visitor_id no servidor. Não são coletados telefone, e-mail, senha ou dados de cartão. Endereço de teste local, com remoção voluntária. Nenhuma API externa. Não há OAuth/pagamento/e-mail fictício apresentado como integração real.

## Riscos e limites

SQLite serializa escritas; SQLITE_BUSY deve produzir erro recuperável. Uma sessão expirada vira novo visitante e não acessa dados antigos. CSRF obrigatório nas mutações. Chaves de idempotência evitam dois pedidos no duplo envio, inclusive retry após perda de resposta. Catálogo somente leitura e seed sintético. Valores de cliente não definem preços. Cookie e banco não são um modelo multiempresa de produção.

## Ordem de construção

S01–S04 e catálogo/carrinho → S05/pedidos com cálculo e idempotência → S06/S07 → S08/privacidade → testes, análise e marca. Não publicar app em hospedagem sem etapa de aprovação de deploy. Entrega de código pelo GitHub não equivale a deploy do serviço.
