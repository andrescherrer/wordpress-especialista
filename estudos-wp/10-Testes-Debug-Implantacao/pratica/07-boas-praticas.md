# Boas práticas – Testes, Deploy e Monitoramento

Checklist. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**.

---

## Testes

- [ ] Escrever testes para código novo (unitários e, quando fizer sentido, integração)
- [ ] Manter cobertura relevante (ex.: > 80% em código crítico; priorizar comportamento, não só %)
- [ ] Rodar testes localmente antes do commit
- [ ] CI deve rodar testes em todo PR/push
- [ ] Usar data providers para vários casos; mock/stub para dependências externas
- [ ] Nomes de teste descritivos (testa_o_que_em_qual_condicao)

---

## Qualidade de código

- [ ] Seguir WordPress Coding Standards (PHPCS)
- [ ] Usar linters (PHPCS, ESLint) no CI
- [ ] Code review antes de merge
- [ ] Documentar código e decisões complexas

---

## Performance e segurança

- [ ] Monitorar queries (evitar N+1; usar cache quando apropriado)
- [ ] Validar e sanitizar inputs; escapar outputs
- [ ] Prepared statements em todas as queries com dados do usuário
- [ ] Verificar capabilities antes de ações sensíveis

---

## Deploy

- [ ] Plano de rollback documentado e testado
- [ ] Testar em staging antes de produção
- [ ] Backup completo (DB + arquivos) antes do deploy
- [ ] Checklist executado (testes, migrations, cache, health check)
- [ ] Monitorar nas primeiras horas após deploy
- [ ] Modo manutenção durante deploy quando aplicável

---

## Monitoramento

- [ ] Error tracking (ex.: Sentry) em produção
- [ ] Alertas para erros críticos
- [ ] Métricas de performance (tempo de resposta, uso de memória)
- [ ] Logs estruturados e revisão periódica

---

## Equívocos comuns

1. **Cobertura 100% ≠ zero bugs** – Cobertura mostra o que foi executado; priorize testes que garantem comportamento correto.
2. **Só testes unitários** – Inclua testes de integração onde há WP, DB ou APIs.
3. **Deploy = só copiar arquivos** – Inclua migrations, cache, health check e rollback.
4. **Blue-Green sempre melhor** – Depende de orçamento e infraestrutura; Canary pode ser mais econômico para rollout gradual.
