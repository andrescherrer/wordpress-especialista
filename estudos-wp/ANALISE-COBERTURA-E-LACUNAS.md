# Análise: Cobertura do roadmap e lacunas

**Objetivo:** avaliar se, com os arquivos 000-WordPress-* (fases 001–020) e o conteúdo da pasta `estudos-wp`, alguém que se dedicar pode se tornar **especialista em WordPress em profundidade** e **quais lacunas existem**.

---

## 1. Conclusão direta

**Sim, é possível tornar-se especialista em profundidade**, desde que:

- A pessoa **estude os 20 documentos de fase** (001–020) como base teórica.
- Use a pasta **estudos-wp** como **prática guiada** (exemplos, referência rápida, checklist).
- **Pratique em projetos reais** (plugin, tema, API, deploy) e não só leia/repita exemplos.
- **Complemente** as áreas em que o material é mais enxuto (ver lacunas abaixo).

O roadmap cobre **desde fundamentos (hooks, $wpdb, loop) até tópicos de especialista (REST avançado, arquitetura, jobs assíncronos, segurança, tratamento de erros)**. O índice (000) e o checklist de masterização deixam claro o nível alvo: **especialista em PHP/WordPress**.

---

## 2. O que está bem coberto

| Área | Onde | Comentário |
|------|------|------------|
| **Core (hooks, $wpdb, loop, bootstrap)** | Fase 1, estudos-wp 01 | Hooks, prepare, transações, estrutura, template hierarchy. |
| **REST API (fundamentos e avançado)** | Fases 2–3, estudos-wp 02–03 | Rotas, controllers, validação, schema, paginação, erros, auth. |
| **Settings API e admin** | Fase 4, estudos-wp 04 | Páginas, campos, tabs, AJAX, sanitize. |
| **CPT e taxonomias** | Fase 5, estudos-wp 05 | register_post_type/taxonomy, meta box, REST/Gutenberg, archive. |
| **Shortcodes, widgets, blocos (PHP)** | Fase 6, estudos-wp 06 | Shortcode, widget clássico, bloco dinâmico (render_callback). |
| **WP-CLI** | Fases 7 e 9, estudos-wp 07 e 09 | Comandos, subcomandos, args, when, progress bar, deploy. |
| **Performance e cache** | Fase 8, estudos-wp 08 | Object cache, transients, enqueue condicional, checklist. |
| **Testes (PHPUnit, hooks, REST)** | Fases 10 e 17, estudos-wp 10 e 17 | Config, unit, mock, hooks, REST, data provider. |
| **Multisite e i18n** | Fase 11, estudos-wp 11 | switch_to_blog, options, __(), POT/PO/MO. |
| **Segurança** | Fases 12 e 19, estudos-wp 12 e 19 | Validação, escape, nonces, capabilities, prepared, upload, anti-padrões, checklist. |
| **Arquitetura** | Fase 13, estudos-wp 13 | SOLID, DDD, Repository, Service, DI (Pimple), eventos. |
| **DevOps e deploy** | Fase 14, estudos-wp 14 | Docker, secrets, CI/CD, backup/restore, worker/cron. |
| **Jobs assíncronos** | Fase 16, estudos-wp 16 | Action Scheduler, filas, webhook, retry, DLQ. |
| **Tratamento de erros** | Fase 20, estudos-wp 20 | Fail fast, Result, handler centralizado, logging, REST. |
| **Caminhos de aprendizado** | Fase 18, estudos-wp 18 | Grafo, paths por perfil, cronograma, exercícios. |

Ou seja: **a maior parte do ecossistema WordPress (backend, API, admin, CPT, segurança, arquitetura, DevOps, testes, erros) está coberta** em teoria (fases) e prática (estudos-wp).

---

## 3. Lacunas e pontos a complementar

### 3.1 Blocos Gutenberg em JavaScript/React

