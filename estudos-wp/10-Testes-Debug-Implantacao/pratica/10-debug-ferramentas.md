# Debug – WP_DEBUG, SCRIPT_DEBUG, Query Monitor

Resumo de ferramentas de debug. Fonte: **010-WordPress-Fase-10-Testes-Debug-Implantacao.md**.

---

## Constantes (wp-config.php)

| Constante | Efeito |
|-----------|--------|
| **WP_DEBUG** | true = exibir erros PHP (nunca true em produção) |
| **WP_DEBUG_LOG** | true = gravar em wp-content/debug.log |
| **WP_DEBUG_DISPLAY** | false = não exibir na tela (só log) |
| **SCRIPT_DEBUG** | true = carregar .js/.css não minificados |
| **SAVEQUERIES** | true = armazenar queries em $wpdb->queries (pesado) |

---

## Exemplo wp-config (desenvolvimento)

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

---

## Query Monitor (plugin)

- Plugin que exibe queries, hooks, condicionais, tempo de carregamento.
- Útil para encontrar N+1, queries lentas e dependências de scripts/styles.
- Desativar em produção.

---

## error_log()

- `error_log('mensagem ' . print_r($var, true));` para inspecionar variáveis sem exibir na tela.
- Saída em debug.log (se WP_DEBUG_LOG) ou no log do servidor.
