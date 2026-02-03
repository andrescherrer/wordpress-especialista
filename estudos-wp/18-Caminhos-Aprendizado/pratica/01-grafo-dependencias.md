# Grafo de dependências entre fases

Fonte: **018-WordPress-Fase-18-Caminhos-Aprendizado.md** (Dependency Graph).

---

## Grafo (texto)

```
Fase 1 (Core)
  ├─→ Fase 2 (REST Fundamentals)
  │     ├─→ Fase 3 (REST Advanced) ──→ Fase 16 (Async)
  │     ├─→ Fase 4 (Settings) ──→ Fase 6 (Shortcodes/Gutenberg)
  │     ├─→ Fase 5 (CPT/Taxonomies) ──→ Fase 6
  │     └─→ Security Essentials
  ├─→ Fase 7 (WP-CLI)
  │     ├─→ Fase 9 (WP-CLI Advanced) ──→ Fase 14 (DevOps)
  │     └─→ Fase 8 (Performance)
  └─→ Testing Throughout (documento)

Fase 13 (Arquitetura) ← agrega vários
  ├─→ Fase 11 (Multisite/i18n)
  └─→ Fase 14 (DevOps) ──→ Fase 16 (Async)
```

---

## Pré-requisitos e tempo por fase

| Fase | Pré-requisitos | Tempo (horas) |
|------|----------------|---------------|
| 1 – Core | Nenhum | 20–30 |
| 2 – REST Fundamentals | 1 | 15–20 |
| 3 – REST Advanced | 1, 2 | 15–20 |
| 4 – Settings API | 1, 2 | 10–15 |
| 5 – CPT/Taxonomies | 1, 2 | 15–20 |
| 6 – Shortcodes/Gutenberg | 1, 2, 4, 5 | 20–25 |
| 7 – WP-CLI | 1 | 10–15 |
| 8 – Performance | 1, 7 | 15–20 |
| 9 – WP-CLI Advanced | 1, 7 | 15–20 |
| 10 – Testing/Debug | 1–9 recomendado | 20–25 |
| 11 – Multisite/i18n | 1–10 recomendado | 15–20 |
| 12 – Segurança | 1, 2 (Security Essentials) | 15–20 |
| 13 – Arquitetura | 1–10 recomendado | 25–30 |
| 14 – DevOps | 1–9, 13 recomendado | 20–25 |
| 16 – Async Jobs | 1–3, 13 recomendado | 20–25 |

**Regra:** Respeitar pré-requisitos para não pular base; Testing Throughout e Security Essentials aplicados ao longo do caminho.
