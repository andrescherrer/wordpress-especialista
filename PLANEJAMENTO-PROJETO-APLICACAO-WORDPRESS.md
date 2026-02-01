# Planejamento: Projeto/Aplicação WordPress — Uso Integral do Roadmap

**Objetivo:** Desenvolver uma aplicação real que utilize **todos os recursos** das 14 fases + tópicos complementares do projeto "WordPress Especialista", permitindo aprofundamento prático em cada tópico.

**Data:** Janeiro 2026  
**Navegação:** [Índice](000-WordPress-Topicos-Index.md) | [Análise](ANALISE-PROJETO-WORDPRESS-ESPECIALISTA.md)

---

## 1. Visão geral do projeto

### 1.1 Nome e propósito

**Projeto escolhido:** **Blog de Análise Técnica em Fintech**  
Site focado em **segurança de pagamento**, **compliance**, **PCI-DSS** e **integrações com gateways**. O projeto explora CPT para case studies, taxonomias para regulamentações, dados estruturados (meta boxes ou ACF) para transações simuladas e um **plugin próprio para dashboard de métricas**. Especificação completa na seção **[1.4 Projeto escolhido: Blog Fintech](#14-projeto-escolhido-blog-de-análise-técnica-em-fintech)**.

Outras ideias de assunto (Receitas, Eventos, Tutoriais) permanecem em **[1.3 Sugestões de assunto específico](#13-sugestões-de-assunto-específico)** como referência.

**Descrição em uma frase:**  
Plugin + tema filho WordPress que implementam um **blog de análise técnica em fintech** (case studies, regulamentações, compliance, PCI-DSS, gateways), com **dados estruturados de transações simuladas**, **dashboard de métricas** no admin, **REST API completa**, **blocos Gutenberg** customizados, **cache e performance**, **testes automatizados**, **segurança rigorosa**, **arquitetura SOLID/DDD** e **CI/CD com Docker**.

Esse escopo exige naturalmente o uso de **todas as fases** do roadmap.

### 1.2 Por que este escopo

| Necessidade do projeto        | Fases utilizadas                          |
|------------------------------|--------------------------------------------|
| Estrutura de conteúdo        | 1 (Core), 5 (CPT/Taxonomies)               |
| API para frontend/mobile     | 2, 3 (REST API)                            |
| Código organizado e versionado| 3 (estrutura de plugin), 13 (arquitetura)  |
| Configurações e telas admin  | 4 (Settings, Meta Boxes)                   |
| Conteúdo no editor           | 6 (Shortcodes, Widgets, Gutenberg)         |
| Tarefas agendadas e async    | 7 (Cron, AJAX)                             |
| Performance e cache          | 8 (Transients, Object Cache, Redis)        |
| Automação e deploy           | 9 (WP-CLI), 14 (Docker, CI/CD)             |
| Qualidade e regressão        | 10 (PHPUnit, Xdebug)                       |
| Múltiplos idiomas / rede     | 11 (i18n, Multisite)                       |
| Segurança de dados e código  | 12 (sanitização, nonces, capabilities)     |
| Manutenção e extensibilidade | 13 (SOLID, Repository, DI, Events)          |
| Ambiente e entrega           | 14 (Dev, Staging, Prod, backup, DR)        |

---

### 1.3 Sugestões de assunto específico

“Portal de Publicações” é genérico. Abaixo, **quatro temas concretos** que encaixam no mesmo planejamento técnico. O projeto em desenvolvimento é a **Opção D (Blog Fintech)**; as demais servem de referência.

---

#### Opção A — **Portal de Receitas** (recomendado para estudar)

| Item | Nome técnico | Descrição |
|------|----------------|-----------|
| **Plugin** | `receitas-api` ou `ppa-receitas` | Slug: `receitas-api` |
| **CPT principal** | **Receita** (`receita`) | Título, conteúdo (modo de preparo), excerpt, thumbnail (foto do prato), meta: tempo (min), porções, dificuldade (1–3 ou texto). |
| **Taxonomia 1** | **Categoria** (`categoria_receita`) — hierárquica | Ex.: Entrada, Prato Principal, Sobremesa, Lanche, Bebida. |
| **Taxonomia 2** | **Ingrediente** (`ingrediente`) — flat | Ex.: Frango, Cebola, Tomate. Usada como tag para filtrar “receitas com X”. |
| **Taxonomia 3** (opcional) | **Cozinha** (`cozinha`) — flat | Ex.: Brasileira, Italiana, Japonesa. |
| **Meta box** | Destaque, tempo de preparo, porções, dificuldade, ingredientes (lista ou relação). | |
| **REST API** | `GET/POST /receitas`, `GET/POST /receitas/{id}`, filtros por categoria, ingrediente, tempo. | Para app mobile ou site headless. |
| **Shortcode** | `[ultimas_receitas]` ou `[receita_destaque]` | |
| **Widget** | “Receitas em destaque” ou “Receita do dia”. | |
| **Bloco Gutenberg** | “Lista de receitas” (dinâmico), “Receita em destaque”. | |
| **Cron** | “Receita do dia” (trocar destaque diariamente) ou limpeza de cache. | |
| **i18n** | Text domain `receitas-api`; .pot para traduzir labels e mensagens. | |

**Por que funciona bem:** modelo de conteúdo claro (receita = CPT, categorias/ingredientes = taxonomias), meta boxes úteis (tempo, porções), listagens e filtros na API, bloco “lista de receitas” e cache de “receitas em destaque” dão uso real a Fases 5, 6 e 8.

---

#### Opção B — **Portal de Eventos / Agenda**

| Item | Nome técnico | Descrição |
|------|----------------|-----------|
| **Plugin** | `eventos-api` ou `ppa-eventos` | Slug: `eventos-api` |
| **CPT principal** | **Evento** (`evento`) | Título, conteúdo (descrição), excerpt, thumbnail, meta: data_inicio, data_fim, local, endereco, link_inscricao, vagas (opcional). |
| **Taxonomia 1** | **Tipo** (`tipo_evento`) — flat | Ex.: Palestra, Workshop, Meetup, Curso. |
| **Taxonomia 2** | **Público** (`publico_evento`) — flat | Ex.: Iniciante, Desenvolvedor, Designer. |
| **Meta box** | Data/hora, local, link, vagas, “evento em destaque”. | |
| **REST API** | `GET/POST /eventos`, filtros por data, tipo, público. | Para agenda em app ou outro site. |
| **Shortcode** | `[proximos_eventos]` ou `[evento_destaque]` | |
| **Widget** | “Próximos eventos” (lista com data). | |
| **Bloco Gutenberg** | “Lista de eventos”, “Evento em destaque”. | |
| **Cron** | Lembrete de eventos (ex.: email 1 dia antes) ou limpeza de eventos passados. | |
| **i18n** | Text domain `eventos-api`. | |

**Por que funciona bem:** datas e filtros por período exercitam queries e REST; cron para “lembrete” ou “arquivar passados” dá sentido real à Fase 7; bom para portfolio (“sistema de eventos”).

---

#### Opção C — **Portal de Tutoriais / Base de conhecimento**

| Item | Nome técnico | Descrição |
|------|----------------|-----------|
| **Plugin** | `tutoriais-api` ou `ppa-tutoriais` | Slug: `tutoriais-api` |
| **CPT principal** | **Tutorial** (`tutorial`) | Título, conteúdo (passo a passo), excerpt, thumbnail, meta: tempo_leitura (min), nivel (iniciante/intermediario/avancado), ordem (para série). |
| **Taxonomia 1** | **Série** (`serie_tutorial`) — hierárquica | Ex.: “WordPress do zero”, “REST API”. Agrupa tutoriais em sequência. |
| **Taxonomia 2** | **Assunto** (`assunto_tutorial`) — flat | Ex.: PHP, JavaScript, Gutenberg, Segurança. |
| **Meta box** | Tempo de leitura, nível, ordem na série, “tutorial em destaque”. | |
| **REST API** | `GET/POST /tutoriais`, filtros por série, assunto, nível. | Para app de estudo ou documentação headless. |
| **Shortcode** | `[ultimos_tutoriais]` ou `[tutorial_serie slug="wordpress-do-zero"]` | |
| **Widget** | “Tutoriais populares” ou “Continue de onde parou”. | |
| **Bloco Gutenberg** | “Lista de tutoriais”, “Tutorial em destaque”, “Índice da série”. | |
| **Cron** | Estatísticas de acesso (fake ou real) ou limpeza de cache. | |
| **i18n** | Text domain `tutoriais-api`. | |

**Por que funciona bem:** série hierárquica e “ordem” exercitam relacionamentos (Fase 5); “índice da série” dá um bloco dinâmico interessante; alinha com seu próprio estudo (WordPress/PHP).

---

#### Opção D — **Blog de Análise Técnica em Fintech** *(projeto escolhido)*

Site focado em **segurança de pagamento**, **compliance**, **PCI-DSS** e **integrações com gateways**. Aproveita expertise em fintech para conteúdo técnico e dados estruturados (transações simuladas, métricas).

| Item | Nome técnico | Descrição |
|------|----------------|-----------|
| **Plugin principal** | `fintech-analytics` ou `blog-fintech` | Slug: `fintech-analytics`. Registra CPT, taxonomias, REST API, dashboard de métricas. |
| **CPT principal** | **Case Study** / **Estudo de Caso** (`case_study`) | Título, conteúdo (análise técnica), excerpt, thumbnail. Cada case study cobre um cenário real ou simulado (ex.: integração gateway X, auditoria PCI-DSS, fluxo de 3DS). |
| **Taxonomia 1** | **Regulamentação** (`regulamentacao`) — hierárquica | Ex.: PCI-DSS, LGPD, BACEN, PIX, Open Finance. Permite filtrar conteúdo por norma/regulamento. |
| **Taxonomia 2** | **Gateway / Integração** (`gateway`) — flat | Ex.: Stripe, PagSeguro, Adyen, Mercado Pago, gateway interno. Tags para “artigos que citam o gateway X”. |
| **Taxonomia 3** | **Tema** (`tema_fintech`) — flat | Ex.: Segurança de Pagamento, Compliance, Tokenização, Chargeback, Reconciliação. |
| **Dados estruturados (ACF ou meta boxes)** | **Transações simuladas** | Por case study: cenário (ex.: “checkout com 3DS”), volume simulado, valor médio, taxa de sucesso/erro, tempo de resposta. Campos: tipo_transacao, gateway_ref, metricas (repeater ou JSON). Permite dashboard e gráficos com dados consistentes. |
| **Meta box(es)** | Destaque, “em conformidade com” (relação com termos de Regulamentação), dados do cenário (gateway, ambiente sandbox/prod), data da análise. | |
| **Plugin / módulo** | **Dashboard de métricas** | Página no admin (Settings API ou menu próprio) com: métricas agregadas dos case studies (ex.: total de transações simuladas, por gateway, por regulamentação), gráficos (Chart.js ou similar), export CSV/JSON. Pode consumir os meta de “transações simuladas” e opcionalmente REST para dados ao vivo. |
| **REST API** | `GET/POST /case-studies`, `GET/POST /case-studies/{id}`, filtros por regulamentacao, gateway, tema. Endpoint opcional: `GET /metrics` (agregados para dashboard externo ou headless). | Para app, relatórios ou integração com ferramentas de BI. |
| **Shortcode** | `[ultimos_case_studies]`, `[case_study_por_regulamentacao slug="pci-dss"]` | |
| **Widget** | “Case studies em destaque” ou “Últimas análises”. | |
| **Bloco Gutenberg** | “Lista de case studies”, “Case study em destaque”, “Filtro por regulamentação”. | |
| **Cron** | Atualização de cache de métricas agregadas; limpeza de transientes; opcional: “resumo semanal” (email com novos case studies). | |
| **i18n** | Text domain `fintech-analytics`; .pot para traduzir labels, mensagens e termos técnicos (compliance, PCI-DSS, etc.). | |
| **Segurança (ênfase)** | Sanitização/escape em todos os inputs (dados de transação simulados nunca reais); capabilities restritas para dashboard; nonces em forms e REST. Conteúdo sensível apenas para roles autorizados. | Alinhado à Fase 12 e ao domínio fintech. |

**Por que funciona bem:** alinha com expertise em fintech (segurança, compliance, gateways); case studies + regulamentações + gateways dão modelo de conteúdo rico; dados estruturados de transações simuladas alimentam o dashboard e exercitam meta boxes/repeater (ou ACF); plugin de dashboard de métricas usa Settings API, gráficos e opcionalmente REST; segurança e compliance são parte do próprio tema do projeto.

**Referência no resto do documento:** ao seguir o mapeamento das Fases 2–14, substitua “Publicação” por **Case Study** / **Estudo de Caso**, “ppa” por **fintech-analytics**, e as taxonomias genéricas por **Regulamentação**, **Gateway**, **Tema**.

---

#### Como escolher (referência)

- **Receitas (A):** mais visual (foto, listagens), conteúdo fácil de criar para testes, API útil para app de receitas.
- **Eventos (B):** foco em datas, filtros e cron (lembretes/arquivamento); bom se quiser destacar “sistemas”.
- **Tutoriais (C):** combina com seu material de estudo; séries e níveis dão um modelo rico sem ser complexo.
- **Fintech (D) — escolhido:** segurança de pagamento, compliance, PCI-DSS, gateways; case studies, regulamentações, dados estruturados (transações simuladas) e dashboard de métricas.

Substituições para o **projeto Fintech (D)** no resto do planejamento:
- “Publicação” → **Case Study** / **Estudo de Caso**
- “ppa” ou “PPA” → **fintech-analytics**
- Taxonomias → **Regulamentação** (`regulamentacao`), **Gateway** (`gateway`), **Tema** (`tema_fintech`).

---

### 1.4 Projeto escolhido: Blog de Análise Técnica em Fintech

Resumo do que será construído (especificação completa na **Opção D** da seção 1.3 acima):

| Componente | Implementação |
|------------|----------------|
| **Conteúdo** | CPT **Case Study** (estudos de caso técnicos); taxonomias **Regulamentação** (PCI-DSS, LGPD, BACEN, PIX…), **Gateway** (Stripe, PagSeguro…), **Tema** (segurança, compliance, tokenização…). |
| **Dados estruturados** | Meta boxes ou **ACF** para dados de **transações simuladas** por case study (cenário, volume, valor médio, taxa de sucesso, tempo de resposta). |
| **Admin** | **Dashboard de métricas** (plugin próprio): página no admin com métricas agregadas, gráficos (Chart.js ou similar), export CSV/JSON; opcionalmente endpoint REST `GET /metrics`. |
| **Frontend e API** | Shortcodes, widget, blocos Gutenberg (“lista de case studies”, “em destaque”, “por regulamentação”); REST API completa para case studies e filtros. |
| **Segurança** | Ênfase em sanitização, escape, nonces e capabilities; dados de transação apenas simulados; conteúdo sensível com roles restritos. |

Ao seguir o mapeamento das Fases 2–14, use os nomes técnicos da Opção D (Case Study, fintech-analytics, Regulamentação, Gateway, Tema) em todo o documento.

---

## 2. Mapeamento: Fase → Entregável no projeto

Cada fase vira um conjunto de **entregáveis** e **tópicos para aprofundamento**. Use a coluna **Onde estudar** para abrir o arquivo e a seção correta do roadmap.

---

### FASE 1 — Fundamentos do WordPress Core

| Entregável no projeto | O que fazer | Onde estudar (arquivo + tópico) |
|------------------------|-------------|----------------------------------|
| Estrutura de diretórios do plugin/tema | Respeitar `wp-content/plugins`, `wp-content/themes`, constantes `ABSPATH`, `WP_CONTENT_DIR` | `001` — 1.1 Arquitetura |
| Hooks em todo o plugin | Usar apenas `add_action`/`add_filter`, prioridades, remoção de hooks quando necessário | `001` — 1.2 Hook System |
| Uso do banco | Consultas via `$wpdb` apenas onde necessário; resto via WP_Query/APIs; prepared statements | `001` — 1.3 Estrutura DB, 1.4 $wpdb |
| Template hierarchy | Tema filho com single, archive, taxonomy, 404 para seus CPTs | `001` — 1.6 Template Hierarchy |
| The Loop | Loops em arquivos de template com `have_posts()`, `the_post()`, reset | `001` — 1.7 The Loop |
| Coding standards | PHPDoc, naming, formatação em todo o código | `001` — 1.8 Coding Standards |

**Aprofundamento:** Ler `001-WordPress-Fase-1-Fundamentals of WordPress Core.md` na íntegra e marcar no índice os itens que você já aplicou no projeto.

---

### FASE 2 — REST API Fundamentals

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Endpoints REST para conteúdo | GET coleções e item de “Publicação” (CPT); verbos HTTP e status codes corretos | `002` — 2.1 Conceitos, 2.2 Rotas |
| Validação e sanitização | `validate_callback` e `sanitize_callback` em todos os argumentos de rota | `002` — 2.4 Validação e Sanitização |
| Autenticação e permissões | Application Passwords ou JWT; `permission_callback` com `current_user_can()` | `002` — 2.5 REST Auth, 2.6 Permissions |
| Respostas e erros | `WP_REST_Response`, `WP_Error`, status HTTP e mensagens claras | `002` — 2.7 Response e Error Handling |
| Schema e documentação | Schema dos recursos; descrição de parâmetros (OpenAPI/Swagger se possível) | `002` — 2.8 Documentação e Schema |

**Aprofundamento:** `002-WordPress-Fase-2-WordPress REST API Fundamentals.md` — focar em 2.2–2.8 e reproduzir padrões no seu plugin.

---

### FASE 3 — REST API Advanced

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Controllers OOP | Um controller por recurso (ex.: `Publicacao_REST_Controller`) estendendo `WP_REST_Controller` | `003` — REST Controllers OOP |
| CRUD completo | `get_items`, `get_item`, `create_item`, `update_item`, `delete_item` com consistência de resposta | `003` — Padrão CRUD |
| Estrutura de plugin | Namespaces, PSR-4, Composer autoload, pastas `src/`, `assets/`, `templates/` | `003` — 3.2 Estrutura de Diretórios |
| Activation/Deactivation | Criar opções/tabelas na ativação; limpar ou marcar versão na desativação | `003` — 3.5 Activation, 3.6 Uninstall |
| Versioning e migrations | Versão no header do plugin; migrations (ex.: dbDelta) ao atualizar | `003` — 3.7 Plugin Versioning |

**Aprofundamento:** `003-WordPress-Fase-3-REST-API-Advanced.md` — implementar um recurso completo (ex.: “Série” ou “Autor”) seguindo o mesmo padrão.

---

### FASE 4 — Settings API e Admin

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Página de configurações | Settings API: `register_setting`, `add_settings_section`, `add_settings_field` | `004` — 4.1, 4.2 |
| Menu e submenus | `add_menu_page` e `add_submenu_page` para “Publicações” e “Configurações” | `004` — 4.2 Criar Páginas |
| Admin CSS/JS | `admin_enqueue_scripts`, `wp_localize_script` para dados e i18n | `004` — 4.3 Admin Styling |
| Meta boxes | Meta box para “Publicação” (ex.: destaque, data de capa, autor) com nonce e save | `004` — 4.5 Meta Boxes |
| Admin notices | `add_settings_error` e `settings_errors()` após salvar configurações | `004` — 4.6 Admin Notices |
| Campos avançados | Pelo menos um: media uploader, color picker ou repeater (conforme 4.7) | `004` — 4.7 Admin Forms Avançado |

**Aprofundamento:** `004-WordPress-Fase-4-Settings-Admin.md` — montar uma tela de opções completa e reutilizar padrões em outras telas.

---

### FASE 5 — Custom Post Types e Taxonomies

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| CPT “Publicação” | `register_post_type` com supports, rewrite, capabilities, `show_in_rest` | `005` — 5.1 CPT |
| Taxonomias | Ex.: “Série”, “Tema” (hierárquica e flat); `show_in_rest` | `005` — 5.2 Taxonomies |
| Supports | Thumbnail, excerpt, revisions, custom fields conforme necessidade | `005` — 5.3 Post Type Supports |
| Relacionamentos | Autor como user meta ou taxonomy; relação Série → Publicações | `005` — 5.4 Relacionamentos |
| Archives e singles | Templates de archive e single para o CPT (usa Fase 1) | `005` — 5.5, 5.6 |
| REST | Garantir `rest_base` e capabilities de leitura/edição na API | `005` — 5.8 Exposição REST |

**Aprofundamento:** `005-WordPress-Fase-5-Custom-Post-Types-Taxonomies.md` — desenhar o modelo de conteúdo no papel e depois implementar.

---

### FASE 6 — Shortcodes, Widgets e Gutenberg

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Shortcode “últimas publicações” | `add_shortcode`, atributos, escaping, query e template | `006` — 6.1, 6.2 |
| Widget “destaques” | Classe estendendo `WP_Widget`, `form`, `widget`, `update`; registrar sidebar | `006` — 6.3, 6.4 |
| Bloco Gutenberg “lista de publicações” | Bloco dinâmico (render callback em PHP) com `block.json` e atributos | `006` — 6.5–6.8 |
| (Opcional) Block pattern | Um pattern que combine blocos nativos + seu bloco customizado | `006` — 6.9 Block Patterns |

**Aprofundamento:** `006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md` — implementar o bloco dinâmico como foco principal (React opcional depois).

---

### FASE 7 — Cron e processamento em background

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Cron para limpeza/agenda | Ex.: limpar transients antigos ou enviar “resumo semanal” (fake ou real) | `000` Índice — Fase 7 (7.1 WP-Cron, 7.2 Cron Hooks) |
| Agendamento e gestão | `wp_schedule_event`, `wp_next_scheduled`, listar eventos (pode ser na admin) | `000` Índice — Fase 7 (7.3 Gerenciar Cron) |
| (Opcional) Cron real | Documentar uso de `DISABLE_WP_CRON` + crontab para produção | `000` Índice — Fase 7 (7.4, 7.5) |
| AJAX no admin | Uma ação AJAX (ex.: preview ou busca) com nonce e `wp_send_json_*` | `000` Índice — Fase 7 (7.6 AJAX API) |

**Aprofundamento:** No **índice** (`000-WordPress-Topicos-Index.md`) a Fase 7 é “Cron Jobs e Background Processing”. Os arquivos `007` e `009` são WP-CLI; use-os na **Fase 9** deste planejamento. Para Cron, siga os tópicos listados no índice da Fase 7 e busque exemplos nos arquivos de fases que tratem de Cron (ex.: plugins que agendam tarefas).

---

### FASE 8 — Performance e Caching

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Transients para listagens | Cache de “últimas publicações” ou “destaques” com TTL; invalidação ao publicar | `008` — 8.1 Transients, 8.4 Invalidation |
| Object Cache (se Redis disponível) | `wp_cache_*` para queries pesadas ou contagens; grupos nomeados | `008` — 8.2, 8.3 Object Cache |
| Otimização de queries | Evitar N+1; usar `update_post_caches`/`update_postmeta_cache` onde fizer sentido | `008` — 8.5, 8.6 |
| Assets | Ordem de scripts/styles, minificação em build, async/defer onde aplicável | `008` — 8.8 Asset Optimization |
| (Opcional) Profiling | Query Monitor / Xdebug em dev; documentar como medir antes/depois | `008` — 8.10 Profiling |

**Aprofundamento:** `008-WordPress-Fase-8-Performance-Cache-Otimizacao.md` — escolher uma lista que seja crítica e implementar cache + invalidação completa.

---

### FASE 9 — WP-CLI e ferramentas

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Comando customizado | Ex.: `wp ppa seed` (criar publicações de exemplo) ou `wp ppa cache flush` | `009` — 9.7 Custom WP-CLI Commands |
| Uso em deploy | Script que rode `wp core install`, `wp plugin activate`, `wp option set` em staging/prod | `009` — 9.8 WP-CLI para Deploy |
| Aliases | `wp-cli.yml` ou aliases para dev/staging/prod (documentar) | `009` — 9.6 WP-CLI Config |

**Aprofundamento:** `009-WordPress-Fase-9-WP-CLI-Ferramentas.md` e `007-WordPress-Fase-7-WP-CLI-Fundamentals.md` — implementar pelo menos um comando que você use no dia a dia.

---

### FASE 10 — Testing e Debugging

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| PHPUnit + WP | Configurar `WP_UnitTestCase`, bootstrap do plugin, database de testes | `010` — 10.1, 10.2 |
| Testes de unidade | Testar repositórios, serviços e helpers (sanitização, validação) | `010` — 10.3 Testing Plugins |
| Testes de REST API | Requests mock para GET/POST de um recurso; checar status e schema | `010` — 10.4 Testing REST API |
| Factories | Usar ou estender `WP_UnitTest_Factory` para publicações e termos | `010` — 10.5 Test Data Factories |
| (Opcional) Coverage e Xdebug | Gerar coverage; configurar Xdebug para um fluxo crítico | `010` — 10.7, 10.9, 10.10 |

**Aprofundamento:** `010-WordPress-Fase-10-Testing-Debugging-Deploy.md` — ter pelo menos um teste por “camada” (unidade, REST).

---

### FASE 11 — Multisite e Internacionalização

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| i18n em todo o plugin | Text domain único; `__()`, `_e()`, `esc_html__()`, `_n()` onde houver strings | `011` — 11.5 i18n, 11.6 l10n |
| Carregar texto | `load_plugin_textdomain()` no hook apropriado; locale e paths corretos | `011` — 11.6 load_plugin_textdomain |
| .pot e traduções | Gerar .pot; criar pelo menos um .po/.mo (ex.: pt_BR) | `011` — 11.8 Translation Workflows |
| (Opcional) Multisite | Se fizer sentido, ativar na rede e testar opções por site vs rede | `011` — 11.1–11.4 |
| (Opcional) RTL | `is_rtl()` em estilos ou um bloco | `011` — 11.9 RTL |

**Aprofundamento:** `011-WordPress-Fase-11-Multisite-Internacionalizacao.md` — deixar o plugin “translation-ready” desde o início.

---

### FASE 12 — Segurança e boas práticas

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Input: sanitização e validação | Todo input de formulário e REST com sanitize + validate; nunca confiar em input | `012` — 12.1, 12.2 |
| Output: escaping | `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` em todos os outputs | `012` — 12.3 |
| SQL | Apenas `$wpdb->prepare()` para queries com dados dinâmicos | `012` — 12.4 SQL Injection |
| CSRF | Nonces em forms e AJAX; `permission_callback` na REST | `012` — 12.6 CSRF |
| Capabilities | Checagens `current_user_can()` em admin e REST; capabilities mínimas | `012` — 12.7 Capability Checking |
| Upload | Validação de tipo e tamanho; `wp_handle_upload()`; whitelist de extensões | `012` — 12.8 File Upload |

**Aprofundamento:** `012-WordPress-Fase-12-Seguranca-Boas-Praticas.md` — fazer uma passada de “security review” em todo o código antes de considerar estável.

---

### FASE 13 — Arquitetura avançada

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| SOLID | Separar responsabilidades (ex.: Validator, Repository, Service); injeção de dependências | `013` — 13.1 SOLID |
| Repository | Uma camada de repositório para “Publicação” (e opcionalmente Série/Autor) | `013` — 13.4 Repository |
| Service Layer | Serviços que orquestram (ex.: PublicacaoService para criar/publicar/invalidar cache) | `013` — 13.3 Service Layer |
| (Opcional) DDD | Entidades e value objects onde fizer sentido (ex.: slug, status) | `013` — 13.2 DDD |
| (Opcional) DI Container | Container simples para registrar Repository e Services e resolver no bootstrap | `013` — 13.5 DI Container |
| (Opcional) Event-Driven | Evento “publicação criada” e listener que invalida cache ou envia notificação | `013` — 13.6 Event-Driven |
| Padrões pontuais | Adapter para um serviço externo (ex.: analytics); Strategy para “formato de export” | `013` — 13.8 Adapter, 13.9 Strategy |

**Aprofundamento:** `013-WordPress-Fase-13-Arquitetura-Avancada.md` — começar por SRP e Repository; depois Service Layer e um evento.

---

### FASE 14 — Deployment e DevOps

| Entregável no projeto | O que fazer | Onde estudar |
|------------------------|-------------|---------------|
| Docker dev | `docker-compose` com PHP, Nginx, MySQL, Redis, WP-CLI, Mailhog (conforme 14.1) | `014` — 14.1 Development |
| Staging | Compose ou script de sync DB/assets para ambiente de homologação | `014` — 14.2 Staging |
| Produção | Documentar setup (PHP, Nginx, MySQL, Redis); `wp-config` de produção | `014` — 14.3 Production |
| Git | `.gitignore`, Conventional Commits, branch strategy (ex.: main + develop) | `014` — 14.4 Version Control |
| CI/CD | Pipeline (GitHub Actions ou GitLab CI): lint, PHPUnit, deploy para staging | `014` — 14.5 CI/CD, 14.6 Testes no pipeline |
| Deploy e rollback | Script de deploy (WP-CLI, rsync, etc.) e procedimento de rollback | `014` — 14.7 Automated Deployment |
| Monitoramento | Logs, Sentry (ou similar), uptime; alertas básicos | `014` — 14.8 Monitoring |
| Backup e DR | Backup de DB e arquivos; documento de RTO/RPO e restore | `014` — 14.9 Backup, 14.10 Disaster Recovery |

**Aprofundamento:** `014-WordPress-Fase-14-Deployment-DevOps.md` — montar primeiro o ambiente dev com Docker; em seguida um pipeline mínimo (test + deploy staging).

---

### Tópicos complementares (015)

| Tópico | Como encaixar no projeto |
|--------|---------------------------|
| API versioning / throttling | Versão na URL da API (ex.: `/wp-json/ppa/v1/`); rate limit em produção |
| Headless | Usar a REST do plugin como backend para um frontend React/Next.js (projeto paralelo ou fase 2) |
| WooCommerce / ACF / outros | Opcional: integração com WooCommerce (vendas de conteúdo) ou ACF para campos extras |
| Core Web Vitals / Lighthouse | Medir e otimizar uma página “listagem” e uma “single” do tema |

Use `015-WordPress-Topicos-Complementares-Avancados.md` para escolher 1–2 itens por ciclo de evolução.

---

## 3. Ordem sugerida de implementação

Fluxo que reduz retrabalho e permite entregas incrementais:

1. **Fundação (Fases 1, 3, 12)**  
   Estrutura do plugin (namespaces, Composer, hooks), coding standards e segurança básica (sanitize/escape/nonce) desde o início.

2. **Modelo de conteúdo (Fase 5)**  
   CPT “Publicação” e taxonomias; relacionamentos e suporte REST.

3. **Admin (Fase 4)**  
   Páginas de configuração e meta boxes; depois campos avançados se necessário.

4. **REST API (Fases 2, 3)**  
   Controllers OOP, CRUD, validação, permissões e schema.

5. **Frontend e editor (Fases 1, 6)**  
   Template hierarchy no tema filho; shortcode, widget e bloco Gutenberg dinâmico.

6. **Performance (Fase 8)**  
   Transients e object cache; invalidação; otimização de queries e assets.

7. **Cron e WP-CLI (Fases 7, 9)**  
   Tarefas agendadas e comando WP-CLI customizado.

8. **Testes (Fase 10)**  
   PHPUnit, testes de REST e factories.

9. **i18n (Fase 11)**  
   Text domain, .pot e pelo menos uma tradução.

10. **Arquitetura (Fase 13)**  
    Refatorar para Repository, Service Layer e, se desejar, eventos e DI.

11. **DevOps (Fase 14)**  
    Docker, CI/CD, staging, produção, backup e DR.

---

## 4. Checklist de aprofundamento por fase

Use este checklist para marcar o que já estudou e aplicou. Pode colar no final do `000-WordPress-Topicos-Index.md` ou manter no seu próprio doc.

- [ ] **Fase 1** — Li as seções 1.1–1.8 e usei hooks, DB, template hierarchy e coding standards no projeto.
- [ ] **Fase 2** — Li 2.1–2.8 e implementei rotas, validação, auth e schema na REST API.
- [ ] **Fase 3** — Li estrutura de plugin e REST avançado; tenho controllers OOP e activation/versioning.
- [ ] **Fase 4** — Li Settings API e admin; tenho pelo menos uma página de opções e meta boxes.
- [ ] **Fase 5** — Li CPT e taxonomias; tenho modelo de conteúdo com REST e templates.
- [ ] **Fase 6** — Li shortcodes, widgets e Gutenberg; tenho shortcode, widget e bloco dinâmico.
- [ ] **Fase 7** — Li Cron e AJAX; tenho pelo menos um cron e uma ação AJAX.
- [ ] **Fase 8** — Li cache e performance; tenho transients/object cache e invalidação.
- [ ] **Fase 9** — Li WP-CLI; tenho comando customizado e uso em deploy.
- [ ] **Fase 10** — Li testing; tenho PHPUnit e testes de REST.
- [ ] **Fase 11** — Li i18n/l10n; plugin translation-ready e .pot.
- [ ] **Fase 12** — Li segurança; revisei sanitização, escaping, nonces e capabilities.
- [ ] **Fase 13** — Li arquitetura; tenho Repository, Service Layer e pelo menos um padrão (ex.: Event).
- [ ] **Fase 14** — Li DevOps; tenho Docker dev, pipeline e documentação de deploy/backup/DR.

---

## 5. Próximos passos práticos

1. **Clonar/criar repositório**  
   Um repo para o plugin (ex.: `ppa-api`) e, se quiser, outro para o tema filho ou monorepo com ambos.

2. **Ler os arquivos de fase na ordem acima**  
   Começar por `001`, `003` e `012`; depois `005`, `004`, `002`/`003`, etc.

3. **Criar issues ou tarefas**  
   Uma issue por “Entregável” desta tabela (ou por subseção do índice) para rastrear o que já foi feito.

4. **Definir um “MVP”**  
   Ex.: Plugin com CPT Publicação + 1 taxonomy, 1 página de opções, REST GET/POST, 1 shortcode, 1 bloco dinâmico, cache com transient e invalidação. Depois acrescentar testes, WP-CLI, Docker e CI/CD.

5. **Revisar este planejamento**  
   Ajustar nomes (ex.: “Publicação” → “Artigo” ou “Curso”) e escopo conforme seu foco (só API, só admin, etc.).

---

**Resumo:** Este documento mapeia **cada fase** do roadmap para **entregáveis concretos** do projeto “Portal de Publicações & API” e indica **em qual arquivo e tópico** você pode se aprofundar. Seguindo a ordem sugerida e o checklist, você utiliza todos os recursos do projeto WordPress Especialista em uma única aplicação e aprofunda tópico a tópico de forma orientada à prática.
