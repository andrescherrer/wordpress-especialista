# 06 – Shortcodes, Widgets e Gutenberg

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-shortcode-basico.php](pratica/01-shortcode-basico.php) | add_shortcode, shortcode_atts, sanitize e escape |
| 2 | [02-shortcode-atributos-conteudo.php](pratica/02-shortcode-atributos-conteudo.php) | Shortcode com conteúdo interno e atributos (alert) |
| 3 | [03-widget-classico.php](pratica/03-widget-classico.php) | WP_Widget: construct, widget(), form(), update(), register_widget |
| 4 | [04-bloco-dinamico-php.php](pratica/04-bloco-dinamico-php.php) | register_block_type com render_callback (bloco dinâmico em PHP) |
| 5 | [05-block-patterns.md](pratica/05-block-patterns.md) | register_block_pattern com conteúdo HTML dos blocos |
| 6 | [06-boas-praticas.md](pratica/06-boas-praticas.md) | Segurança, performance e equívocos comuns |
| 7 | [08-shortcode-box-conteudo.php](pratica/08-shortcode-box-conteudo.php) | Shortcode com conteúdo envolto [box]content[/box] |
| 8 | [09-widget-classe-completa.php](pratica/09-widget-classe-completa.php) | Widget completo (form, update, widget) |
| 9 | [10-block-minimo.php](pratica/10-block-minimo.php) | Block mínimo (attributes, render_callback) |
| 10 | [11-quando-usar-qual.md](pratica/11-quando-usar-qual.md) | Tabela shortcode vs widget vs block (quando usar) |
| 11 | [12-bloco-js-estrutura.md](pratica/12-bloco-js-estrutura.md) | Estrutura de um bloco JS (src/, block.json, edit/save) |
| 12 | [13-block-json-completo.md](pratica/13-block-json-completo.md) | block.json completo – exemplo mínimo copiável |
| 13 | [14-edit-js-minimo.md](pratica/14-edit-js-minimo.md) | edit.js mínimo (React, useBlockProps, RichText) |
| 14 | [15-save-js-serializacao.md](pratica/15-save-js-serializacao.md) | save.js e serialização; estático vs dinâmico |
| 15 | [16-build-wordpress-scripts.md](pratica/16-build-wordpress-scripts.md) | Build com npm e @wordpress/scripts |
| 16 | [17-checklist-bloco-js.md](pratica/17-checklist-bloco-js.md) | Checklist – Meu primeiro bloco JS |

**Recursos externos (blocos JS):** [Create Block](https://developer.wordpress.org/block-editor/getting-started/create-block/), [Block Editor Handbook](https://developer.wordpress.org/block-editor/).

**Como usar:** copie trechos para um plugin ou tema. Shortcodes/widgets em init ou plugins_loaded; blocos em init com function_exists('register_block_type'). Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – shortcodes, widgets, blocos (Ctrl+F).
- **Fonte completa:** [006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md](../../006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md) na raiz do repositório.

---

## Objetivos da Fase 6

- Criar shortcodes (simples, com atributos, com conteúdo) usando shortcode_atts e escape
- Construir widget clássico com WP_Widget (widget, form, update)
- Registrar bloco Gutenberg dinâmico com render_callback em PHP
- Conhecer block patterns (register_block_pattern)
- Aplicar sanitização e escape em shortcodes/widgets
