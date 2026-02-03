# Fluxo POT → PO → MO (i18n)

Resumo: gerar POT, traduzir em PO, compilar MO. Fonte: **011-WordPress-Fase-11-Multisite-Internacionalizacao.md**.

---

## 1. Gerar POT (template de tradução)

```bash
# WP-CLI (wp i18n make-pot)
wp i18n make-pot wp-content/plugins/meu-plugin wp-content/plugins/meu-plugin/languages/meu-plugin.pot --domain=meu-plugin
```

- O POT contém todas as strings extraídas de `__()`, `_e()`, etc. do plugin/tema.
- Domínio deve coincidir com o usado no código (ex.: `meu-plugin`).

---

## 2. Criar/editar PO (por idioma)

- Copiar o `.pot` para `meu-plugin-pt_BR.po` (ou usar Poedit, Loco, etc.).
- Traduzir cada `msgid` em `msgstr`.
- Salvar o `.po`.

---

## 3. Compilar MO (binário)

- O WordPress carrega arquivos `.mo` (não `.po`).
- Poedit compila automaticamente; ou: `msgfmt -o meu-plugin-pt_BR.mo meu-plugin-pt_BR.po`.
- Colocar em `languages/` do plugin: `languages/meu-plugin-pt_BR.mo`.

---

## Estrutura sugerida

```
plugin/
  languages/
    meu-plugin.pot
    meu-plugin-pt_BR.po
    meu-plugin-pt_BR.mo
```

- `load_plugin_textdomain('meu-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');`
