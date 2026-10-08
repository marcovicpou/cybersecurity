# Validação da entrega

## Ambiente utilizado

PHP 8.4.24 com PDO MySQL, mbstring e output_buffering=4096; MariaDB 11.8.6; Chromium headless controlado por Playwright. Os sistemas foram executados a partir do projeto entregue, com os scripts SQL originais e registros de demonstração.

PHP e MariaDB foram preparados localmente para os testes; binários, banco de teste, sessões e caches de ferramentas não fazem parte do ZIP.

## Verificações aprovadas

| Verificação | Resultado |
| --- | --- |
| Sintaxe PHP | 69 arquivos aprovados |
| Sintaxe JavaScript do portfólio | Aprovada |
| Página principal e três cases | HTTP 200, títulos, metadados e imagens carregados |
| Responsividade | 44 combinações: 4 páginas × 11 larguras |
| Larguras | 320, 360, 375, 390, 414, 430, 768, 1024, 1280, 1440, 1920px |
| Overflow horizontal | Não detectado nas 44 combinações |
| Console JS e respostas HTTP dos assets | Sem erros nos testes do portfólio |
| Navegação mobile | Abrir/fechar, estado ARIA, link para seção e Escape aprovados |
| Teclado | Link de pular conteúdo e retorno de foco do menu aprovados |
| Movimento reduzido | Conteúdo visível e efeitos removidos |
| JavaScript desabilitado | Projetos e navegação permanecem disponíveis no mobile |
| Referências internas | 99 referências conferidas |
| Sitemap | XML válido, quatro páginas do portfólio |
| Auditoria axe-core 4.11 | Oito execuções: quatro páginas em 390 e 1440px; zero violações automáticas WCAG A/AA |
| Contraste dos textos HTML | Verificação complementar de cores, tamanho e peso no navegador |
| PDV | 16 páginas autenticadas aprovadas |
| ERP | 11 páginas autenticadas aprovadas |
| CRM | 13 páginas autenticadas aprovadas |
| ERP produtos | Cadastro e edição do módulo original aprovados |
| PDV venda | Produto no carrinho, finalização, pagamento em dinheiro, troco, comprovante e baixa de estoque aprovados |
| Sessões simultâneas | Três demos no mesmo navegador, sem compartilhar usuários; logout do PDV preserva ERP/CRM |
| Preservação | Scripts SQL e módulo original de produtos idênticos ao ZIP |

Dados criados para validar cadastro e venda foram removidos do banco local após as verificações.

## Revisão visual

Capturas de página inteira nas larguras 320, 390, 768 e 1440px complementaram as verificações automáticas. Foram revisadas a composição do desktop e do mobile, a ordem das imagens/textos, o hero, a apresentação dos cases, o contato e o rodapé. As imagens dos sistemas foram novamente capturadas após ativar mbstring e incluir as dependências locais do ERP.

## Limites

- A logo oficial não foi anexada; a marca DL é provisória.
- As páginas autenticadas das demos passaram por checks de navegação e ausência de erros PHP. Isso não equivale a testar todas as combinações possíveis de ações de cada módulo.
- As demos preservam seus estilos e funcionalidades originais. A matriz de 11 larguras se refere à página principal e aos três cases.
- Não foram testados câmera física, leitor USB/Bluetooth, impressora térmica ou integrações fiscais e de pagamento externas.
- Testes ocorreram no Chromium e no ambiente local, sem publicação em hospedagem real e sem testes adicionais em Safari/Firefox.
- Auditoria automatizada de acessibilidade não substitui avaliação completa com pessoas e leitores de tela. As verificações de contraste não certificam textos dentro das screenshots.
- As otimizações de fonte, imagens e carregamento foram implementadas. Não há alegação de pontuação Lighthouse ou aprovação de Core Web Vitals de campo.
