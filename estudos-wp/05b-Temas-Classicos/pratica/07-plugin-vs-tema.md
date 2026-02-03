# Tabela – Plugin vs Tema

O que vai no **tema** (apresentação, layout) vs no **plugin** (lógica, CPT, API); boas práticas (não colocar lógica pesada no tema).

---

| Aspecto | Tema | Plugin |
|---------|------|--------|
| **Apresentação** | Layout, CSS, templates (header, footer, single, archive). | Não define layout do site; pode enfileirar CSS/JS para suas funcionalidades. |
| **Estrutura visual** | Template hierarchy, sidebar, menus, Customizer (cores, logo). | Pode adicionar shortcodes/widgets que o tema exibe. |
| **Lógica de negócio** | Evitar: regras de negócio, integrações, CPT pesados. | CPT, taxonomias, REST API, integrações, jobs, settings. |
| **Dados** | Opções de aparência (theme_mod); evitar armazenar dados de conteúdo. | Options, post types, tabelas; dados do “plugin”. |
| **Troca de tema** | Ao trocar tema, o layout muda; conteúdo (posts, options de plugin) permanece. | Plugin independe do tema; ao desativar, perde funcionalidade e pode perder dados se não for persistido. |
| **Recomendação** | Tema: foco em **como** o site aparece. | Plugin: foco em **o quê** o site faz (funcionalidades, dados). |

---

## Boas práticas

- **Não colocar lógica pesada no tema:** CPT, API, integrações, relatórios → plugin.
- **Tema + plugin:** tema cuida da apresentação; plugin fornece dados (CPT, endpoints) e o tema só exibe (templates, loops).
- **Reutilização:** plugin pode ser usado em outro site com outro tema; tema pode ser trocado sem perder a funcionalidade do plugin.

---

## Recursos

- [Theme Handbook](https://developer.wordpress.org/themes/)
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- Ver **01-estrutura-minima-tema.md** a **06-starter-theme-underscores.md**; estudos-wp/05 (CPT, taxonomias).
