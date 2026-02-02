# Boas práticas – CPT e Taxonomias

**Referência rápida:** Prefixo no nome (evitar conflito); show_in_rest para REST API; map_meta_cap com capabilities; labels completos; supports conforme necessidade; flush_rewrite_rules após mudar rewrite.

---

## Checklist

- [ ] **CPT e taxonomia** registrados no hook **init**
- [ ] **Nome único** com prefixo (ex: `estudos_wp_livro` ou `livro` em contexto de plugin próprio)
- [ ] **show_in_rest => true** se for usar REST API / Gutenberg
- [ ] **map_meta_cap => true** no CPT quando usar capability_type/capabilities customizadas
- [ ] **supports** definido (title, editor, thumbnail, excerpt, custom-fields, etc.)
- [ ] **rewrite** com slug amigável; **has_archive** se quiser página de listagem
- [ ] **Labels** preenchidos para melhor UX no admin
- [ ] Taxonomia **registrada depois** do CPT (ou no mesmo init)
- [ ] **flush_rewrite_rules()** na ativação do plugin após register_post_type

---

## Equívocos comuns

1. **"CPT precisa de tabela no banco"** – Não. CPT usa `wp_posts` e `wp_postmeta`; a coluna `post_type` diferencia.

2. **"Taxonomia é só categoria"** – Taxonomia é o sistema; categoria e tag são exemplos. Hierárquica = tipo categoria; não-hierárquica = tipo tag.

3. **"CPT aparece na REST API sozinho"** – Só com **show_in_rest => true** no register_post_type.

4. **"register_post_type pode ser em qualquer hook"** – O correto é **init** (ou after_setup_theme em tema); antes do WP precisar do CPT.

---

## WP-CLI (debug)

```bash
wp post-type list
wp taxonomy list
wp post list --post_type=livro
wp term list genero_livro
```

---

*Fonte: 005-WordPress-Fase-5-CPT-Taxonomias.md*
