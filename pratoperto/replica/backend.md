# Backend implementado

PHP 8.1+ e PDO SQLite; schema.sql idempotente e seed próprio. SQLite com foreign keys, WAL e busy timeout. Identidade anônima por cookie de sessão httpOnly, SameSite=Lax, secure quando HTTPS, strict mode. Todas as leituras e alterações de dados pessoais incluem visitor_id.

Proteção CSRF nas mutações; consultas preparadas; limites de tamanho; opções/quantidades validadas. Valores em centavos calculados pelo servidor. Versão otimista do carrinho rejeita alterações antigas com 409 e fornece estado atual. Checkout sob BEGIN IMMEDIATE, snapshots de itens/endereço e chave UUID de idempotência única por visitante. Repetição devolve o pedido existente, sem duplicação. Cancelamento idempotente. Exclusão do visitante faz cascade e troca sessão/token.

Dados persistem fora do diretório público. PRATOPERTO_DATA_DIR permite apontar para pasta privada; .env.example apenas documenta a variável. Não há segredo, dependência Composer, provedor, envio de email, OAuth, uploads ou pagamento real. Nenhum processador externo. Para backup local, use SQLite backup ou snapshot consistente com banco parado; não copie somente o arquivo principal durante escrita em WAL.

Autenticação durável, recuperação de acesso, limites contra abuso em escala, retenção e serviços de produção estão fora deste recorte local. Cookie não é uma conta de usuário. A expiração da sessão não recupera pedidos anteriores.
