# Blocos e a11y

Requisitos de a11y para **blocos** (Block Editor Handbook); teste com **leitor de tela**; **foco** e **ordem de tab**.

---

## Requisitos para blocos

- **Texto alternativo** em imagens e mídia.
- **Labels** em controles do bloco (edit.js); botões com texto ou **aria-label**.
- **Ordem de foco** lógica no editor (Tab percorre controles na ordem esperada).
- **Contraste** nos estilos do bloco (front e editor).
- **Estrutura semântica** no markup salvo (headings, listas, landmarks quando fizer sentido).

---

## Teste com leitor de tela

- **NVDA** (Windows), **VoiceOver** (macOS/iOS), **TalkBack** (Android): navegar pelo bloco no editor e no front; verificar se nomes e estados são anunciados corretamente.
- Garantir que ícones e botões tenham **aria-label** ou texto visível.

---

## Foco e ordem de tab

- No **edit** do bloco, a ordem de Tab deve seguir a ordem visual (título → conteúdo → opções).
- Modais e dropdowns: **trapar** o foco dentro do modal; ao fechar, devolver o foco ao elemento que abriu.
- **useBlockProps** já adiciona atributos de acessibilidade quando aplicável; conferir no DOM gerado.

---

## Referência

- [Block Editor – Accessibility](https://developer.wordpress.org/block-editor/how-to-guides/accessibility/)
- Ver **01-principios-a11y-wordpress.md** e **06-checklist-a11y-plugin.md**.
