# 11 – Multisite e Internacionalização (i18n/l10n)

**Foco: prática.** Multisite (switch_to_blog, network options), i18n (text domain, POT/PO/MO) e RTL.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-multisite-basico.php](pratica/01-multisite-basico.php) | is_multisite, switch_to_blog, get_sites, ativação por site |
| 2 | [02-network-options-admin.php](pratica/02-network-options-admin.php) | get_site_option, update_site_option, menu network admin |
| 3 | [03-i18n-funcoes-traducao.php](pratica/03-i18n-funcoes-traducao.php) | load_plugin_textdomain, __(), _e(), _x(), _n(), esc_attr_e |
| 4 | [04-js-rtl.php](pratica/04-js-rtl.php) | wp_localize_script para JS; RTL (wp_style_add_data, is_rtl) |
| 5 | [05-pot-wpcli-i18n.md](pratica/05-pot-wpcli-i18n.md) | Gerar POT (wp i18n make-pot), estrutura languages, WP-CLI |
| 6 | [06-site-vs-network-options.md](pratica/06-site-vs-network-options.md) | get_option vs get_site_option; quando usar cada um |
| 7 | [07-boas-praticas.md](pratica/07-boas-praticas.md) | Checklist i18n e Multisite; equívocos comuns |
| 8 | [08-multisite-switch-blog.php](pratica/08-multisite-switch-blog.php) | switch_to_blog / restore_current_blog com get_option |
| 9 | [09-i18n-funcoes-template.php](pratica/09-i18n-funcoes-template.php) | __(), _e(), esc_html__() em template |
| 10 | [10-pot-po-mo-fluxo.md](pratica/10-pot-po-mo-fluxo.md) | Fluxo POT → PO → MO (wp i18n make-pot) |

**Como usar:** copie as classes/trechos para seu plugin. Header do plugin: `Network: true` para ativação em rede. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada `.php` tem no topo um bloco **REFERÊNCIA RÁPIDA**.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Multisite, options, funções de tradução, POT (Ctrl+F).
- **Fonte completa:** [011-WordPress-Fase-11-Multisite-Internacionalizacao.md](../../011-WordPress-Fase-11-Multisite-Internacionalizacao.md) na raiz do repositório.

---

## Objetivos da Fase 11

- Verificar e usar Multisite: is_multisite(), switch_to_blog/restore_current_blog, get_sites(), hooks wp_insert_site/wp_delete_site
- Diferenciar opções de site e de rede: get_option vs get_site_option, update_site_option
- Implementar i18n: load_plugin_textdomain, __(), _e(), _x(), _n(), esc_attr_e; gerar POT e arquivos PO/MO
- Traduzir JavaScript (wp_localize_script ou wp_set_script_translations) e suporte RTL
- Aplicar checklist de internacionalização e Multisite
