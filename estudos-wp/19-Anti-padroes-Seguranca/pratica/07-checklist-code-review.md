# Checklist de Code Review – Segurança

Use em toda revisão de código. Fonte: **019-WordPress-Fase-19-Anti-padroes-Seguranca.md**.

---

## Input validation

- [ ] Todo input do usuário é validado no servidor (não só no cliente)?
- [ ] Tipos e formatos são verificados?
- [ ] Ranges e limites são validados?

## Output escaping

- [ ] Todo output é escapado?
- [ ] Contexto correto (HTML, atributo, URL, JS)?
- [ ] Dados de banco escapados antes de exibir?

## SQL Injection

- [ ] Todas as queries usam prepared statements?
- [ ] `$wpdb->prepare()` com placeholders %d, %s, %f?
- [ ] Nenhuma concatenação direta de string em SQL?

## Authentication & authorization

- [ ] Permissões verificadas antes de ações sensíveis?
- [ ] `current_user_can()` com capability adequada?
- [ ] Nonces verificados em formulários e ações?
- [ ] Capability específica para recurso quando aplicável (ex.: delete_post, $post_id)?

## Dados sensíveis

- [ ] Dados sensíveis criptografados quando armazenados?
- [ ] Senhas nunca em texto plano (apenas hash)?
- [ ] Tokens e API keys não logados?
- [ ] Dados sensíveis não expostos em APIs públicas (filtrar por capability)?

## File uploads

- [ ] Tipo de arquivo validado (whitelist)?
- [ ] Tamanho limitado?
- [ ] Salvos fora do web root quando possível?

## REST API

- [ ] Endpoints têm `permission_callback` adequado?
- [ ] Inputs validados via `args` (validate_callback, sanitize_callback)?
- [ ] Respostas filtradas (sem IDs desnecessários, sem dados sensíveis)?
- [ ] Rate limiting considerado?

## Error handling

- [ ] Mensagens de erro não expõem informações sensíveis em produção?
- [ ] Stack traces desabilitados em produção?
- [ ] Erros logados de forma segura (sem senha/token)?

## Secrets

- [ ] Nenhum secret hardcoded no código?
- [ ] Secrets vêm de variáveis de ambiente ou wp-config (não versionado)?
- [ ] .env no .gitignore?
- [ ] Rotação de secrets considerada?

## Infraestrutura

- [ ] HTTPS forçado?
- [ ] Banco de dados não acessível publicamente?
- [ ] Firewall e usuário DB com privilégios mínimos?
- [ ] Logs monitorados?

---

**Recursos:** [Fase 12 – Segurança Avançada](../../012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) | OWASP Top 10 | [WordPress Security Handbook](https://developer.wordpress.org/advanced-administration/security/)
