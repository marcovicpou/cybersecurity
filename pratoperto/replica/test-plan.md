# Plano e execução de QA local

Ambiente: Linux, PHP 8.4.24 com PDO SQLite/mbstring, Chromium /usr/bin/chromium, Python Playwright, axe-core 4.11.0. Testes consultam somente 127.0.0.1:8090. XAMPP Windows não está instalado nesta máquina; entrega usa PHP/SQLite compatíveis, mas execução em Windows não foi observada.

| Fluxo | Casos efetivamente automatizados |
| --- | --- |
| F01 | Endereço fictício; vazio/tipo errado; Unicode; limite 101 caracteres; exemplo; salvar |
| F02 | Busca/categoria; vazio; limpar; entrega grátis; favoritos; ordenação por tempo; abertos |
| F03 | Porção maior; nota Unicode/HTML exibido como texto; quantidade; item fechado/indisponível; confirmação de substituição |
| F04 | Checkout/valores; cupons inválido/válido/teto/remoção; mínimo; endereço; duplicação idempotente; total adulterado; CSRF inválido; versão antiga de carrinho/checkout |
| F05 | Reload do detalhe; histórico/cancelamento; dupla solicitação de cancelamento; isolamento entre clientes |
| F06 | Exclusão do visitante; novo CSRF; preservação dos dados de outro cliente |
| Interface | Home sem overflow em 360/390/768/1440; fluxo móvel 390; Tab em diálogo/Escape/retorno de foco; rede offline→erro→retry; rota desconhecida |

Auditados com axe (WCAG 2 A/AA e 2.1 A/AA): home nas quatro larguras, modal de produto, carrinho desktop/mobile, checkout desktop/mobile, pedido desktop, histórico e ajuda. Auditoria automatizada não substitui verificação manual com leitores de tela ou demonstra conformidade completa.

Não executados: leitor de tela real, navegador Safari/Firefox, XAMPP/Windows, slow network/servidor sob carga, DST por mudança real do relógio, limpeza do cookie via expiração temporal. Datas armazenadas UTC e formatadas pela timezone do navegador; proteção de sessão validada por CSRF antigo e clientes independentes. Não há email/OAuth/SMS/payment handoff real.

Comando executado com AXE_PATH e CHROMIUM_PATH configurados:

    python3 -m unittest discover -s tests -v

A suíte verifica erros de página/HTTP 5xx em cada cenário de navegador. Resultados finais constam em test-results.md.
