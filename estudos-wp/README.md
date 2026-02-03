# estudos-wp – Prática guiada para especialista em WordPress

Esta pasta contém **exemplos práticos**, **guias**, **checklists** e **referências rápidas** alinhados aos 20 documentos de fase (001–020) do repositório. O foco é **prática**: copie trechos para plugins ou temas, siga os passos e use a teoria dos 000-WordPress-* quando precisar aprofundar.

---

## Como usar

- **Cada pasta** corresponde a uma fase (ou complemento) e tem um **README.md** com a tabela de arquivos e um **pratica/README.md** com dicas de uso.
- **Arquivos `.php`:** exemplos para copiar em plugin ou tema; no topo há um bloco **Referência rápida** com a sintaxe do tópico.
- **Arquivos `.md`:** guias, tabelas, checklists e referências; use como apoio ao código.
- **REFERENCIA-RAPIDA.md** (quando existir na pasta): uma página com funções, tabelas e atalhos (Ctrl+F).
- **Teoria completa:** os documentos **001-WordPress-Fase-1** a **020-WordPress-Fase-20** ficam na **raiz do repositório** (acima de `estudos-wp/`).

**Ordem sugerida:** siga a numeração das pastas (01 → 02 → … → 20); use **18-Caminhos-Aprendizado** para escolher um path (backend, DevOps, plugin/tema) e **05b**, **06b**, **12b**, **12c** como complementos quando fizer sentido.

---

## Estrutura da pasta estudos-wp

| Pasta | Conteúdo | README |
|-------|----------|--------|
| **[01-Fundamentos-core](01-Fundamentos-core/)** | Hooks, $wpdb, The Loop, bootstrap, template hierarchy, transações, estrutura | [README](01-Fundamentos-core/README.md) |
| **[02-REST-API-Fundamentos](02-REST-API-Fundamentos/)** | Rotas, controller, validação, permissões, Application Password, JWT (endpoint + middleware) | [README](02-REST-API-Fundamentos/README.md) |
| **[03-REST-API-Avancado](03-REST-API-Avancado/)** | Controller completo, schema, paginação, erros, OAuth2, segurança de tokens, JWT vs App Password vs OAuth2 | [README](03-REST-API-Avancado/README.md) |
| **[04-Configuracoes-Admin](04-Configuracoes-Admin/)** | Settings API, campos, tabs, meta boxes, admin notices, AJAX, sanitize | [README](04-Configuracoes-Admin/README.md) |
| **[05-CPT-Taxonomias](05-CPT-Taxonomias/)** | register_post_type, register_taxonomy, meta box, REST/Gutenberg, archive, template single | [README](05-CPT-Taxonomias/README.md) |
| **[05b-Temas-Classicos](05b-Temas-Classicos/)** | Estrutura tema, sidebar/widgets, menus, Customizer, child theme, Underscores, plugin vs tema | [README](05b-Temas-Classicos/README.md) |
| **[06-Shortcodes-Widgets-Gutenberg](06-Shortcodes-Widgets-Gutenberg/)** | Shortcodes, widget clássico, bloco dinâmico (PHP), blocos JS (estrutura, block.json, edit/save, build, checklist) | [README](06-Shortcodes-Widgets-Gutenberg/README.md) |
| **[06b-FSE-Temas-Bloco](06b-FSE-Temas-Bloco/)** | FSE, theme.json, template parts, templates (index/single/archive), block theme mínimo | [README](06b-FSE-Temas-Bloco/README.md) |
| **[07-WP-CLI-Fundamentos](07-WP-CLI-Fundamentos/)** | Comandos, subcomandos, args, when, registro de comando | [README](07-WP-CLI-Fundamentos/README.md) |
| **[08-Performance-Cache-Otimizacao](08-Performance-Cache-Otimizacao/)** | Object cache, transients, invalidação, fragment cache, Redis/Memcached, load balancer/sessões, CDN | [README](08-Performance-Cache-Otimizacao/README.md) |
| **[09-WP-CLI-Ferramentas](09-WP-CLI-Ferramentas/)** | Progress bar, deploy, ferramentas de linha de comando | [README](09-WP-CLI-Ferramentas/README.md) |
| **[10-Testes-Debug-Implantacao](10-Testes-Debug-Implantacao/)** | PHPUnit, bootstrap, unit tests, mock, data providers, hooks, debug, deploy | [README](10-Testes-Debug-Implantacao/README.md) |
| **[11-Multisite-Internacionalizacao](11-Multisite-Internacionalizacao/)** | Multisite, network options, i18n (__(), POT/PO/MO), RTL, switch_to_blog | [README](11-Multisite-Internacionalizacao/README.md) |
| **[12-Seguranca-Boas-Praticas](12-Seguranca-Boas-Praticas/)** | Validação, escape, nonces, capabilities, prepared statements, upload, headers, checklist | [README](12-Seguranca-Boas-Praticas/README.md) |
| **[12b-Acessibilidade](12b-Acessibilidade/)** | Princípios a11y, escape/semântica, ARIA, contraste, blocos, checklist plugin | [README](12b-Acessibilidade/README.md) |
| **[12c-Privacidade](12c-Privacidade/)** | GDPR/LGPD, exportação/exclusão de dados (hooks do core), política, consentimento, checklist | [README](12c-Privacidade/README.md) |
| **[13-Arquitetura-Avancada](13-Arquitetura-Avancada/)** | SOLID, DDD, Repository, Service, DI (Pimple), eventos | [README](13-Arquitetura-Avancada/README.md) |
| **[14-Implantacao-DevOps](14-Implantacao-DevOps/)** | Docker, secrets, health checks, CI/CD, backup/restore, worker/cron, fila externa, read replica, checklist escalabilidade | [README](14-Implantacao-DevOps/README.md) |
| **[15-Topicos-Complementares-Avancados](15-Topicos-Complementares-Avancados/)** | GraphQL, versioning, OpenAPI, headless, code review, WooCommerce (hooks, checkout, gateway, variável, relatórios, checklist), WPGraphQL, contribuição core, publicar plugin .org, PHPCS, comunidade | [README](15-Topicos-Complementares-Avancados/README.md) |
| **[16-Jobs-Assincronos-Background](16-Jobs-Assincronos-Background/)** | Action Scheduler, filas, webhook, retry, DLQ, boas práticas | [README](16-Jobs-Assincronos-Background/README.md) |
| **[17-Testes-Em-Toda-Fase](17-Testes-Em-Toda-Fase/)** | PHPUnit setup, testes de hooks, REST, CPT/settings/shortcode, arquitetura/async, data provider | [README](17-Testes-Em-Toda-Fase/README.md) |
| **[18-Caminhos-Aprendizado](18-Caminhos-Aprendizado/)** | Grafo de dependências, paths (backend, DevOps, plugin/tema), cronograma, exercícios | [README](18-Caminhos-Aprendizado/README.md) |
| **[19-Anti-padroes-Seguranca](19-Anti-padroes-Seguranca/)** | Anti-padrões (XSS, SQLi, capabilities), REST/settings/CPT/upload, checklist code review | [README](19-Anti-padroes-Seguranca/README.md) |
| **[20-Boas-Praticas-Tratamento-Erros](20-Boas-Praticas-Tratamento-Erros/)** | Fail fast, try/catch, Result, handler centralizado, logging, REST, checklist | [README](20-Boas-Praticas-Tratamento-Erros/README.md) |

