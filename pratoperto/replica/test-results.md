# Resultado final — 2026-10-09 UTC

- API: 10 testes executados, 10 passaram.
- Navegador: 6 cenários executados, 6 passaram.
- Suíte combinada final: 16 testes, 16 passaram, 0 falhas/erros, 23,554 s.
- Axe-core 4.11.0 foi carregado efetivamente por AXE_PATH; zero violações nos contextos listados no plano.
- Zero pageerror ou respostas HTTP 5xx nos cenários de navegador.
- Larguras testadas: 360, 390, 768 e 1440px; sem overflow horizontal da home.
- Contraste dos tokens: 5 pares, 0 falhas AA.
- Brand sweep: exit 0, nenhum termo configurado encontrado no produto.
- Listing lint do rascunho: exit 0.
- Screenshots reais em clone-screens/. Apenas imagens do protótipo.

PHP e JavaScript passaram também nas verificações de sintaxe. XAMPP/Windows, leitores de tela reais, Safari/Firefox e integração real de pagamento/entrega não testados. Não existe deploy público.

Verificação adicional do pacote: o ZIP foi extraído em outro diretório e servido no subcaminho /pratoperto/; 10 testes de API e 6 cenários de navegador passaram novamente. Isso verifica estrutura, arquivos e caminhos relativos, sem simular uma execução Windows.
