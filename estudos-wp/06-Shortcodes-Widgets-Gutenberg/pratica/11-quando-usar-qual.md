# Quando usar: Shortcode vs Widget vs Block

Resumo para escolher a extensão certa. Fonte: **006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md**.

---

## Shortcode

| Quando usar | Exemplo |
|-------------|---------|
| Conteúdo inserido **no meio do texto** (post/page) | Alertas, citações, botões, formulários simples |
| Output controlado por **atributos e/ou conteúdo** | [alert tipo="info"]Texto[/alert] |
| Não precisa de **área de widget** nem de **bloco no editor** | Conteúdo que o autor cola no editor clássico ou em blocos “Shortcode” |

**Limitação:** no Gutenberg, fica dentro do bloco “Shortcode”; não é um bloco nativo com preview rico.

---

## Widget (clássico)

| Quando usar | Exemplo |
|-------------|---------|
| Conteúdo em **sidebars e áreas de widget** (footer, barra lateral) | Lista de posts, formulário de busca, CTA |
| Configuração no **Customize > Widgets** ou **Aparência > Widgets** | Título + texto + link |
| Não precisa estar **dentro do conteúdo** do post | Menus, banners, “Sobre o autor” |

**Limitação:** em temas full block (FSE), widgets podem ser substituídos por blocos (Template Part, etc.).

---

## Block (Gutenberg)

| Quando usar | Exemplo |
|-------------|---------|
| Conteúdo que deve ser **bloco nativo** no editor (arrastar, configurar no painel) | CTA, depoimento, preço, FAQ |
| **Preview** no editor igual ao front | Melhor UX para o editor |
| Integração com **design do tema** (estilos, espaçamento) | Blocos reutilizáveis e patterns |

**Custo:** exige JavaScript (React) para edit ou apenas PHP (render_callback) para bloco dinâmico simples.

---

## Resumo

- **Só no conteúdo do post/page, simples** → shortcode.
- **Só em sidebar/footer, configurável** → widget.
- **No editor como bloco, com preview** → block.
