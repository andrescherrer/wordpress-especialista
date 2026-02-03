# theme.json – estilos globais

Definição de **paleta de cores**, **tipografia** (font sizes, font families) e uso em blocos; variáveis CSS geradas pelo core.

---

## Paleta de cores (settings.color.palette)

```json
"settings": {
  "color": {
    "palette": [
      { "slug": "primary", "color": "#0073aa", "name": "Primary" },
      { "slug": "secondary", "color": "#333333", "name": "Secondary" },
      { "slug": "background", "color": "#ffffff", "name": "Background" }
    ]
  }
}
```

O core gera variáveis CSS:

- `--wp--preset--color--primary`
- `--wp--preset--color--secondary`
- `--wp--preset--color--background`

Nos blocos (e no Site Editor) o usuário escolhe “Primary”, “Secondary” etc.; no front o valor usado é a variável.

---

## Tipografia (fontSizes, fontFamilies)

```json
"typography": {
  "fontSizes": [
    { "slug": "small", "size": "14px", "name": "Small" },
    { "slug": "medium", "size": "18px", "name": "Medium" },
    { "slug": "large", "size": "24px", "name": "Large" }
  ],
  "fontFamilies": [
    { "slug": "sans", "fontFamily": "system-ui, sans-serif", "name": "Sans" },
    { "slug": "serif", "fontFamily": "Georgia, serif", "name": "Serif" }
  ]
}
```

Variáveis geradas (exemplos):

- `--wp--preset--font-size--small`, `--medium`, `--large`
- `--wp--preset--font-family--sans`, `--serif`

---

## Uso em blocos

No editor, os blocos que suportam cor e tipografia passam a oferecer os presets do tema (dropdown “Primary”, “Small”, “Sans”, etc.). No HTML/CSS o valor final é a variável ou o valor equivalente.

---

## styles globais

Em **styles** você aplica esses presets ao corpo ou a elementos globais:

```json
"styles": {
  "color": {
    "background": "var(--wp--preset--color--background)",
    "text": "var(--wp--preset--color--secondary)"
  },
  "typography": {
    "fontSize": "var(--wp--preset--font-size--medium)",
    "fontFamily": "var(--wp--preset--font-family--sans)"
  }
}
```

Ver **02-theme-json-estrutura.md** para a estrutura completa do theme.json.
