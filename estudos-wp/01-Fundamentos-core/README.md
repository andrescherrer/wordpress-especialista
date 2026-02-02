# 01 – Fundamentos Core

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-hooks-actions-filters.php](pratica/01-hooks-actions-filters.php) | Actions, filters, prioridade, hook customizado |
| 2 | [02-wpdb-queries.php](pratica/02-wpdb-queries.php) | Consultas seguras com `$wpdb->prepare()`, insert/update/delete |
| 3 | [03-posts-meta-loop.php](pratica/03-posts-meta-loop.php) | Posts, meta, The Loop, `WP_Query`, loop aninhado |
| 4 | [04-bootstrap-hooks.php](pratica/04-bootstrap-hooks.php) | Quando usar `init`, `plugins_loaded`, `wp`, etc. |
| 5 | [05-plugin-demo-standards.php](pratica/05-plugin-demo-standards.php) | Plugin completo + Coding Standards |
| 6 | [06-template-hierarchy.md](pratica/06-template-hierarchy.md) | Qual template o WP carrega; conditional tags |

**Como usar:** copie trechos para `functions.php` do tema ou vire plugin (o 05 já é plugin). Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – tabelas, funções, checklist (Ctrl+F e acha).
- **Fonte completa:** [001-WordPress-Fase-1-Fundamentos-Core.md](../../001-WordPress-Fase-1-Fundamentos-Core.md) na raiz do repositório.

---

## Objetivos da Fase 1

- Hooks (actions/filters) com prioridade e argumentos corretos
- `$wpdb` com `prepare()`; transações quando precisar
- Posts, meta, The Loop e loops aninhados com `wp_reset_postdata()`
- Bootstrap: usar o hook certo (`init`, `wp`, etc.)
- Template Hierarchy e Coding Standards