- **No índice:** Fase 6 – “JavaScript/React para blocos”, “block.json”, “Atributos”, “Salvar dados”.
- **Na prática (estudos-wp):** há bloco **dinâmico em PHP** (render_callback); **não há** tutorial passo a passo de bloco com **edit.js** (React), **save.js**, **block.json** e build (npm/webpack).
- **Lacuna:** quem quiser **criar blocos ricos no editor** (UI em React, preview fiel) precisa complementar com documentação oficial (Create Block, Block Editor Handbook) ou curso focado em Gutenberg JS.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Estrutura de um bloco JS** | Novo arquivo em estudos-wp/06 (ex.: `pratica/12-bloco-js-estrutura.md`) | Documento que descreve pastas e arquivos: `src/`, `block.json`, `edit.js`, `save.js`, `style.css`, `editor.css`; papel de cada um e ordem de carregamento. |
| **block.json completo** | Exemplo em .md ou snippet | Campos essenciais: `apiVersion`, `name`, `title`, `category`, `icon`, `attributes`, `supports`; `editorScript`, `editorStyle`, `style`; exemplo mínimo copiável. |
| **edit.js mínimo (React)** | Exemplo em .js ou .md | Uso de `wp.element`, `wp.blocks.useBlockProps`, `wp.components` (TextControl, RichText); leitura e atualização de `attributes`; sem build externo (script direto) ou com `@wordpress/scripts`. |
| **save.js e serialização** | Exemplo em .js ou .md | Retorno de elemento React em `save()`; atributos salvos no HTML (data-*); bloco estático vs dinâmico (e quando usar PHP render_callback). |
| **Build (npm / @wordpress/scripts)** | Guia em .md | Comandos `npm run build` e `npm run start`; `webpack.config.js` padrão; saída em `build/`; enqueue no PHP. |
| **Checklist “Meu primeiro bloco JS”** | Novo item em 06 (ex.: `13-checklist-bloco-js.md`) | Passos: criar plugin, npm init, instalar @wordpress/scripts, criar block.json e edit/save, registrar no PHP, testar no editor. |
| **Recursos externos** | Referência no README ou REFERENCIA-RAPIDA | Link para [Create Block](https://developer.wordpress.org/block-editor/getting-started/create-block/), [Block Editor Handbook](https://developer.wordpress.org/block-editor/); indicação de tutorial em vídeo se houver. |

---

### 3.2 Full Site Editing (FSE) e temas de bloco

- **No índice:** Fase 6 trata de blocos; **não há** seção dedicada a **FSE**, **theme.json**, **template parts**, **block theme**.
- **Lacuna:** desenvolvimento de **tema de bloco** (sem PHP de template clássico) e **theme.json** (estilos, paleta, tipografia) não estão cobertos em profundidade. Quem quiser atuar como “especialista em temas FSE” deve buscar material específico.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **O que é FSE e block theme** | Novo doc em estudos-wp (ex.: pasta `06b-FSE-Temas-Bloco` ou seção em 06) | Diferença entre tema clássico e tema de bloco; papel do editor de site (Site Editor); quando usar FSE. |
| **theme.json – estrutura** | Exemplo em .md | Campos principais: `version`, `settings` (typography, color, spacing), `styles`, `templateParts`; paleta de cores e fontes; exemplo mínimo por seção. |
| **theme.json – estilos globais** | Exemplo em .json ou .md | Definição de `color.palette`, `typography.fontSizes`; uso em blocos; variáveis CSS geradas. |
| **Template parts** | Guia em .md | O que é template part; pasta `parts/`; arquivo HTML (ou bloco); registro e uso no Site Editor. |
| **Templates (index, single, archive)** | Guia em .md | Pasta `templates/`; `index.html` como fallback; `single.html`, `archive.html`; relação com template hierarchy. |
| **Criar um block theme mínimo** | Passo a passo em .md | Estrutura de pastas; theme.json mínimo; templates e parts mínimos; ativação e preview no Site Editor. |
| **Recursos externos** | Referência no doc | [Block Editor Handbook – Full Site Editing](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/), [theme.json](https://developer.wordpress.org/block-editor/reference-guides/theme-json/). |

---

### 3.3 Autenticação REST (JWT / OAuth2)

- **No índice:** Fase 2 – “JWT tokens customizados”, “OAuth2”; Fase 3 – “Autenticação JWT”.
- **Na prática (estudos-wp):** Application Password e nonce estão cobertos; **JWT e OAuth2** aparecem mais na teoria dos 002/003 do que em exemplos práticos completos (implementação de emissão/validação de JWT, fluxo OAuth2).
- **Lacuna:** implementar **REST com JWT ou OAuth2** do zero exigirá complemento (plugin existente ou documentação de implementação).

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Endpoint de login que emite JWT** | Novo .php em estudos-wp/02 ou 03 (ex.: `10-auth-jwt-endpoint.php`) | Rota POST que recebe user/pass; valida com `wp_authenticate`; gera JWT (biblioteca firebase/php-jwt ou similar); retorna `{ "token": "..." }`; uso de secret (wp_salt ou constante). |
| **Middleware / permission_callback que valida JWT** | Exemplo em .php | Extrair header `Authorization: Bearer <token>`; decodificar e validar assinatura e expiração; obter user_id do payload; `wp_set_current_user`; retornar true/false no permission_callback. |
| **Fluxo OAuth2 (autorização)** | Guia em .md | Passos: redirect para autorização; usuário aprova; callback com code; troca de code por token; uso do token em REST; referência a RFC ou plugin (ex.: OAuth2 Server). |
| **Segurança (refresh token, revogação)** | Seção em .md | Onde armazenar tokens; tempo de vida; refresh token; revogação (blacklist ou delete user_meta). |
| **Tabela JWT vs Application Password vs OAuth2** | Item em REFERENCIA-RAPIDA ou .md | Quando usar cada um; prós e contras; cenário headless vs admin. |
| **Recursos externos** | Referência | Plugin JWT Authentication for WP REST API; [OAuth2 Server](https://oauth2.thephpleague.com/); documentação de implementação JWT em PHP. |

---

### 3.4 WooCommerce / e-commerce em profundidade

- **No índice:** Fase 15 – “Integração WooCommerce (padrões avançados)”.
- **Na prática (estudos-wp):** há **hooks** (pedido criado, checkout); **não há** fluxo completo de loja (checkout, gateways, produtos variáveis, relatórios, extensões).
- **Lacuna:** o material prepara para **integrar com WooCommerce** (hooks, boas práticas); **não** substitui um curso de desenvolvimento WooCommerce (customização de checkout, gateways, etc.).

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Tabela de hooks WooCommerce** | Novo .md em estudos-wp/15 (ex.: `11-woocommerce-hooks-tabela.md`) | Hooks por fase: carrinho (`woocommerce_add_to_cart`), checkout (`woocommerce_checkout_order_processed`), pedido (`woocommerce_order_status_*`), produto (`woocommerce_product_*`); parâmetros e uso típico. |
| **Customizar checkout (campos, validação)** | Exemplo em .php | `woocommerce_checkout_fields`; adicionar/remover campos; `woocommerce_checkout_process` para validação; salvar em order meta. |
| **Gateway de pagamento (esqueleto)** | Exemplo em .php | Classe que estende `WC_Payment_Gateway`; `process_payment`; retorno (success/failure); redirecionamento; link para documentação oficial de gateways. |
| **Produto variável e dados custom** | Exemplo em .php | Hooks para variações; salvar meta em produto/varição; exibir no carrinho e no pedido. |
| **Relatórios e REST WooCommerce** | Guia em .md | Endpoints REST do WooCommerce (orders, products); relatório simples (total de pedidos, por status); uso em dashboard custom. |
| **Checklist “Integrar com WooCommerce”** | Item em 15 (ex.: `12-checklist-woocommerce.md`) | Verificar versão mínima; não modificar templates sem child theme/override; usar hooks e filtros; testar com dados reais; compatibilidade com blocos. |
| **Recursos externos** | Referência | [WooCommerce Developer Documentation](https://woocommerce.com/documentation/plugins/woocommerce/), [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/). |

---

### 3.5 Acessibilidade (a11y)

- **No índice:** não há seção dedicada a **acessibilidade** (ARIA, contraste, teclado, leitores de tela).
- **Lacuna:** a11y é exigida em muitos projetos e em diretrizes de plugins WordPress; vale complementar com WCAG e documentação de a11y no WordPress.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Princípios a11y no WordPress** | Novo .md em estudos-wp (ex.: pasta `12b-Acessibilidade` ou anexo em 12) | Resumo: navegação por teclado, contraste, texto alternativo, ARIA quando necessário; requisitos do repositório de plugins (.org). |
| **Escape e semântica** | Seção em .md | Uso de `esc_html`, `esc_attr` já ajuda; elementos semânticos (`<nav>`, `<main>`, `<button>` vs `<div onclick>`); labels em formulários. |
| **ARIA básico** | Tabela ou lista em .md | `aria-label`, `aria-describedby`, `role`, `aria-expanded`; quando usar; exemplo em botão de menu e modal. |
| **Contraste e cores** | Checklist em .md | Razão de contraste (WCAG AA); não depender só de cor para informação; ferramentas (ex.: axe DevTools). |
| **Blocos e a11y** | Guia em .md | Requisitos de a11y para blocos (Block Editor Handbook); teste com leitor de tela; foco e ordem de tab. |
| **Checklist a11y para plugin** | Novo .md (ex.: `13-checklist-a11y.md`) | Formulários com label; botões com texto ou aria-label; contraste; teclado; link para [WordPress Accessibility](https://make.wordpress.org/accessibility/). |
| **Recursos externos** | Referência | [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/), [Block Editor – Accessibility](https://developer.wordpress.org/block-editor/how-to-guides/accessibility/). |

---

### 3.6 GDPR / LGPD (privacidade e dados)

- **No índice:** “Violação de LGPD/GDPR” aparece em contexto de **segurança/dados sensíveis** (Fase 19); **não há** capítulo específico sobre **política de privacidade**, **exportação/exclusão de dados pessoais**, **consentimento**, **Data Processing Agreement**.
- **Lacuna:** conformidade com GDPR/LGPD em plugins (ex.: ferramentas de exportar/apagar dados do usuário) precisa ser estudada à parte.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Conceitos GDPR/LGPD no plugin** | Novo .md em estudos-wp (ex.: anexo em 12 ou pasta `12c-Privacidade`) | Base legal; consentimento; minimização de dados; direito de acesso, retificação, exclusão, portabilidade. |
| **Exportação de dados do usuário** | Exemplo em .php | Implementar handler para “Exportar meus dados”: coletar dados do usuário (user_meta, post_meta, options); gerar JSON ou ZIP; endpoint ou página de ferramentas; link com ferramenta nativa do WP (`Tools > Export Personal Data`). |
| **Exclusão de dados (right to be forgotten)** | Exemplo em .php | Anonimizar ou apagar usuário e dados relacionados; `wp_delete_user`; apagar post_meta/comment_meta; log do que foi removido; integração com `Tools > Erase Personal Data`. |
| **Política de privacidade e consentimento** | Guia em .md | Como declarar coleta de dados; checkbox de consentimento em formulários; registro de consentimento (data/finalidade); modelo de texto para política. |
| **Hooks do Core (privacidade)** | Tabela em .md | `wp_privacy_personal_data_exporters`, `wp_privacy_personal_data_erasers`; como registrar exportador e apagador no plugin. |
| **Checklist “Plugin e privacidade”** | Novo .md (ex.: `14-checklist-privacidade.md`) | Coleta só o necessário; política atualizada; exportar/apagar implementado; consentimento quando aplicável; documentação. |
| **Recursos externos** | Referência | [WordPress Privacy Handbook](https://developer.wordpress.org/plugins/privacy/), [GDPR](https://gdpr.eu/), LGPD (lei brasileira). |

---

### 3.7 GraphQL (WPGraphQL)

- **No índice:** Fase 15 – “GraphQL para WordPress”.
- **Na prática (estudos-wp):** há exemplo em 15 (headers, rate limit, GraphQL); a **profundidade** depende do conteúdo do 015-WordPress-Fase-15.
- **Lacuna:** se o foco for **headless com GraphQL**, pode ser necessário aprofundar na documentação do WPGraphQL.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Instalação e configuração WPGraphQL** | Guia em .md em estudos-wp/15 | Instalar plugin WPGraphQL; endpoint `/graphql`; teste com GraphiQL (interface no admin); autenticação (JWT ou header). |
| **Query básica (posts, usuário)** | Exemplo em .md ou .graphql | Query `posts`, `post(id)`, `users`; campos retornados; fragmentos; diferença em relação a REST (uma request, campos sob demanda). |
| **Expor CPT e campos custom** | Exemplo em .php | Registrar CPT com suporte a GraphQL; `register_graphql_field` para campos; conexões (relação post → terms). |
| **Mutations (criar/atualizar)** | Exemplo em .php ou .md | Mutation `createPost`, `updatePost`; input types; validação e permissões. |
| **Headless: consumir GraphQL no front** | Guia em .md | Cliente (Apollo, fetch) no Next/React; variáveis e cache; boas práticas (evitar over-fetch). |
| **Tabela REST vs GraphQL** | Item em REFERENCIA-RAPIDA ou .md | Quando usar REST vs GraphQL; prós e contras no contexto WordPress. |
| **Recursos externos** | Referência | [WPGraphQL](https://www.wpgraphql.com/), [WPGraphQL Docs](https://www.wpgraphql.com/docs/intro). |

---

### 3.8 Contribuição ao core e à comunidade

- **No índice:** Fase 15 – “Contribuir com o WordPress”, “Padrões do repositório de plugins”.
- **Na prática (estudos-wp):** há menção em 15 (community, code review); **não há** passo a passo de **patch ao core**, **Trac**, **SVN**, **guidelines de contribuição**.
- **Lacuna:** quem quiser **contribuir com patches ao WordPress.org** deve usar a documentação oficial de contribuição.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Contribuir com o core (passo a passo)** | Novo .md em estudos-wp/15 (ex.: `13-contribuir-core.md`) | Criar conta em WordPress.org; acessar Trac; encontrar ticket; criar branch SVN; fazer alteração; gerar patch; anexar no ticket; referência a [Core Contributor Handbook](https://make.wordpress.org/core/handbook/). |
| **Publicar plugin no .org** | Guia em .md | Requisitos (slug, readme.txt, licença GPL, sem código ofensivo); SVN do repositório; primeiro commit; tags e atualizações; guidelines de revisão. |
| **Padrões de código (PHPCS, WordPress-Core)** | Exemplo em .md | Configurar PHP_CodeSniffer com ruleset WordPress-Core; rodar no plugin; corrigir warnings; integração em CI. |
| **Code review (o que revisores olham)** | Checklist em .md | Segurança (escape, nonce, capability); performance; i18n; a11y; documentação; link para [Plugin Handbook – Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/). |
| **Comunidade (slack, make teams)** | Lista em .md | Slack Make WordPress; times (Core, Plugins, Themes, etc.); como pedir ajuda e onde reportar bugs. |
| **Recursos externos** | Referência | [Make WordPress](https://make.wordpress.org/), [Plugin Handbook](https://developer.wordpress.org/plugins/), [Core Handbook](https://make.wordpress.org/core/handbook/). |

---

### 3.9 Escalabilidade extrema (multi-servidor, CDN, filas externas)

- **No índice:** Performance (Fase 8), DevOps (14), Jobs (16) tratam de cache, deploy, Action Scheduler.
- **Lacuna:** **escalar** para muitos milhões de pageviews (load balancer, Redis/Memcached distribuído, fila externa como RabbitMQ/SQS) está mais no nível “conceito” do que em guia passo a passo; isso é comum em roadmaps e pode ser complementado com material de infraestrutura.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Object cache persistente (Redis/Memcached)** | Guia em .md em estudos-wp/08 ou 14 | Instalar extensão PHP (redis/memcached); configurar `wp-config.php` (WP_REDIS_HOST, etc.); plugin Redis Object Cache ou equivalente; verificar que transients e wp_cache usam Redis. |
| **Load balancer e sessões** | Guia em .md | Problema: sessões PHP por servidor; solução: sticky session ou armazenar sessão em Redis; configuração Nginx/HAProxy como exemplo. |
| **CDN para assets e páginas** | Guia em .md | O que colocar no CDN (imagens, CSS, JS); invalidação de cache; CloudFront/Cloudflare como exemplo; integração com WordPress (constante ou plugin). |
| **Fila externa (RabbitMQ, SQS)** | Conceito e exemplo em .md | Quando Action Scheduler não basta (throughput, múltiplos workers); padrão: publicar mensagem na fila; worker consome; exemplo de integração mínima (enviar para SQS no lugar de as_enqueue_async_action). |
| **Banco read replica** | Guia em .md | Leitura em réplica; escrita no primário; plugin ou constante para definir $wpdb para leitura; cuidados (replicação lag). |
| **Checklist “Escala horizontal”** | Novo .md (ex.: em 14 `11-escalabilidade-checklist.md`) | Cache persistente; sessões centralizadas; CDN; fila externa se necessário; monitoramento; referência a documentação de hospedagem (Kinsta, WP Engine, etc.). |
| **Recursos externos** | Referência | Documentação de Redis/Memcached para PHP; AWS SQS; guias de scaling WordPress (Kinsta, etc.). |

---

### 3.10 Temas clássicos (PHP) em profundidade

- **No índice:** Fase 1 (template hierarchy), Fase 5 (archive/single para CPT), Fase 6 (shortcodes/widgets); Fase 18 – “Desenvolvedor de Temas”.
- **Na prática (estudos-wp):** hierarchy e CPT estão cobertos; **não há** uma “fase só de tema” (sidebar, footer, menus, customizer, child theme, starter theme).
- **Lacuna:** o roadmap é **forte em plugin e API**; **temas clássicos** aparecem de forma distribuída. Quem quiser ser “especialista em temas” pode precisar de um módulo ou curso focado em tema.

**Oportunidades de complemento (detalhadas):**

| O que complementar | Onde / formato | Descrição |
|--------------------|----------------|-----------|
| **Estrutura mínima de um tema** | Novo .md (ex.: pasta `05b-Temas-Classicos` ou anexo em 05) | Arquivos obrigatórios: `style.css`, `index.php`; `functions.php`; hierarquia de templates (reforçar com foco em tema). |
| **Sidebar e widgets** | Exemplo em .php | `register_sidebar`; `dynamic_sidebar`; widgets no Customizer; tema com uma sidebar e um footer com widgets. |
| **Menus** | Exemplo em .php | `register_nav_menus`; `wp_nav_menu`; walker custom (opcional); location no tema. |
| **Customizer** | Exemplo em .php | `wp_customize`; `add_section`, `add_setting`, `add_control`; tema com opção de cor e logo. |
| **Child theme** | Guia em .md | Criar child theme; `@import` ou `wp_enqueue_style` do parent; override de templates e functions; quando usar. |
| **Starter theme (Underscores)** | Guia em .md | O que é _s; como usar como base; estrutura de pastas; template parts (get_template_part). |
| **Tabela “Plugin vs Tema”** | Item em REFERENCIA-RAPIDA ou .md | O que vai no tema (apresentação, layout) vs no plugin (lógica, CPT, API); boas práticas (não colocar lógica pesada no tema). |
| **Recursos externos** | Referência | [Theme Handbook](https://developer.wordpress.org/themes/), [Underscores](https://underscores.me/). |

---

## 4. Síntese: pode virar especialista? Há lacunas?

| Pergunta | Resposta |
|----------|----------|
| **Alguém que se dedicar pode se tornar especialista em WordPress em profundidade?** | **Sim**, nas dimensões **plugin, REST API, admin, CPT, segurança, arquitetura, testes, DevOps, jobs assíncronos e tratamento de erros**. O conjunto 000-WordPress-* + estudos-wp cobre da base ao nível especialista nessas áreas. |
| **Há lacunas não cobertas?** | **Sim**, em graus diferentes: **(1)** Blocos em **JavaScript/React** e **FSE/theme.json** – pouco prático; **(2)** **JWT/OAuth2** em REST – mais teoria que exemplo completo; **(3)** **WooCommerce** em profundidade – apenas integração/hooks; **(4)** **Acessibilidade**, **GDPR/LGPD** e **contribuição ao core** – não são o foco do material; **(5)** **Tema clássico** e **escalabilidade extrema** – cobertura parcial ou conceitual. |

Recomendação: usar o **roadmap + estudos-wp** como espinha dorsal e, conforme o objetivo (blocos JS, FSE, loja, a11y, conformidade, contribuição), **complementar** com documentação oficial ou cursos específicos nas lacunas listadas acima.

---

*Documento de análise – estudos-wp. Revisar conforme novas fases ou expansão do conteúdo.*
