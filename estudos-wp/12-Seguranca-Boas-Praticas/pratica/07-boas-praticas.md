# Boas práticas e equívocos – Segurança

Resumo. Fonte: **012-WordPress-Fase-12-Seguranca-Boas-Praticas.md**.

---

## Princípios

- **Validar entrada, sanitizar dados, escapar saída** – em toda interação com usuário
- **Nunca confiar, sempre verificar** – nonce, capability, tipo de arquivo
- **Defesa em profundidade** – várias camadas (escape + CSP + validação)
- **Menor privilégio** – capabilities mínimas necessárias
- **Falhar de forma segura** – em dúvida, negar acesso ou não executar ação
- **Segurança por design** – desde o desenho do código, não como remendo

---

## Ferramentas úteis

- **Análise estática:** PHPCS (WordPress Coding Standards), PHPStan, Psalm
- **Segurança:** Wordfence, Sucuri, iThemes Security; Composer audit / npm audit
- **Monitoramento:** Sentry, logs de acesso, WP Security Audit Log
- **Testes:** PHPUnit, testes de permissão e validação

---

## Equívocos comuns

1. **“WordPress é inseguro”**  
   O core é mantido e seguro quando atualizado. Muitos problemas vêm de plugins, temas e má configuração.

2. **“Só escapar resolve XSS”**  
   Escaping é uma camada essencial, mas é preciso também validar entrada, CSP e não confiar em conteúdo de terceiros sem filtro.

3. **“Nonce expira após um uso”**  
   Nonces expiram por tempo (padrão ~24h) ou ao fazer logout, não após um único uso. Podem ser reutilizados no período de validade.

4. **“Prepared statements resolvem toda SQL injection”**  
   Resolvem para **valores** (WHERE id = %d). Nomes de tabela/coluna dinâmicos não entram em prepare; usar whitelist.

5. **“Plugin de segurança deixa tudo seguro”**  
   Plugins de segurança ajudam (firewall, scan), mas não substituem código seguro, atualizações e configuração correta.
