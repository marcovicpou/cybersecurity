# Replica Recon — recorte de delivery

## Alvo e evidência

Referência solicitada: https://www.ifood.com.br. Em 2026-10-09 (UTC), curl e Chromium retornaram bloqueio do proxy (HTTP CONNECT 403 / ERR_TUNNEL_CONNECTION_FAILED). O conteúdo da página web atual não foi inspecionado. Nova tentativa chegou ao servidor, mas continuou HTTP 403. Não foram copiados código, bundles, endpoints, texto, marcas, imagens ou dados do produto de referência.

| Fonte | Finalidade | Resultado |
| --- | --- | --- |
| https://www.ifood.com.br/ | Interface pública e fluxo principal | Bloqueado pelo proxy |
| https://institucional.ifood.com.br/ | Contexto público | HTTP 403 na nova tentativa |
| https://play.google.com/store/apps/details?id=br.com.brainweb.ifood | Listagem e feedback público | GET 200; descrição e 3 reviews públicos inspecionados |
| https://apps.apple.com/br/app/ifood-delivery/id386099915 | Listagem e feedback público | URL informada retornou 404; identificação atual pendente |

Domínios necessários foram salvos no rascunho de rede; salvar o rascunho não aplica o acesso. A inspeção e a comparação visual atuais permanecem pendentes. A descrição pública da listagem Android, inspecionada em 2026-10-09, confirma busca, categorias, cupons, sacola, acompanhamento e categorias de restaurantes. Não estabelece telas web, regras exatas ou modelo de dados. Os demais detalhes abaixo continuam hipóteses de implementação.

## Recorte e core loop

Aplicação web local, XAMPP/PHP. Consumidor visitante escolhe endereço de demonstração, busca restaurante, consulta cardápio, escolhe itens/opções, revisa frete e pedido, finaliza sem cobrança, acompanha uma linha do tempo simulada e consulta/cancela pedidos próprios. Catálogo sintético, marca distinta e ilustrações próprias. Projeto M.

## Telas

- S01 /#/inicio: catálogo, categorias, busca, filtros, ordenação, favoritos. Estados: carregando, vazio, erro, offline, mobile.
- S02 diálogo Endereço: rua, número, bairro, cidade, complemento. Validação e dados de exemplo opcionais.
- S03 /#/restaurante/slug: informações, categorias de menu, disponibilidade, modal de item e observações. Restaurante fechado e item indisponível impedem adição.
- S04 painel Carrinho: quantidades, remoção, observações, total, taxa e mínimo. Troca de restaurante exige confirmação.
- S05 /#/checkout: endereço, resumo, cupom e pagamento fictício; confirmação idempotente, erros, sem dados de cartão.
- S06 /#/pedido/uuid: detalhes e etapas simuladas; cancelamento confirmado; desconhecido/inacessível retorna erro.
- S07 /#/pedidos: histórico do visitante atual, vazio e acesso ao detalhe.
- S08 /#/ajuda: escopo da demonstração, privacidade e limpeza dos dados locais.

## Fluxos

- F01: abrir endereço → salvar → catálogo. Caminho feliz, campos vazios, Unicode e texto longo.
- F02: buscar/filtrar/ordenar/favoritar → restaurante. Vazio, categoria/termo combinados e offline.
- F03: escolher item/opção/quantidade → carrinho → editar. Item indisponível, máximo de quantidade, restaurante fechado e troca de restaurante.
- F04: checkout → cupom/endereço → finalizar → detalhe. Mínimo, cupom inválido, requisição duplicada, total adulterado, sessão expirada, CSRF e concorrência de abas.
- F05: histórico → detalhe → cancelar. Histórico vazio, pedido de outro visitante e repetição de cancelamento.
- F06: ajuda → apagar dados → catálogo. Confirmação, exclusão de pedidos/favoritos/endereço/carrinho da identidade atual.

## Componentes

Header e tabs mobile, chip de categoria, busca com label, filtros, cards, etiqueta de demonstração, modal acessível, stepper de quantidade, drawer de carrinho, toast, resumo de custos, timeline e estado vazio. Diálogos usam foco inicial, Escape e retorno ao acionador.

## Modelo inferido

Visitante anônimo UUID → endereço/um carrinho/favoritos/múltiplos pedidos. Restaurantes UUID → produtos UUID. Pedidos UUID → snapshots de itens/opções/custos. Relações são decisão de implementação com confiança alta para o protótipo, sem conhecimento do modelo interno do alvo.

## Dificuldades

Idempotência e isolamento entre visitantes; cálculo confiável de preços/opções/cupom; navegação e diálogo acessíveis no mobile. Fora do recorte: rede real de lojas/entregadores, mapa/geocodificação, avaliações reais, contas sociais, pagamentos reais, comunicações externas e rastreamento GPS. Não há cópia de ativos da referência.

## Evidência pública adicional

A listagem Android descreve delivery de restaurantes, mercado, bebidas, farmácia e pet shop. Este recorte cobre somente restaurantes fictícios. Não recria vertical de mercado/farmácia/pet, clube de assinatura, agendamento, localização em tempo real ou rede real. Três reviews exibidos na página foram coletados com citações curtas, autor e data em reviews.csv; nenhuma nota/métrica global da referência é usada no protótipo.
