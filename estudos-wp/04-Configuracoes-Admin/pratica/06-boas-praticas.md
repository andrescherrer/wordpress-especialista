# Boas práticas – Configurações Admin

**Referência rápida:** Settings API = estrutura + sanitize; form action="options.php"; nonce em todo form; capability manage_options; meta box com nonce + save_post seguro; admin_enqueue_scripts só no hook da página.

---

## Checklist

- [ ] **register_setting** com **sanitize_callback**; tipo correto (array/string)
- [ ] Form de configuração com **action="options.php"**, **settings_fields()** e **do_settings_sections()**
- [ ] Verificar **current_user_can( 'manage_options' )** antes de renderizar página
- [ ] **name** dos campos no formato **option_name[key]** quando a option for array
- [ ] Sempre **esc_attr()**, **checked()**, **selected()** nos campos
- [ ] Meta box: **wp_nonce_field** + em save_post: **wp_verify_nonce**, **!autosave/revision**, **current_user_can( 'edit_post' )**, **update_post_meta** com sanitize
- [ ] **admin_enqueue_scripts**: carregar CSS/JS apenas no **$hook** da página (ex: `toplevel_page_meu-plugin`)
- [ ] Validação no sanitize: em caso de erro usar **add_settings_error** e retornar valor anterior
- [ ] Admin notices com classe **notice notice-success|error|warning|info** e **is-dismissible**

---

## Equívocos comuns

1. **"Settings API salva sozinha"** – A API dá a estrutura; o form com action **options.php** é que dispara o salvamento. O **sanitize_callback** processa os dados antes de salvar.

2. **"Nonce é opcional"** – Nonce é obrigatório para proteção CSRF. **settings_fields()** já inclui nonce; em formulários customizados use **wp_nonce_field()**.

3. **"Mesma sanitização para tudo"** – Use o tipo certo: texto → sanitize_text_field; email → sanitize_email; URL → esc_url_raw; número → absint/floatval; checkbox → isset ? 1 : 0.

4. **"Meta box só em post"** – add_meta_box aceita qualquer **$screen**: 'post', 'page', ou slug de CPT.

---

## Estrutura sugerida no plugin

```
plugin/
├── plugin.php              # admin_menu, admin_init, add_meta_boxes, save_post
├── admin/
│   ├── class-settings.php  # register_setting, sections, fields, sanitize
│   ├── admin.css
│   └── admin.js
└── ...
```

---

*Fonte: 004-WordPress-Fase-4-Configuracoes-Admin.md*
