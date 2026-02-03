# Planejamento – Expansão de conteúdo e exemplos (estudos-wp)

**Objetivo:** definir onde e como adicionar mais exemplos e conteúdo em cada pasta da `estudos-wp`, mantendo coerência e progressão.

**Resposta direta:** sim, cabe uma quantidade maior de exemplos. O documento abaixo detalha o plano.

---

## 1. Situação atual

- Cada fase tem: **README.md**, **REFERENCIA-RAPIDA.md**, pasta **pratica/** com **README.md** + **5 a 8 arquivos** (em geral 6–7).
- Conteúdo da `pratica/`: mix de `.php` (exemplos executáveis) e `.md` (referência, checklist, resumo).
- Poucos arquivos por tópico; muitos assuntos da fase ficam com um único exemplo ou só teoria na referência.

**Conclusão:** há espaço para mais exemplos sem poluir a estrutura; o plano abaixo organiza essa expansão.

---

## 2. Estratégia de expansão

### 2.1 Opções de estrutura

| Opção | Descrição | Prós | Contras |
|-------|-----------|------|--------|
| **A** | Aumentar arquivos em `pratica/` (de ~7 para 10–12 por fase) | Tudo no mesmo lugar; README já lista arquivos | Tabela do README fica longa; risco de misturar “base” com “extra” |
| **B** | Nova subpasta `pratica/exemplos/` (ou `pratica/extra/`) com mais arquivos por tópico | Base continua enxuta; exemplos adicionais agrupados | Dois níveis em pratica/; README precisa explicar onde está o quê |
| **C** | Nova subpasta `exemplos/` na raiz de cada fase, ao lado de `pratica/` | Separação clara: pratica = essencial, exemplos = aprofundamento | Mais uma pasta por fase; índice duplicado (README + exemplos) |

**Recomendação:** **Opção A** para a maioria das fases (ampliar `pratica/` para 10–12 arquivos), e **Opção B** apenas em fases muito densas (ex.: 02, 03, 12, 13), com `pratica/exemplos/` para cenários adicionais.

### 2.2 Tipos de conteúdo a adicionar

1. **Exemplos por cenário**  
   Um mesmo tópico com 2–3 cenários (ex.: REST: listar, criar, atualizar, deletar em arquivos separados ou seções bem marcadas).

2. **Errado vs correto**  
   Arquivos ou seções “anti-padrão” + “solução” (especialmente em 12, 19, 20).

3. **Mini fluxos completos**  
   Um fluxo de ponta a ponta (ex.: “formulário admin → sanitize → save → listagem”) em um único arquivo comentado.

4. **Exercícios com solução**  
   Arquivo “exercício” (enunciado + dicas) e, opcionalmente, “solução” em outro arquivo ou no final do mesmo.

5. **Referência em tabela**  
   `.md` com tabelas de funções/parâmetros/hooks daquele tópico (complementar ao REFERENCIA-RAPIDA.md).

6. **Checklist por tópico**  
   Checklist curto (segurança, performance, boas práticas) específico daquela fase.

---

## 3. Plano por fase (o que adicionar)

Cada linha é uma **sugestão concreta** de novo arquivo ou bloco. “+N” = até N novos itens (arquivos ou seções grandes).

### Fase 01 – Fundamentos Core

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | Exemplo **transações** com `$wpdb` (BEGIN/COMMIT/ROLLBACK) | PHP | `pratica/07-wpdb-transacoes.php` |
| 2 | Exemplo **múltiplos filters** em cascata (prioridade, remoção) | PHP | `pratica/08-filters-cascata.php` |
| 3 | **Estrutura de diretórios** WP (wp-admin, wp-includes, wp-content) + constantes | MD | `pratica/09-estrutura-constantes.md` |
| 4 | **Tabela de hooks** de bootstrap (plugins_loaded, init, wp, etc.) com “o que já está disponível” | MD | Incluir em REFERENCIA-RAPIDA ou `pratica/10-hooks-bootstrap-tabela.md` |

**Total sugerido:** +3 a +4 itens (ficar com 9–10 arquivos em pratica/).

---

### Fase 02 – REST API Fundamentos

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | Exemplo **GET** (listar) + **POST** (criar) em rotas separadas ou um controller com dois métodos | PHP | `pratica/06-endpoints-get-post.php` ou em 02-controller |
| 2 | Exemplo **WP_Error** + status HTTP (400, 404, 403) em resposta | PHP | `pratica/07-erros-status-http.php` |
| 3 | **Autenticação**: Application Password e/ou nonce em header (exemplo mínimo) | PHP | Ampliar `05-autenticacao-seguranca.md` ou `pratica/08-auth-application-password.php` |
| 4 | Tabela **args** (validate_callback, sanitize_callback, required, type) | MD | Seção em REFERENCIA-RAPIDA ou `pratica/09-args-validacao-tabela.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 03 – REST API Avançado

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Schema** (tipos, default, description) em register_rest_route | PHP | `pratica/08-schema-response.php` |
| 2 | **Paginação** (page, per_page) + headers (X-WP-Total, X-WP-TotalPages) | PHP | `pratica/09-paginacao-headers.php` |
| 3 | **Retry/fallback** em chamada externa (ex.: wp_remote_get com retry) | PHP | `pratica/10-retry-fallback.md` ou .php |
| 4 | Checklist **REST avançado** (schema, permissions, pagination, errors) | MD | `pratica/11-checklist-rest-avancado.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 04 – Configurações Admin

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Tabs** em página de settings (ex.: Aba Geral / Aba Avançado) | PHP | `pratica/08-settings-tabs.php` |
| 2 | **add_settings_section** + **add_settings_field** completo (um exemplo com 3–4 tipos de campo) | PHP | Reforçar em 01 ou `pratica/09-campos-tipos.php` |
| 3 | **Admin AJAX** (wp_ajax_*, nonce, resposta JSON) | PHP | `pratica/10-admin-ajax.php` |
| 4 | Tabela **sanitize/validate** por tipo de campo | MD | REFERENCIA-RAPIDA ou `pratica/11-sanitize-tabela.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 05 – CPT e Taxonomias

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | CPT **com suporte a Gutenberg** (show_in_rest, rest_base) | PHP | `pratica/07-cpt-rest-gutenberg.php` |
| 2 | **Meta box** para CPT (salvar meta no save_post) | PHP | `pratica/08-cpt-meta-box.php` |
| 3 | **archive** com filtro por termo (tax_query na URL) + single com termos | PHP | `pratica/09-archive-filtro-tax.php` |
| 4 | Tabela **register_post_type** (supports, capabilities, labels resumidos) | MD | REFERENCIA-RAPIDA ou `pratica/10-cpt-parametros-tabela.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 06 – Shortcodes, Widgets e Gutenberg

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Shortcode** com atributos + conteúdo envolto ([$tag]content[/$tag]) | PHP | Reforçar ou `pratica/08-shortcode-atributos-conteudo.php` |
| 2 | **Widget** com form() + update() + widget() (classe WP_Widget) | PHP | `pratica/09-widget-classe-completa.php` |
| 3 | **Block** (registerBlockType) mínimo: save, edit, attributes | PHP | `pratica/10-block-minimo.php` |
| 4 | Tabela **shortcode vs widget vs block** (quando usar) | MD | `pratica/11-quando-usar-qual.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 07 – WP-CLI Fundamentos

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | Comando com **argumento posicional** e **assoc** | PHP | `pratica/08-comando-args-assoc.php` |
| 2 | **When** (registrar comando só quando WP carregado) + mensagem de erro amigável | PHP | `pratica/09-when-wp-load.php` |
| 3 | Tabela **comandos úteis** (core, plugin, db, user, option) | MD | REFERENCIA-RAPIDA ou `pratica/10-comandos-uteis-tabela.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 08 – Performance e Cache

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Object cache** (wp_cache_get/set/add/delete) com grupo e TTL | PHP | `pratica/09-object-cache.php` |
| 2 | **Transient** (set_transient, get_transient, delete_transient) + expiração | PHP | `pratica/10-transients.php` |
| 3 | **Lazy load** de script/style (enqueue condicional, defer) | PHP | `pratica/11-enqueue-condicional.php` |
| 4 | Checklist **performance** (cache, queries, assets, transients) | MD | `pratica/12-checklist-performance.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 09 – WP-CLI Ferramentas

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Progress bar** (WP_CLI\Utils\make_progress_bar) em loop | PHP | `pratica/08-progress-bar.php` |
| 2 | **db query** + **export/import** (exemplo de uso no deploy) | MD/PHP | `pratica/09-db-export-import.md` |
| 3 | Tabela **subcomandos** típicos (list, get, create, delete) | MD | REFERENCIA-RAPIDA ou `pratica/10-subcomandos-tabela.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 10 – Testes, Debug e Implantação

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Integração** com WordPress (bootstrap, WP_UnitTestCase) | MD | Reforçar 01 ou `pratica/08-bootstrap-wp-unit.md` |
| 2 | Teste de **filter** e **action** (assert chamada, ordem) | PHP | `pratica/09-testes-hooks.php` |
| 3 | **Debug**: WP_DEBUG, SCRIPT_DEBUG, query monitor (resumo) | MD | `pratica/10-debug-ferramentas.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 11 – Multisite e Internacionalização

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **switch_to_blog** / **restore_current_blog** com get_option por site | PHP | `pratica/08-multisite-switch-blog.php` |
| 2 | **__()**, **_e()**, **esc_html__()** em template + texto de domínio | PHP | `pratica/09-i18n-funcoes-template.php` |
| 3 | **.pot** (wp i18n make-pot) + **.po/.mo** (resumo de fluxo) | MD | `pratica/10-pot-po-mo-fluxo.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 12 – Segurança e Boas Práticas

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Capability** por recurso (current_user_can('edit_post', $id)) em REST e admin | PHP | Reforçar ou `pratica/08-capability-recurso.php` |
| 2 | **CORS / security headers** (X-Frame-Options, CSP resumido) | MD | `pratica/09-security-headers.md` |
| 3 | **Upload** seguro (allowed types, size, wp_handle_upload) | PHP | `pratica/10-upload-seguro.php` |
| 4 | Checklist **segurança** (input, output, SQL, nonce, capabilities) | MD | Reforçar 06 ou `pratica/11-checklist-seguranca.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 13 – Arquitetura Avançada

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Repository** completo: find, findById, save, delete com wp_insert_post/get_post | PHP | `pratica/08-repository-completo.php` |
| 2 | **Service** que usa Repository + Validator (um fluxo create/update) | PHP | `pratica/09-service-fluxo-completo.php` |
| 3 | **Pimple** (ou container mínimo): bind, get, singleton | PHP | `pratica/10-container-pimple.php` |
| 4 | Tabela **quando usar** cada padrão (Repository, Service, Event, Factory) | MD | Reforçar 05 ou `pratica/11-tabela-padroes.md` |

**Total sugerido:** +3 a +4 itens.

---

### Fase 14 – Implantação e DevOps

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **docker-compose** com serviço de fila (worker) ou cron | YML + MD | `pratica/08-docker-worker-cron.md` |
| 2 | **GitHub Actions**: job de teste (PHPUnit) + job de deploy (exemplo mínimo) | YML | `pratica/09-github-actions-teste-deploy.yml` |
| 3 | **Backup** (wp db export, arquivos) + restore em uma página | MD | Reforçar 06 ou `pratica/10-backup-restore-passos.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 15 – Tópicos Complementares Avançados

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **WooCommerce** (hook em pedido criado, produto) – exemplo mínimo | PHP | `pratica/08-woocommerce-hooks.php` |
| 2 | **ACF** (get_field, the_field, has_block) – uso em tema/plugin | PHP | `pratica/09-acf-campos.php` |
| 3 | **Headless**: rota REST que retorna lista de posts para front externo | PHP | `pratica/10-headless-endpoint-exemplo.php` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 16 – Jobs Assíncronos e Background

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Recurring** (as_schedule_recurring_action) + cancelar série | PHP | `pratica/08-recurring-cancel.php` |
| 2 | **Retry** manual (tentativas, backoff) dentro do callback | PHP | `pratica/09-retry-no-callback.php` |
| 3 | **Webhook** (receber POST, verificar assinatura, idempotência por chave) | PHP | Reforçar 03 ou `pratica/10-webhook-receiver.php` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 17 – Testes em Toda Fase

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | Teste de **REST** (status 200/400/403, body) com WP_UnitTestCase | PHP | Reforçar 03 ou `pratica/08-rest-test-completo.php` |
| 2 | Teste de **Repository** com mock (stub retornando entidade) | PHP | `pratica/09-repository-mock.php` |
| 3 | **Data provider** (PHPUnit) para vários inputs de validação | PHP | `pratica/10-data-provider-validacao.php` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 18 – Caminhos de Aprendizado

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Cronograma semanal** (ex.: 2h/dia, 5 dias) para um path | MD | `pratica/08-cronograma-exemplo.md` |
| 2 | **Lista de exercícios** por path (1–2 exercícios por fase do path) | MD | `pratica/09-exercicios-por-path.md` |

**Total sugerido:** +2 itens. Conteúdo é mais conceitual; poucos arquivos novos.

---

### Fase 19 – Anti-padrões e Segurança

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **Upload** inseguro vs seguro (tipo, tamanho, extensão, wp_handle_upload) | PHP | `pratica/08-upload-errado-vs-correto.php` |
| 2 | **REST** sem permission_callback vs com capability por recurso | PHP | `pratica/09-rest-permission-errado-vs-correto.php` |
| 3 | Resumo **por fase** (Core, REST, Settings, CPT, Arquitetura, DevOps) em uma tabela | MD | `pratica/10-anti-padroes-por-fase.md` |

**Total sugerido:** +2 a +3 itens.

---

### Fase 20 – Boas Práticas Tratamento de Erros

| # | Conteúdo sugerido | Tipo | Onde |
|---|-------------------|------|------|
| 1 | **WP_Error** em REST (retorno, status, dados) + try/catch convertendo para WP_Error | PHP | `pratica/08-wp-error-rest.php` |
| 2 | **finally** (fechar handle, limpar recurso) em bloco try/catch | PHP | `pratica/09-finally-recursos.php` |
| 3 | **Log com contexto** (order_id, user_id) em formato estruturado (JSON) | PHP | Reforçar 05 ou `pratica/10-log-contexto-exemplo.php` |

**Total sugerido:** +2 a +3 itens.

---

## 4. Resumo executivo

- **Sim**, cabe mais conteúdo e exemplos em cada pasta.
- **Estratégia:** ampliar `pratica/` para **10–12 arquivos** por fase, com foco em:
  - exemplos por cenário (GET/POST, list/create/update),
  - errado vs correto (segurança, erros),
  - mini fluxos completos (admin → save → list),
  - tabelas de referência e checklists por tópico.
- **Prioridade sugerida** para implementação:
  1. Fases 01, 02, 05, 12 (base muito usada).
  2. Fases 03, 04, 06, 13 (API e arquitetura).
  3. Demais fases conforme demanda.

---

## 5. Convenções ao adicionar

- Todo `.php` novo: bloco **REFERÊNCIA RÁPIDA** no topo (sintaxe + fonte da fase).
- Todo `.md` novo: link para o documento da fase na raiz do repositório.
- Atualizar o **README.md** da fase: incluir nova linha na tabela “Comece pela prática” para cada arquivo em `pratica/`.
- Atualizar **pratica/README.md** com uma linha sobre o novo arquivo (o que fazer / quando usar).

---

*Documento de planejamento – estudos-wp. Revisar conforme novas fases ou feedback de uso.*
