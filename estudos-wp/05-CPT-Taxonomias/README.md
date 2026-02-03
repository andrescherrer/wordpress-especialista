# 05 – CPT e Taxonomias

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-registrar-cpt.php](pratica/01-registrar-cpt.php) | register_post_type: labels, public, rewrite, supports, capabilities |
| 2 | [02-registrar-taxonomia.php](pratica/02-registrar-taxonomia.php) | register_taxonomy hierárquica e não-hierárquica para CPT |
| 3 | [03-cpt-taxonomia-query.php](pratica/03-cpt-taxonomia-query.php) | WP_Query com tax_query, wp_get_post_terms |
| 4 | [04-funcoes-termos.php](pratica/04-funcoes-termos.php) | get_terms, get_term, wp_get_post_terms, wp_set_post_terms |
| 5 | [05-template-single-archive.md](pratica/05-template-single-archive.md) | single-{cpt}.php, archive-{cpt}.php na Template Hierarchy |
| 6 | [06-boas-praticas.md](pratica/06-boas-praticas.md) | Checklist e equívocos comuns |
| 7 | [07-cpt-rest-gutenberg.php](pratica/07-cpt-rest-gutenberg.php) | CPT com show_in_rest e rest_base (Gutenberg + REST) |
| 8 | [08-cpt-meta-box.php](pratica/08-cpt-meta-box.php) | Meta box para CPT (nonce, capability, update_post_meta) |
| 9 | [09-archive-filtro-tax.php](pratica/09-archive-filtro-tax.php) | Archive com filtro por termo (tax_query na URL) |
| 10 | [10-cpt-parametros-tabela.md](pratica/10-cpt-parametros-tabela.md) | Tabela register_post_type (supports, capabilities, labels) |

**Como usar:** copie trechos para um plugin ou tema; registrar no hook **init**. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – CPT, taxonomias, query, funções (Ctrl+F).
- **Fonte completa:** [005-WordPress-Fase-5-CPT-Taxonomias.md](../../005-WordPress-Fase-5-CPT-Taxonomias.md) na raiz do repositório.

---

## Objetivos da Fase 5

- Registrar CPT com register_post_type() (labels, public, rewrite, has_archive, supports, capabilities)
- Registrar taxonomias com register_taxonomy() (hierárquica vs não-hierárquica)
- Associar taxonomias a CPTs; queryar com WP_Query e tax_query
- Usar get_terms, wp_get_post_terms, wp_set_post_terms
- Saber single-{cpt}.php e archive-{cpt}.php na Template Hierarchy
