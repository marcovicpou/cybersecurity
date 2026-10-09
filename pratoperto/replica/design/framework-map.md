# Mapeamento

CSS nativo em assets/css/style.css importa assets/css/tokens.css. Valores semânticos, raios e largura correspondem a tokens.json. DM Sans local com licença incluída; fallback system-ui. Nenhum framework ou CDN.

componentes → elementos:
shell → header/main/footer/nav
category/filter → button aria-pressed
restaurant-card → article + link + botão de favorito
product-card → button; opções → fieldset/legend/radio
address/product/cart/confirmation → dialog nativo com títulos acessíveis
quantity → botão/output/botão
checkout → sections + aside; taxas → dl
timeline → ol aria-current step, explicitamente simulada
notification → role=status; erro → role=alert
privacy → conteúdo da rota ajuda + exclusão do visitante atual

Ilustrações alimentares e ícone de tigela são SVG originais. Não há fotos, marcas, código ou assets externos. Motion respeita prefers-reduced-motion.
