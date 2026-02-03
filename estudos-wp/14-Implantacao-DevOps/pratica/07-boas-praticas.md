# Boas práticas – Implantação e DevOps

Fonte: **014-WordPress-Fase-14-Implantacao-DevOps.md** (14.8, 14.9, 14.10, Resumo).

---

## Monitoring

- **Sentry (ou similar):** DSN por ambiente; release = versão da app; em produção reduzir tracesSampleRate; capturar exceções e erros fatais (set_error_handler, set_exception_handler).
- **Logs:** arquivo com rotação (ex.: wp-content/logs/app.log); em produção considerar Syslog ou serviço gerenciado; formato estruturado (JSON) facilita busca; nunca logar senhas ou tokens.
- **Performance:** em dev, logar tempo de resposta, memória e número de queries no shutdown; em produção usar APM se necessário.
- **Uptime:** endpoint `/health` e checagem externa (cron ou serviço); alertas se falhar.

---

## Disaster recovery

- **RTO (Recovery Time Objective):** tempo máximo para voltar ao ar; define prioridade e tipo de procedimento.
- **RPO (Recovery Point Objective):** perda máxima aceitável de dados; define frequência de backup e necessidade de PITR.
- **Procedimentos:** documentar passo a passo (restore DB, restore arquivos, DNS, etc.); manter em local acessível (wiki, runbook).
- **Testes:** rodar restore e simulação de DR periodicamente; treinar equipe.
- **Backups em localização separada:** outro datacenter ou cloud; não depender só do mesmo servidor.

---

## Checklist Fase 14 (resumo)

- [ ] **Dev:** Docker Compose, PHP/Nginx/Redis, WP-CLI, health checks.
- [ ] **Staging:** ambiente espelhando produção; sync DB/assets; testes automatizados.
- [ ] **Produção:** servidor, PHP/Nginx/MySQL otimizados, SSL, Redis.
- [ ] **Git:** .gitignore completo, conventional commits, branching, pre-commit.
- [ ] **CI/CD:** pipeline com test, security, build, deploy por branch; secrets configurados.
- [ ] **Deploy:** script com backup → pull → composer → migrations → cache → permissões → health.
- [ ] **Rollback:** script testado; backups identificáveis.
- [ ] **Backup:** script automático, retenção, cópia remota, teste de restore.
- [ ] **Monitoring:** erros (Sentry), logs, uptime/health.
- [ ] **DR:** RTO/RPO definidos, procedimentos documentados, testes e equipe treinada.

---

## Equívocos comuns

1. **Committar .env ou wp-config com senhas** – sempre usar variáveis de ambiente e .gitignore.
2. **Deploy sem backup** – sempre backup antes de pull/composer.
3. **Nunca testar restore** – backup sem teste de restore pode falhar na hora do incidente.
4. **Health check só no deploy** – ter monitoramento contínuo (uptime) e alertas.
5. **Secrets em logs ou echo no CI** – nunca expor senhas ou chaves em output do pipeline.
6. **Um único local de backup** – manter cópia remota (outro servidor ou S3).

---

## Referência rápida

- Uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
- Teoria completa: [../../014-WordPress-Fase-14-Implantacao-DevOps.md](../../014-WordPress-Fase-14-Implantacao-DevOps.md).
