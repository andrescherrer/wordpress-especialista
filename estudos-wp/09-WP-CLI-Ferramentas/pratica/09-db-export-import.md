# DB export/import – Uso no deploy

Resumo: usar `wp db export` e `wp db import` em scripts de deploy. Fonte: **009-WordPress-Fase-9-WP-CLI-Ferramentas.md**.

---

## Export

```bash
# Exportar para arquivo com data
wp db export backup-$(date +%Y%m%d).sql

# Exportar apenas estrutura (sem dados)
wp db export estrutura.sql --no-data
```

---

## Import

```bash
# Importar (cuidado: sobrescreve o banco atual)
wp db import backup-20260202.sql

# Em deploy: geralmente importar dump de staging ou inicial
wp db import ../dumps/initial.sql
```

---

## Fluxo típico no deploy

1. **Backup antes de atualizar:** `wp db export backup-pre-deploy.sql`
2. **Migrar/atualizar código** (git pull, composer, etc.)
3. **Rodar migrations** (se houver): `wp meu-plugin migrate`
4. **Se falhar:** restaurar com `wp db import backup-pre-deploy.sql`

---

## Dica

- Manter dumps fora do web root ou em diretório ignorado pelo Git.
- Em CI/CD, usar secrets para credenciais; não versionar arquivos .sql com dados reais.