---

## Documentos da raiz de estudos-wp

| Arquivo | Descrição |
|---------|-----------|
| **[ANALISE-COBERTURA-E-LACUNAS.md](ANALISE-COBERTURA-E-LACUNAS.md)** | Análise de cobertura do roadmap: o que está bem coberto, lacunas (e oportunidades já implementadas), e se o material permite tornar-se especialista em profundidade. |
| **[PLANEJAMENTO-EXPANSAO-CONTEUDO.md](PLANEJAMENTO-EXPANSAO-CONTEUDO.md)** | Planejamento de expansão do conteúdo das pastas de prática (exemplos por cenário, comparações, checklists). |

---

## Resumo por tipo de arquivo

- **pratica/*.php** – Exemplos de código; copie para plugin ou tema; referência rápida no topo.
- **pratica/*.md** – Guias, tabelas, checklists, passos, recursos externos.
- **README.md** (em cada pasta) – Tabela de arquivos da pasta e como começar.
- **pratica/README.md** – Como usar os arquivos da prática (hooks, registro, testes).
- **REFERENCIA-RAPIDA.md** (em várias pastas) – Funções, sintaxe e tabelas em uma página.

---

## Relação com os documentos de fase (raiz do repositório)

| estudos-wp | Documento de fase (raiz) |
|------------|---------------------------|
| 01 … 20 (e 05b, 06b, 12b, 12c) | 000-WordPress-Indice-Topicos.md (índice) |
| 01 | 001-WordPress-Fase-1-Fundamentos-Core.md |
| 02 | 002-WordPress-Fase-2-REST-API-Fundamentos.md |
| 03 | 003-WordPress-Fase-3-REST-API-Avancado.md |
| … | … |
| 20 | 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md |

Os números das pastas em **estudos-wp** seguem as fases 1–20. As pastas **05b**, **06b**, **12b** e **12c** são **complementos** (temas clássicos, FSE, acessibilidade, privacidade) sem documento de fase exclusivo na raiz.

---

*Prática guiada – estudos-wp. Use junto com os 20 documentos 001–020 para trilha completa de especialista em WordPress.*
