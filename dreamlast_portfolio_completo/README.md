# Dream Last — portfólio e demonstrações PHP

Redesign do projeto original em preto, branco e cinza. O portfólio apresenta os três sistemas do pacote, com páginas individuais e capturas reais das interfaces.

## Executar no XAMPP

1. Extraia a pasta dreamlast_portfolio_completo em C:\xampp\htdocs\.
2. Inicie Apache. Use PHP 8.1 ou superior, com PDO, pdo_mysql e mbstring habilitados.
3. Acesse a pasta pelo Apache. A página principal e os cases funcionam sem banco.
4. Para usar as demos, inicie MySQL/MariaDB e siga a configuração abaixo.

Após copiar a pasta para htdocs, você também pode executar ABRIR-TESTE-XAMPP.bat. O atalho verifica o PHP, abre o painel XAMPP e, depois da sua confirmação, abre o site no navegador. Não instala bancos nem sobrescreve arquivos. Ele considera Apache na porta padrão 80.

Também é possível servir o projeto com o servidor de desenvolvimento do PHP, a partir desta pasta:

    php -d output_buffering=4096 -S 127.0.0.1:8080 -t .

O buffer de saída segue a configuração usual do PHP/XAMPP; as telas originais do ERP dependem dele para os redirecionamentos após envio de formulários.

Não é necessário instalar Node, npm ou compilar assets para utilizar o projeto.

## Banco das demonstrações

As configurações originais foram preservadas em sistemas/*/config/database.php. Ajuste usuário, senha, host e porta para seu banco local.

- **ERP:** importe sistemas/erp/sql/dreamlast_erp.sql em um banco novo. Abra sistemas/erp/. Login original: admin@dreamlast.com / admin123.
- **PDV Pro v4:** importe sistemas/pdv/sql/dreamlast_pdv.sql em um banco novo e, em seguida, sistemas/pdv/sql/migration_v4.sql. Abra sistemas/pdv/. Login original: admin@dreamlast.com / admin123.
- **CRM:** configure o banco e abra sistemas/crm/setup.php. Execute o instalador original e entre em sistemas/crm/. Login original: admin@dreamlast.com.br / Dream@123.

Não importe novamente os scripts de instalação em bancos já utilizados. Para bancos existentes, preserve backups e siga os READMEs originais de cada sistema.

Cada demonstração usa seu próprio cookie de sessão. É possível abrir os três sistemas no mesmo navegador sem misturar usuários e permissões.

## Páginas do portfólio

- index.php: projetos, serviços, sobre, processo e contato.
- projetos/pdv/index.php: case do PDV Pro.
- projetos/erp/index.php: case do ERP.
- projetos/crm/index.php: case do CRM.
- sitemap.php: sitemap XML dessas quatro páginas.
- robots.txt: permite o portfólio e solicita que os buscadores não indexem as demos.

As rotas de case usam diretórios com index.php; não dependem de regras de rewrite. O ERP calcula sua base a partir da localização real, inclusive dentro de uma subpasta.

## Conteúdo e identidade

Os projetos, administradores e e-mail vieram do ZIP. Não foram adicionados projetos, clientes, depoimentos, números de resultado ou canais de contato fictícios. O contato existente, contato.dreamlast@gmail.com, foi mantido com link de e-mail e alternativa para Gmail.

A logo oficial mencionada no briefing não foi anexada. O pacote usa um monograma tipográfico DL e o nome Dream Last em preto e branco, como solução provisória. O favicon original foi preservado. Para aplicar a logo oficial depois, altere a marca nos templates compartilhados (includes/header.php e includes/footer.php) e o favicon em assets/img/.

Os dados dos cases estão centralizados em includes/projects.php. As screenshots WebP foram capturadas dos sistemas do ZIP com suas bases de demonstração. Os painéis mantêm a identidade visual original; o redesign se concentra no portfólio e nas páginas de apresentação.

## Assets e desempenho

- Fonte DM Sans variável hospedada no próprio projeto; licença em assets/fonts/.
- Screenshots WebP em versões de 720 e 1440 pixels.
- Imagens abaixo da área inicial com carregamento lazy e dimensões declaradas.
- Imagem inicial com prioridade de carregamento.
- JavaScript pequeno, sem framework, com menu acessível e reveal discreto.
- Sem Google Fonts, CDN, trackers ou bibliotecas de animação no portfólio.
- Bootstrap 5.3.3, Bootstrap Icons 1.11.3 e Chart.js 4.4.4 do ERP incluídos localmente, com suas licenças.

## Publicação e SEO

Mantenha PHP e as configurações de banco do seu ambiente. Title, description, Open Graph, imagem de compartilhamento e Organization são gerados pelo template.

O endereço público normalmente é inferido pelo PHP. Se houver proxy reverso que termine HTTPS, configure a variável não secreta DREAMLAST_PUBLIC_URL com a origem pública, por exemplo https://seu-dominio.com, sem subpasta. A localização da aplicação é calculada separadamente.

O sitemap fica em sitemap.php na pasta publicada. Se publicar dentro de uma subpasta, coloque/adapte o robots.txt na raiz do domínio e informe o sitemap aos buscadores. robots.txt não substitui controle de acesso.

Antes de expor as demos publicamente, siga as orientações de produção dos READMEs originais: credenciais próprias, HTTPS, senhas de demonstração substituídas e banco de demonstração separado.

## Documentação da entrega

- docs/DESIGN-SYSTEM.md: decisões visuais e tokens.
- docs/VALIDACAO.md: verificações realizadas e seus limites.
- docs/ALTERACOES.md: escopo de alterações no pacote original.
