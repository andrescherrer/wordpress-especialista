# Tabela – Sanitize e validate por tipo de campo (Settings)

Resumo: qual sanitização usar em sanitize_callback. Fonte: **004-WordPress-Fase-4-Configuracoes-Admin.md**.

---

## Sanitize por tipo

| Tipo de campo | Sanitize recomendado | Observação |
|---------------|------------------------|------------|
| Texto (input) | `sanitize_text_field` | Remove tags e caracteres perigosos |
| Email | `sanitize_email` | Validação: `is_email($value)` no validate_callback |
| Número inteiro | `absint` | Para IDs e quantidades |
| Textarea (HTML permitido) | `wp_kses_post` | Tags permitidas pelo post |
| Textarea (tags específicas) | `wp_kses($value, $allowed)` | Definir array de tags permitidas |
| Checkbox | Callback que retorna '1' ou '0' | `function ($v) { return $v ? '1' : '0'; }` |
| URL | `esc_url_raw` | Para salvar URL |
| Select/radio | `sanitize_text_field` + validar enum | Validar contra lista de valores permitidos |

---

## Validação (validate_callback ou no sanitize)

- Em falha: `add_settings_error('option_id', 'code', 'Mensagem'); return get_option('option_id');` (manter valor anterior).
- Tipos: `is_email()`, `absint()` com range, `in_array($value, $allowed, true)`.
