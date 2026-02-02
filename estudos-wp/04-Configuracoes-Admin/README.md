# 04 – Configurações Admin (Settings API)

**Foco: prática.** Teoria só quando precisar, sempre à mão.

---

## Comece pela prática

| # | Arquivo | O que você vai fazer |
|---|---------|----------------------|
| 1 | [01-settings-api-pagina.php](pratica/01-settings-api-pagina.php) | Menu admin, register_setting, seções, campos, form com options.php |
| 2 | [02-registrar-campos-sanitize.php](pratica/02-registrar-campos-sanitize.php) | add_settings_field, callbacks de render, sanitize_callback |
| 3 | [03-meta-boxes.php](pratica/03-meta-boxes.php) | add_meta_box, nonce, save_post com verificação de segurança |
| 4 | [04-admin-notices-scripts.php](pratica/04-admin-notices-scripts.php) | admin_notices, admin_enqueue_scripts, wp_localize_script |
| 5 | [05-validacao-settings-error.md](pratica/05-validacao-settings-error.md) | add_settings_error no sanitize_callback |
| 6 | [06-boas-praticas.md](pratica/06-boas-praticas.md) | Checklist e equívocos comuns |

**Como usar:** copie trechos para um plugin; menu em `admin_menu`, settings em `admin_init`. Detalhes em [pratica/README.md](pratica/README.md).

---

## Teoria quando precisar

- **No código:** cada arquivo `.php` tem no topo um bloco **Referência rápida** com a sintaxe daquele tópico.
- **Uma página:** [REFERENCIA-RAPIDA.md](REFERENCIA-RAPIDA.md) – Settings API, menus, meta boxes, notices (Ctrl+F).
- **Fonte completa:** [004-WordPress-Fase-4-Configuracoes-Admin.md](../../004-WordPress-Fase-4-Configuracoes-Admin.md) na raiz do repositório.

---

## Objetivos da Fase 4

- Usar Settings API: register_setting, add_settings_section, add_settings_field
- Criar páginas admin (add_menu_page, add_submenu_page, add_options_page)
- Form com settings_fields() + do_settings_sections() e action="options.php"
- Validar e sanitizar com sanitize_callback; add_settings_error em caso de erro
- Meta boxes (add_meta_boxes, add_meta_box) e save_post com nonce e capability
- Admin notices e enfileirar CSS/JS no admin (admin_enqueue_scripts)
