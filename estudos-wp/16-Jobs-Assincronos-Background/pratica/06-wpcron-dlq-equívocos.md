# WP-Cron, DLQ e equívocos comuns

Fonte: **016-WordPress-Fase-16-Jobs-Assincronos-Background.md** (Limitações WP-Cron, Equívocos).

---

## Limitações do WP-Cron

- **Não é cron real:** Dispara em requisições HTTP; sem visitas = tarefas podem não rodar no horário.
- **Produção:** Definir em wp-config: `define('DISABLE_WP_CRON', true);` e usar cron do sistema, ex.: `*/15 * * * * curl -s https://seusite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1`
- **Ainda assim:** Não cobre bem jobs assíncronos one-time, retry, prioridade, múltiplos workers, DLQ. Para isso use **Action Scheduler** (ou fila externa).

---

## Quando WP-Cron “falha”

- **Pouco tráfego:** Job “a cada 6h” pode rodar a cada 2–3 dias.
- **Vários servidores:** Cada instância pode rodar o mesmo schedule = execuções duplicadas.
- **Job longo:** Timeout PHP (ex.: 30s) interrompe; job não termina.
- **Falhas silenciosas:** Sem retry automático; sem visibilidade de falha.

---

## Equívocos comuns

1. **“WP-Cron é confiável em produção”** – Só roda com requisições. Use cron real + Action Scheduler para jobs críticos.
2. **“Async sempre melhora performance”** – Melhora tempo de resposta (UX), não reduz trabalho total. Tem custo de complexidade e recursos.
3. **“Idempotência é só para API”** – Idempotência é importante em webhooks, jobs e pagamentos (retries podem duplicar efeitos).
4. **“DLQ é só para jobs falhados”** – DLQ é para falhas **após** todas as tentativas; serve para análise e retry manual, não retry automático.
5. **“Mais workers = sempre mais rápido”** – Gargalos (DB, API externa) limitam ganho. Medir e escalar com critério.
