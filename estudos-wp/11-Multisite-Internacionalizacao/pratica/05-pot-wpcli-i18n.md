# Gerar POT, estrutura de idiomas e WP-CLI i18n

Referência. Fonte: **011-WordPress-Fase-11-Multisite-Internacionalizacao.md**.

---

## Estrutura de diretórios

```
meu-plugin/
├── languages/
│   ├── meu-plugin.pot       # Template (strings a traduzir)
│   ├── meu-plugin-pt_BR.po  # Português Brasil
│   ├── meu-plugin-pt_BR.mo  # Compilado (WordPress usa)
│   ├── meu-plugin-es_ES.po
│   └── meu-plugin-es_ES.mo
```

---

## Gerar arquivo POT (WP-CLI)

```bash
# Na raiz do plugin
wp i18n make-pot . languages/meu-plugin.pot --domain=meu-plugin

# Com exclusões
wp i18n make-pot . languages/meu-plugin.pot \
  --domain=meu-plugin \
  --exclude=vendor,node_modules,tests
```

Requer [wp-cli/i18n-command](https://github.com/wp-cli/i18n-command) (geralmente já incluído no WP-CLI 2.x).

---

## Header do plugin (Domain Path)

No arquivo principal do plugin:

```php
/**
 * Plugin Name: Meu Plugin
 * ...
 * Text Domain: meu-plugin
 * Domain Path: /languages
 */
```

---

## Compilar PO para MO

- **Poedit:** Abrir o .po, salvar – gera o .mo automaticamente.
- **Linha de comando:** `msgfmt -o meu-plugin-pt_BR.mo meu-plugin-pt_BR.po`
- **WP-CLI:** não há comando nativo; usar script ou msgfmt.

---

## WP-CLI i18n (comandos customizados)

Você pode registrar subcomandos no seu plugin, por exemplo:

- `wp meu-plugin i18n makepot` – chama `wp i18n make-pot` com o path do plugin.
- `wp meu-plugin i18n missing --locale=pt_BR` – lista strings do POT sem tradução no .po.
- `wp meu-plugin i18n compile` – compila todos os .po em .mo (via exec msgfmt).

A extração de strings é feita pelo `wp i18n make-pot`; os comandos customizados só automatizam path, domain e compilação.
