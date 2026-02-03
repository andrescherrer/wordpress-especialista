# Checklist – A11y para plugin

Formulários com label; botões com texto ou aria-label; contraste; teclado; link para WordPress Accessibility.

---

- [ ] **1. Formulários**  
  Todo campo tem `<label for="...">` ou `aria-label`; mensagens de erro associadas (aria-describedby ou aria-invalid).

- [ ] **2. Botões**  
  Botões têm texto visível ou `aria-label`; ícones decorativos com `aria-hidden="true"` ou fora do nome acessível.

- [ ] **3. Links**  
  Texto do link é descritivo (evitar “clique aqui”); links que abrem em nova aba com aviso (texto ou aria-label).

- [ ] **4. Contraste**  
  Texto e fundo com razão de contraste WCAG AA (4.5:1 texto normal; 3:1 texto grande); não depender só de cor para informação.

- [ ] **5. Teclado**  
  Todas as ações principais acessíveis por teclado; ordem de Tab lógica; foco visível (outline ou estilo equivalente).

- [ ] **6. Imagens**  
  Imagens informativas com `alt` descritivo; decorativas com `alt=""` ou `role="presentation"`/`aria-hidden="true"`.

- [ ] **7. Estrutura**  
  Uso de headings em ordem (h1 → h2 → h3); landmarks (`<nav>`, `<main>`) quando fizer sentido.

- [ ] **8. Blocos (se aplicável)**  
  Controles do bloco com labels; ordem de foco no editor; markup salvo semântico.

- [ ] **9. Teste**  
  Rodar axe DevTools ou Lighthouse (acessibilidade); testar com leitor de tela (NVDA, VoiceOver).

---

## Recursos

- [WordPress Accessibility](https://make.wordpress.org/accessibility/)
- [WCAG 2.1 Quick Reference](https://www.w3.org/WAI/WCAG21/quickref/)
- [Block Editor – Accessibility](https://developer.wordpress.org/block-editor/how-to-guides/accessibility/)

Documentos desta pasta: **01-principios-a11y-wordpress.md** a **05-blocos-a11y.md**, **07-recursos-a11y.md**.
