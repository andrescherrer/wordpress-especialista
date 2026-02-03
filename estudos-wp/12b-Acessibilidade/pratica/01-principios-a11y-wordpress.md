# Princípios a11y no WordPress

Resumo: **navegação por teclado**, **contraste**, **texto alternativo**, **ARIA** quando necessário; requisitos do repositório de plugins (.org).

---

## Princípios gerais

| Princípio | Descrição |
|-----------|------------|
| **Navegação por teclado** | Todas as ações principais devem ser acessíveis por teclado (Tab, Enter, Esc); ordem de foco lógica. |
| **Contraste** | Texto e fundo com razão de contraste adequada (WCAG AA: 4.5:1 para texto normal; 3:1 para texto grande). |
| **Texto alternativo** | Imagens com `alt` descritivo; ícones decorativos com `alt=""` ou `aria-hidden="true"`. |
| **ARIA quando necessário** | Usar ARIA para descrever comportamento (aria-label, aria-expanded, role) quando o HTML semântico não bastar. |
| **Formulários** | Labels associados aos campos (`<label for="id">` ou aria-label); mensagens de erro associadas ao campo. |

---

## Requisitos do repositório de plugins (WordPress.org)

- Plugins submetidos ao .org passam por **revisão de acessibilidade**.
- Exigências comuns: contraste adequado, foco visível, labels em formulários, botões com texto ou aria-label, links descritivos.
- Documentação: [WordPress Accessibility](https://make.wordpress.org/accessibility/), [Plugin Handbook – Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/).

---

## Resumo

- Garantir **teclado**, **contraste**, **alt** e **labels**; usar **ARIA** para complementar semântica.
- Ver **02-escape-semantica.md**, **03-aria-basico.md**, **04-contraste-cores.md** e **06-checklist-a11y-plugin.md** para detalhes.
