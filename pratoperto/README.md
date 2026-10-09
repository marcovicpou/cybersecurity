# PratoPerto — delivery local de demonstração

Aplicação independente em PHP, SQLite e JavaScript. Catálogo, busca, categorias, favoritos, opções e observações, carrinho, cupons, checkout fictício, histórico, acompanhamento simulado e cancelamento. Não envia pedidos a restaurantes e não processa pagamentos.

## Abrir no XAMPP (Windows)

1. Extraia a pasta **pratoperto** do ZIP para **C:\xampp\htdocs\pratoperto**.
2. Abra o painel do XAMPP e inicie **Apache**. Não é necessário iniciar MySQL.
3. Acesse **http://localhost/pratoperto/** no navegador.
4. Clique em “Escolha seu endereço” e use “Preencher com um exemplo fictício”.
5. Escolha um prato, abra o carrinho e confirme um pedido de demonstração.

O arquivo **ABRIR-TESTE-XAMPP.bat** abre o painel e o endereço no navegador quando esta pasta estiver no htdocs do XAMPP. Inicie Apache no painel.

Requisitos: PHP 8.1 ou superior; extensões **PDO**, **pdo_sqlite** e **mbstring**. No XAMPP, use Apache > Config > PHP (php.ini), remova o ponto e vírgula das linhas **extension=pdo_sqlite** e **extension=mbstring**, salve e reinicie Apache caso essas extensões estejam desativadas. Não abra index.php pelo explorador de arquivos: o PHP precisa do Apache.

Se Apache usar outra porta, ajuste o endereço (ex.: http://localhost:8080/pratoperto/). Confirme que o ZIP foi extraído e que não há uma pasta pratoperto extra entre htdocs e index.php.

## Executar com PHP

Na pasta deste projeto:

    php -S 127.0.0.1:8090 -t .

Acesse http://localhost:8090/ no seu computador. Não é necessário npm, Composer, download de fontes ou importar SQL manualmente. O SQLite é criado automaticamente fora da pasta pública, no diretório temporário do sistema, com nome derivado do caminho do projeto. Opcionalmente configure PRATOPERTO_DATA_DIR para um diretório gravável fora do htdocs. O arquivo .env.example é documentação; esta aplicação não carrega arquivos .env.

## Dados de teste

Seis restaurantes e 24 produtos fictícios. Há um restaurante fechado e um item indisponível para explorar esses estados. O carrinho aceita um restaurante por vez, com confirmação antes da substituição. Pedido mínimo considera o subtotal dos itens.

- **BEMVINDO10**: 10% sobre itens, limitado a R$ 15.
- **ENTREGAGRATIS**: desconta a taxa de entrega.
- Um cupom por pedido.
- Pix, cartão e dinheiro são simulações; não há campos de dados reais.
- O acompanhamento avança automaticamente a cada 30 segundos.
- “Ajuda e privacidade” permite excluir registros do visitante atual.

Os registros pertencem à sessão deste navegador; outro perfil ou sessão não vê os mesmos pedidos. A exclusão ou expiração do cookie de sessão impede o acesso ao histórico daquela sessão. Use dados fictícios.

## Testes e documentação

Servidor PHP iniciado em 8090:

    python3 -m unittest discover -s tests -p test_api.py -v

Para navegador, instale o pacote Python playwright e seu Chromium. Pode configurar CHROMIUM_PATH para o executável existente e AXE_PATH para axe.min.js local; sem AXE_PATH as auditorias axe não são executadas:

    python3 -m unittest discover -s tests -p test_browser.py -v

PRATOPERTO_TEST_URL altera a URL local de testes. Não aponte os testes para produtos de terceiros. A documentação do processo e as evidências estão em replica/. A skill fornecida pelo usuário fica em .agents/skills/ e tem sua licença própria.

Sem deploy de produção. Para operação real seriam necessários autenticação durável, operação de restaurantes, gestão de entregas, provedores de pagamento, política de retenção e infraestrutura própria.
