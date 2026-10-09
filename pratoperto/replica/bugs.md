# Defeitos reproduzidos e resolvidos

## B01 — S2: foco saía da janela por Tab
Passos: abrir diálogo de endereço; pressionar Tab 15 vezes.
Esperado: foco permanece entre os controles do diálogo até fechar.
Observado: document.activeElement ficava fora do diálogo.
Correção: ciclo explícito primeiro/último controle visível habilitado, incluindo Shift+Tab. Escape nativo e retorno ao acionador preservados.
Regressão: test_dialog_keyboard_address_unicode_and_privacy_reset.

## B02 — S3: glyph de seta sem desenho na fonte local
Passos: abrir home no Chromium do ambiente.
Esperado: seta legível nos links dos restaurantes.
Observado: caixa no lugar de alguns caracteres de seta.
Correção: ícones SVG originais inline para setas, cesta e home.
Evidência: screenshots iniciais inspecionadas e substituídas por capturas finais.

## B03 — S3: foco após regenerar checkout
Passos: aplicar cupom no checkout; formulário original era substituído.
Esperado: foco de navegação no conteúdo atual.
Observado: foco retornava ao primeiro link da página em algumas interações.
Correção: foco no main após regenerar checkout; filtros/favoritos mantêm foco no controle correspondente.

Nenhum S1/S2 aberto na execução final do recorte. Falhas iniciais dos próprios testes (nome de produto errado, espaço não quebrável no formato monetário, inicialização do cliente de testes) foram corrigidas no harness e não contabilizadas como defeitos do produto.
