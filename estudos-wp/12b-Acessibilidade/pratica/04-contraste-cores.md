# Contraste e cores

Razão de **contraste** (WCAG AA); não depender **só de cor** para informação; ferramentas (ex.: axe DevTools).

---

## WCAG AA – contraste

- **Texto normal:** razão de contraste mínima **4.5:1** entre texto e fundo.
- **Texto grande** (18px+ ou 14px+ bold): **3:1**.
- **Elementos de UI e gráficos:** **3:1** em relação ao fundo adjacente.

---

## Não depender só de cor

- Não transmitir informação **apenas** pela cor (ex.: “campos em vermelho são obrigatórios” – adicionar ícone ou texto).
- Links: além da cor, sublinhado ou outro indicador visual (ou pelo menos contraste adequado e foco visível).

---

## Ferramentas

- **axe DevTools** (extensão ou integrado): analisa a página e aponta problemas de contraste e a11y.
- **Lighthouse** (Chrome): auditoria de acessibilidade inclui contraste.
- **Color Contrast Analyzer** (TPGi): verificar razão entre duas cores.
- **Contrast checker** (WebAIM): inserir hex de foreground e background.

---

## No WordPress

- Temas e plugins devem garantir contraste nos estilos (admin e front).
- Block Editor e theme.json: usar paleta com cores que atendam WCAG quando aplicável.

Ver **06-checklist-a11y-plugin.md** e **07-recursos-a11y.md**.
