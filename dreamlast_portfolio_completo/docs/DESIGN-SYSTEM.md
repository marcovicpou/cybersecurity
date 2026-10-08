# Design system — Dream Last

## Direção

Portfólio editorial com bastante espaço, composição monocromática e projetos apresentados em imagens grandes. No desktop, os projetos alternam a posição da imagem; no mobile, a imagem vem antes do texto.

A marca tipográfica DL é provisória, pois a logo oficial não foi enviada. Não há alegação de que esse monograma seja a identidade oficial da empresa.

## Tokens

| Uso | Valor |
| --- | --- |
| Fundo principal | #f7f7f4 |
| Superfície secundária | #edede9 |
| Área de projetos | #ffffff |
| Texto principal | #171717 |
| Texto secundário | #545450 |
| Texto auxiliar | #666662 |
| Bordas claras | #d8d8d2 |
| Seções escuras | #171717 e #202020 |
| Bordas escuras | #383838 |
| Fonte | DM Sans variável, local |
| Largura máxima | 1320px |
| Radius | 4px |
| Transição padrão | 180ms |
| Reveal | 350ms, deslocamento de 12px |
| Escala de espaço | 8, 12, 16, 20, 24, 32, 40, 64, 80, 112px |

Títulos usam peso 500 e espaçamento entre letras ajustado. O hero tem título limitado a 64px, com adaptações até 33px em telas estreitas. Os estilos e variáveis estão em assets/css/style.css.

## Interações

- Botões com alteração discreta de fundo.
- Links de navegação com underline de 180ms.
- Screenshots com zoom máximo de 2% no hover.
- Conteúdo entra uma única vez, sem bloquear o scroll.
- prefers-reduced-motion remove animações e smooth scrolling.
- Sem gradientes decorativos, glow, partículas, parallax ou carrossel.

## Acessibilidade e adaptação

Navegação semântica, link para pular ao conteúdo, foco visível, labels do menu, estado aria-expanded, fechamento com Escape e imagens com texto alternativo. O menu é uma lista de navegação expansível, sem comportamento de diálogo modal. O JavaScript não bloqueia a rolagem.

As quatro páginas foram verificadas nas larguras 320, 360, 375, 390, 414, 430, 768, 1024, 1280, 1440 e 1920px. O layout se adapta em 1100, 960, 760 e 430px. Uma folha de estilo de fallback mantém a navegação utilizável sem JavaScript.

## Recursos citados no briefing

Taste Skill, UI UX Pro Max, Design Motion Principles e Magic MCP não estavam disponíveis nesta sessão; não foram invocados. A implementação usou a skill HTML Code Generator e suas orientações de portfólio, com hierarquia, espaço, consistência, movimento discreto e revisão no Chromium.
