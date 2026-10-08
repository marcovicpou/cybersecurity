# Alterações em relação ao ZIP original

## Portfólio

- Evolução de index.php, assets/css/style.css e assets/js/main.js.
- Remoção dos efeitos decorativos neon, glow, marquee e do dashboard fictício.
- Preservação dos três projetos e de suas funcionalidades na apresentação.
- Inclusão de serviços, processo de seis etapas, sobre e contato.
- Templates compartilhados e dados de projeto centralizados.
- Três páginas de case com problema, solução, tecnologias, funcionalidades, galeria, resultado e projetos relacionados.
- Capturas reais das demos, fonte local, favicon monocromático e imagem de compartilhamento.
- SEO e navegação por teclado, menu mobile e suporte a movimento reduzido.
- README atualizado e documentação da entrega.

## Demonstrações: ajustes de integração

A lógica de vendas, compras, estoque, financeiro, cadastro e relatórios foi mantida. Os scripts SQL originais permanecem idênticos.

- ERP: BASE_URL passa a seguir a localização real da aplicação, em vez de exigir /dreamlast_erp.
- ERP: Bootstrap, Bootstrap Icons e Chart.js passam a ser servidos localmente, nas mesmas versões.
- ERP: caminho do favicon corrigido para funcionar nos módulos internos.
- PDV, ERP e CRM: nomes de sessão distintos para evitar conflito ao abrir demos simultâneas.
- CRM: instalador usa o mesmo nome de sessão do CRM.

O módulo de produtos do ERP foi restaurado do ZIP após uma conclusão incorreta da inspeção inicial; seu código original foi preservado.

O repositório cybersecurity e os anexos originais não foram alterados.
