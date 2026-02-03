# theme.json – estrutura

Campos principais do **theme.json**: `version`, `settings`, `styles`, `templateParts`.

---

## Campos principais

| Campo | Descrição |
|-------|-----------|
| **`version`** | Versão do schema (ex.: 2). |
| **`settings`** | Configurações que geram variáveis e presets: typography (fontSizes, fontFamilies), color (palette), spacing, etc. |
| **`styles`** | Estilos aplicados globalmente (ex.: cor de fundo do body, tipografia base). |
| **`templateParts`** | Definição das partes reutilizáveis (ex.: header, footer) – nome, área, arquivo. |

---

## Exemplo mínimo por seção

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        { "slug": "primary", "color": "#0073aa", "name": "Primary" },
        { "slug": "secondary", "color": "#333333", "name": "Secondary" }
      ]
    },
    "typography": {
      "fontSizes": [
        { "slug": "small", "size": "14px", "name": "Small" },
        { "slug": "medium", "size": "18px", "name": "Medium" },
        { "slug": "large", "size": "24px", "name": "Large" }
      ]
    },
    "spacing": {
      "units": ["px", "em", "rem", "%"]
    }
  },
  "styles": {
    "color": {
      "background": "var(--wp--preset--color--primary)",
      "text": "var(--wp--preset--color--secondary)"
    },
    "typography": {
      "fontSize": "var(--wp--preset--font-size--medium)"
    }
  },
  "templateParts": [
    { "name": "header", "title": "Header", "area": "header" },
    { "name": "footer", "title": "Footer", "area": "footer" }
  ]
}
```

- **settings** define paleta e font sizes; o core gera variáveis CSS `--wp--preset--color--*`, `--wp--preset--font-size--*`.
- **styles** aplica esses presets ao body/global.
- **templateParts** associa nome/título à área (header, footer); os arquivos ficam em `parts/` (ex.: `parts/header.html`, `parts/footer.html`).

---

## Referência

- [theme.json reference](https://developer.wordpress.org/block-editor/reference-guides/theme-json/) – todos os campos.
- **03-theme-json-estilos-globais.md** – paleta, tipografia e variáveis CSS geradas.
