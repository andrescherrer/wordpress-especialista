# 📚 Índice de Tópicos - Roadmap Completo WordPress

**Status:** Roadmap detalhado e completo para especialista em PHP  
**Data:** Janeiro 2026  
**Versão:** 1.0

---

## 📂 Navegação do repositório

| Fase | Documento |
|:----:|-----------|
| — | [📚 Índice (este arquivo)](000-WordPress-Topicos-Index.md) |
| 1 | [Fundamentos do WordPress Core](001-WordPress-Fase-1-Fundamentals%20of%20WordPress%20Core.md) |
| 2 | [REST API Fundamentals](002-WordPress-Fase-2-WordPress%20REST%20API%20Fundamentals.md) |
| 3 | [REST API Advanced](003-WordPress-Fase-3-REST-API-Advanced.md) |
| 4 | [Settings API e Admin](004-WordPress-Fase-4-Settings-Admin.md) |
| 5 | [Custom Post Types e Taxonomies](005-WordPress-Fase-5-Custom-Post-Types-Taxonomies.md) |
| 6 | [Shortcodes, Widgets e Gutenberg](006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md) |
| 7 | [WP-CLI Fundamentals](007-WordPress-Fase-7-WP-CLI-Fundamentals.md) |
| 8 | [Performance e Caching](008-WordPress-Fase-8-Performance-Cache-Otimizacao.md) |
| 9 | [WP-CLI Ferramentas](009-WordPress-Fase-9-WP-CLI-Ferramentas.md) |
| 10 | [Testing, Debugging e Deploy](010-WordPress-Fase-10-Testing-Debugging-Deploy.md) |
| 11 | [Multisite e Internacionalização](011-WordPress-Fase-11-Multisite-Internacionalizacao.md) |
| 12 | [Segurança e Boas Práticas](012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) |
| 13 | [Arquitetura Avançada](013-WordPress-Fase-13-Arquitetura-Avancada.md) |
| 14 | [Deployment e DevOps](014-WordPress-Fase-14-Deployment-DevOps.md) |
| + | [Tópicos complementares](015-WordPress-Topicos-Complementares-Avancados.md) |
| — | [📊 Análise do projeto](ANALISE-PROJETO-WORDPRESS-ESPECIALISTA.md) · [README](README.md) |

---

## 🎯 FASE 1: Fundamentos do WordPress Core

### 1.1 Arquitetura e Estrutura do WordPress
- Estrutura de diretórios (wp-admin, wp-includes, wp-content)
- Arquivos core essenciais
- Ordem de carregamento do WordPress
- Constantes definidas (ABSPATH, WP_CONTENT_DIR)

### 1.2 Hook System (Actions e Filters)
- Fundamentos de actions
- Fundamentos de filters
- Diferença entre actions e filters
- Hook priority (ordem de execução)
- Remove hooks
- Hooks condicionais e dinâmicos
- Named functions vs anonymous functions
- Múltiplos hooks no mesmo ponto

### 1.3 Estrutura do Banco de Dados
- Tabelas principais do WordPress
- Estrutura de posts (wp_posts)
- Estrutura de meta (wp_postmeta, wp_usermeta, wp_options)
- Estrutura de usuários (wp_users)
- Estrutura de términos (wp_terms)
- Relacionamentos (wp_term_relationships, wp_term_taxonomy)
- Índices e performance
- Custom tables

### 1.4 WordPress Database API ($wpdb)
- Global $wpdb
- Métodos básicos (get_results, get_row, get_var)
- Insert, Update, Delete
- Prepared statements (proteção contra SQL Injection)
- Table prefixes
- Transações (BEGIN, COMMIT, ROLLBACK)
- Charset e collation

### 1.5 Posts, Pages e Custom Content
- Post types nativos
- Post status (publish, draft, pending, etc)
- Posts vs Pages vs Attachments
- Revisões de posts
- Featured images
- Post parent/child relationships
- Post excerpts vs content

### 1.6 Template Hierarchy
- Ordem de resolução de templates
- Single post templates
- Archive templates
- Category/Tag templates
- Taxonomy templates
- Search templates
- 404 templates
- Index fallback

### 1.7 The Loop
- Conceito do loop
- have_posts() e the_post()
- get_the_ID(), get_the_title()
- the_content(), the_excerpt()
- Query reset e loops aninhados

### 1.8 WordPress Coding Standards
- PHPDoc padrão
- Naming conventions
- Code formatting
- File structure
- Plugin headers

---

## 🔌 FASE 2: WordPress REST API Fundamentals

### 2.1 Conceitos Básicos da REST API
- O que é REST
- Verbos HTTP (GET, POST, PUT, PATCH, DELETE)
- Status codes (200, 201, 400, 401, 404, etc)
- JSON request/response
- Endpoints padrão do WordPress

### 2.2 Registrar Rotas Customizadas
- register_rest_route()
- Argumentos de rota
- Métodos HTTP suportados
- Callbacks e controllers
- Permissões com permission_callback

### 2.3 REST Controllers (OOP)
- Estender WP_REST_Controller
- Registrar rotas em classe
- get_items(), create_item(), get_item(), update_item(), delete_item()
- Padrão CRUD completo
- Response consistency

### 2.4 Validação e Sanitização em APIs
- validate_callback para validação
- sanitize_callback para sanitização
- Args definition (type, required, enum, pattern)
- Custom validation functions
- Tratamento de erros

### 2.5 REST Authentication
- Autenticação básica
- Nonces em REST
- Application Passwords (WordPress 5.6+)
- JWT tokens customizados
- OAuth2
- Verificação de capabilities

### 2.6 REST Permissions
- current_user_can() em REST
- Verificação de roles
- Capabilities específicas
- Permission callbacks por método HTTP
- Public vs Authenticated endpoints

### 2.7 REST Response e Error Handling
- WP_REST_Response
- WP_Error
- Status codes apropriados
- Mensagens de erro claras
- Headers customizados

### 2.8 Documentação e Schema
- Schema de dados (WP_JSON_Schema)
- Documentação de endpoints
- Descrição de parâmetros
- Documentação de erros
- OpenAPI/Swagger integration

### 2.9 REST Filters Avançados
- rest_prepare_{post_type}
- rest_insert_{post_type}
- rest_post_query
- rest_{post_type}_collection_params
- Modificação de resposta
- Adicionar campos customizados

---

## 🏗️ FASE 3: Estrutura Profissional de Plugins

### 3.1 Anatomia Básica de um Plugin
- Plugin header (metadados)
- Funções de ativação/desativação
- Uninstall hook
- Main plugin file

### 3.2 Estrutura de Diretórios Profissional
- Separação de concerns
- Autoloading com Composer
- Namespaces
- PSR-4 compliance
- Estrutura recomendada (src, assets, templates, tests)

### 3.3 Plugin Classes e Patterns
- Singleton pattern
- Dependency Injection
- Service Container
- Factory pattern
- Observer pattern (hooks)

### 3.4 Composer Integration
- composer.json
- Autoloading automático
- Gerenciamento de dependências
- PSR-4 autoloader

### 3.5 Activation e Deactivation Hooks
- register_activation_hook()
- register_deactivation_hook()
- Criar tabelas ao ativar
- Limpar dados ao desativar
- Versionamento de plugin

### 3.6 Uninstall Hook
- register_uninstall_hook()
- Limpar dados do banco
- Remover opções
- Remover posts/meta
- Remover tabelas customizadas

### 3.7 Plugin Versioning
- Atualizar versão
- Executar migrations
- dbDelta() para alterações de schema
- Backward compatibility

---

## ⚙️ FASE 4: Settings API e Admin Pages

### 4.1 Settings API Fundamentos
- register_setting()
- add_settings_section()
- add_settings_field()
- Sanitização automática
- Nonces automáticos

### 4.2 Criar Páginas de Configuração
- add_options_page()
- add_menu_page()
- add_submenu_page()
- Hierarchia de menus
- Icones e posição

### 4.3 Admin Styling e Scripts
- admin_enqueue_scripts
- wp_enqueue_style()
- wp_enqueue_script()
- wp_localize_script()
- Condições de página (load-page)

### 4.4 Settings Form Rendering
- settings_fields()
- do_settings_sections()
- submit_button()
- Form customização

### 4.5 Meta Boxes
- add_meta_box()
- Rendering callbacks
- Salvar meta box data
- wp_nonce_field() e wp_verify_nonce()
- Context (normal, side, advanced)

### 4.6 Admin Notices
- add_settings_error()
- settings_errors()
- WP Admin notices styling
- Tipos (error, warning, success, info)

### 4.7 Admin Forms Avançado
- Campos dinâmicos (repeaters)
- Color picker
- Media uploader
- Date/Time picker
- Select2 integration

### 4.8 Validation e Sanitization em Forms
- Validação de entrada
- Sanitização de dados
- Mensagens de erro
- Redirect com parâmetros

---

## 📝 FASE 5: Custom Post Types e Taxonomies

### 5.1 Custom Post Types (CPT)
- register_post_type()
- Argumentos de CPT
- Labels e strings
- Supports (título, editor, thumbnail, etc)
- Menu position e icon
- Rewrite rules
- Capabilities e roles customizadas
- Archivos de CPT

### 5.2 Taxonomies Customizadas
- register_taxonomy()
- Argumentos de taxonomia
- Hierarchical vs flat
- Meta box rendering customizado
- Rewrite rules
- REST API exposure

### 5.3 Post Type Supports
- add_post_type_support()
- remove_post_type_support()
- Editor (Gutenberg)
- Featured image
- Revisions
- Custom fields
- Comments
- Trackbacks
- Post formats
- Author
- Excerpt
- Page attributes (parent)

### 5.4 Relacionamentos entre CPTs
- Relacionamentos hierárquicos (parent/child)
- Post relationships via meta
- Taxonomies compartilhadas
- Cross-post type queries

### 5.5 Archive Pages para CPTs
- Archive templates customizadas
- Pagination
- Filtros
- Sorting
- WP_Query customização

### 5.6 Single Post Templates
- Single templates customizadas
- Template hierarchy para CPTs
- Sidebar e layout options
- Related posts queries

### 5.7 Meta Boxes para CPTs
- Registrar meta boxes customizadas
- Rendering
- Salvar dados
- Nonces e validação
- Conditional meta boxes

### 5.8 Exposição em REST API
- show_in_rest parameter
- rest_base
- rest_controller_class
- Capabilities para REST

---

## 📦 FASE 6: Shortcodes, Widgets e Gutenberg Blocks

### 6.1 Shortcodes API
- add_shortcode()
- Shortcode processing
- Atributos (extract)
- Conteúdo aninhado
- Filtros de shortcode

### 6.2 Shortcode Avançado
- Validação de atributos
- Escaping de output
- Aninhamento
- Namespace de shortcodes
- Shortcodes em widgets

### 6.3 Widgets API Clássica
- WP_Widget class
- form(), widget(), update()
- Registrar widgets
- Sidebars/Widget Areas
- Styling widgets

### 6.4 Widgets Customizados
- Widgets com opções avançadas
- Media picker em widgets
- Color picker
- Repeater fields
- Widget preview

### 6.5 Block Editor (Gutenberg) Basics
- Blocos nativos
- Block registration
- Block attributes
- Block metadata
- block.json

### 6.6 Criar Custom Blocks
- JavaScript/React para blocos
- Block structure
- Attributes
- Validação
- Salvar dados

### 6.7 Block Styles e Variations
- Block CSS padrão
- Custom styles
- Block variations
- Conditional rendering

### 6.8 Dynamic Blocks
- Renderizar no backend vs frontend
- PHP render callback
- Dados dinâmicos
- Query customizadas em blocos

### 6.9 Block Patterns
- Criar padrões de blocos
- Registrar patterns
- Pattern categories

---

## ⏰ FASE 7: Cron Jobs e Background Processing

### 7.1 WordPress Cron (WP-Cron)
- Conceito de WP-Cron
- wp_schedule_event()
- wp_schedule_single_event()
- Frequências padrão (hourly, twicedaily, daily)
- Frequências customizadas

### 7.2 Cron Hooks
- do_action() executado por WP-Cron
- Argumentos de cron
- Múltiplas ações por frequência

### 7.3 Gerenciar Cron Jobs
- wp_get_scheduled_events()
- wp_unschedule_event()
- wp_unschedule_hook()
- wp_clear_scheduled_hook()
- wp_next_scheduled()

### 7.4 WP-Cron vs Sistema Cron
- Ativação baseada em requisições
- Desativação (DISABLE_WP_CRON)
- Sistema cron real (via crontab)
- Hybrid approach

### 7.5 Real Cron Setup
- Desabilitar WP-Cron
- Crontab Linux
- Frequência vs WP-Cron
- Troubleshooting

### 7.6 AJAX API
- wp_ajax_{action} hook
- wp_ajax_nopriv_{action} (públicos)
- Nonce verification
- JSON responses
- Error handling em AJAX

### 7.7 Background Tasks em Plugins
- Fila de tarefas simples
- Processar em background
- Logging de tasks
- Retry logic

### 7.8 Async Processing
- Usar cron para async
- wp_remote_post() para callbacks
- Loopback requests
- Timeout handling

---

## ⚡ FASE 8: Performance e Caching

### 8.1 Transients API
- set_transient()
- get_transient()
- delete_transient()
- Expiração de cache
- Transient naming conventions

### 8.2 Object Cache
- wp_cache_set()
- wp_cache_get()
- wp_cache_delete()
- Cache groups
- Non-persistent vs persistent cache

### 8.3 Object Cache Backends
- Memcached integration
- Redis integration
- WinCache
- APCu
- Persistent cache plugins

### 8.4 Cache Invalidation
- Quando invalidar cache
- Patterns para invalidação
- Cache dependencies
- Tag-based caching

### 8.5 Query Optimization
- N+1 query problems
- Meta query optimization
- WP_Query caching
- get_posts() performance
- Cache results

### 8.6 Post Meta Cache
- update_postmeta_cache()
- update_object_term_cache()
- Eager loading
- Cache warming

### 8.7 Lazy Load Posts
- wp_lazy_load_attr()
- Native lazy loading
- Plugin assets lazy loading

### 8.8 Asset Optimization
- Minification
- Concatenation
- wp_enqueue_script() dependency ordering
- Script placement (head vs footer)
- Async/defer attributes

### 8.9 Database Optimization
- Index optimization
- Query analysis (EXPLAIN)
- Slow query logging
- Table optimization
- Data archiving

### 8.10 Profiling e Debugging
- Query Monitor plugin
- Xdebug
- Debug logging
- Performance metrics
- Bottleneck identification

---

## 🛠️ FASE 9: WP-CLI e Development Tools

### 9.1 WP-CLI Basics
- Instalação
- wp --version
- wp core download/install/update
- wp plugin list/install/activate
- wp theme list/install/activate

### 9.2 WP-CLI Database
- wp db create/drop
- wp db export/import
- wp db query
- wp db optimize
- Backups via WP-CLI

### 9.3 WP-CLI Posts e Taxonomies
- wp post create/list/update/delete
- wp term create/list/update/delete
- Bulk operations
- Batch processing

### 9.4 WP-CLI Users
- wp user create/list/update/delete
- Gerenciar roles/capabilities
- Bulk user operations

### 9.5 WP-CLI Options
- wp option get/set/delete/list
- Modificar opções em lote

### 9.6 WP-CLI Config
- wp-cli.yml
- Aliases para ambientes
- Scripts customizados

### 9.7 Criar Custom WP-CLI Commands
- WP_CLI_Command class
- Registrar comando
- Argumentos e opções
- Output customizado
- Progress bars e tabelas

### 9.8 WP-CLI para Deploy
- Migrations
- Database sync
- Plugin installation
- Theme activation
- Configuration

---

## 🧪 FASE 10: Testing e Debugging

### 10.1 PHPUnit Basics
- Instalação
- Writing tests
- Setup/teardown
- Assertions
- Test suites

### 10.2 WordPress Unit Testing
- WP_UnitTestCase
- Factory para criar dados
- Fixtures
- Database transactions
- Test database isolation

### 10.3 Testing Plugins
- Bootstrap
- Plugin-specific tests
- Mocking WordPress functions
- Query assertions

### 10.4 Testing REST API
- REST API tests
- Mock requests
- Response assertions
- Autenticação em testes

### 10.5 Test Data Factories
- WP_UnitTest_Factory
- Criar posts, users, terms
- Relações entre dados
- Reutilizar factory methods

### 10.6 Mocking em WordPress
- Mockery/Prophecy
- Mockar funções externas
- Mockar API calls
- Stub WordPress functions

### 10.7 Code Coverage
- Gerar coverage reports
- Analisar cobertura
- Identificar gaps
- Target de coverage

### 10.8 Debug Logging
- error_log()
- Debug logging functions
- Debug plugins (Query Monitor)
- Stack traces

### 10.9 Xdebug Setup
- Instalação
- IDE integration (PhpStorm, VSCode)
- Breakpoints
- Variable inspection
- Step debugging

### 10.10 Debug Mode WordPress
- WP_DEBUG
- WP_DEBUG_LOG
- WP_DEBUG_DISPLAY
- SCRIPT_DEBUG
- SAVEQUERIES

---

## 🌍 FASE 11: Multisite e Internacionalização

### 11.1 WordPress Multisite Basics
- Multisite vs Single site
- Network setup
- Subdomínio vs subdirectório
- Tabelas de rede

### 11.2 Multisite Database Structure
- Tabelas comuns
- Tabelas por site (prefixes dinâmicos)
- Users vs Sites vs Blogs
- Network options

### 11.3 Plugin Behavior em Multisite
- Plugin ativação por site vs rede
- Network activation
- Per-site configuration
- Shared data vs per-site data

### 11.4 Multisite API
- switch_to_blog()
- get_blog_option()
- get_sites()
- get_blog_details()
- Loops multisite

### 11.5 Internacionalização (i18n)
- Text domains
- __() function
- esc_html__()
- Pluralization (_n())

### 11.6 Localization (l10n)
- .pot files (template)
- .po files (translations)
- .mo files (compiled)
- load_plugin_textdomain()
- load_textdomain()

### 11.7 Locale e Language
- get_locale()
- get_user_locale()
- get_blog_language_attributes()
- date_i18n()

### 11.8 Translation Workflows
- Generating .pot
- Translating .po
- Compiling .mo
- Translation management platforms (GlotPress)

### 11.9 RTL (Right-to-Left) Support
- RTL stylesheets
- wp-content/languages/
- RTL language detection
- is_rtl()

---

## 🔐 FASE 12: Security Avançada

### 12.1 Sanitização vs Validação vs Escaping
- Diferenças conceituais
- Quando aplicar cada uma
- Funções específicas

### 12.2 Input Sanitization Functions
- sanitize_text_field()
- sanitize_email()
- sanitize_url()
- wp_kses() variants
- Custom sanitization

### 12.3 Output Escaping Functions
- esc_html()
- esc_attr()
- esc_url()
- esc_js()
- wp_kses_post()

### 12.4 SQL Injection Prevention
- Prepared statements
- $wpdb->prepare()
- Placeholders (%d, %s, %f)
- Never trust user input

### 12.5 Cross-Site Scripting (XSS)
- DOM-based XSS
- Stored XSS
- Reflected XSS
- Escaping prevention

### 12.6 Cross-Site Request Forgery (CSRF)
- Nonce concept
- wp_create_nonce()
- wp_verify_nonce()
- wp_nonce_field()
- wp_nonce_url()

### 12.7 Capability Checking
- current_user_can()
- Capabilities vs roles
- Custom capabilities
- Granular permissions

### 12.8 File Upload Security
- Validação de tipo
- Validação de size
- wp_handle_upload()
- Whitelist de tipos
- Scan for malware

### 12.9 Plugin Security Audit
- Code review
- Vulnerability scanning
- Dependency audit (Composer)
- Third-party API validation

### 12.10 Security Best Practices
- Never disable security functions
- Update regularly
- Minimize admin access
- Rate limiting
- Two-factor authentication
- Security headers
- HTTPS enforcement

---

## 📊 FASE 13: Arquitetura Avançada

### 13.1 SOLID Principles em WordPress
- Single Responsibility
- Open/Closed
- Liskov Substitution
- Interface Segregation
- Dependency Inversion

### 13.2 Domain-Driven Design (DDD)
- Entities
- Value Objects
- Repositories
- Services
- Domain Events

### 13.3 Service Layer Pattern
- Separação de concerns
- Business logic isolation
- Reusability
- Testability

### 13.4 Repository Pattern
- Data abstraction
- Query isolation
- Multiple persistence layers
- Collection handling

### 13.5 Dependency Injection Container
- Service Container
- Auto-wiring
- Lazy loading
- Lifecycle management

### 13.6 Event-Driven Architecture
- Events customizados
- Event listeners
- Event dispatching
- Decoupling via events

### 13.7 MVC em WordPress
- Models (Data)
- Views (Templates)
- Controllers (Logic)
- Integração com hooks

### 13.8 Adapter Pattern para APIs Externas
- Abstrair integrações
- Multiple provider support
- Fallback strategies
- Error handling

### 13.9 Strategy Pattern
- Diferentes estratégias
- Runtime selection
- Payment gateways
- Storage backends

### 13.10 Factory Pattern
- Object creation
- Complex instantiation
- Configuration management

---

## 🚀 FASE 14: Deployment e DevOps

### 14.1 Development Environment
- Docker para WordPress
- Compose file
- Database container
- Nginx/Apache container
- PHP-FPM
- Volume management

### 14.2 Staging Environment
- Replicar production
- Database sync
- Asset sync
- Testing
- Performance testing

### 14.3 Production Environment
- Server setup
- Security hardening
- SSL/TLS
- PHP configuration
- Database optimization
- Backup strategy

### 14.4 Version Control (Git)
- Gitignore patterns
- Commit organization
- Branch strategy
- Merge requests/Pull requests

### 14.5 CI/CD Pipeline
- GitHub Actions
- GitLab CI
- Jenkins
- Testing automation
- Deployment automation

### 14.6 Automated Testing in Pipeline
- PHPUnit execution
- Code quality checks
- Security scanning
- Build artifacts

### 14.7 Automated Deployment
- Stage deployment
- Production deployment
- Database migrations
- Plugin updates
- Rollback strategy

### 14.8 Monitoring e Logging
- Application logs
- Error tracking (Sentry)
- Performance monitoring
- Uptime monitoring
- Alert configuration

### 14.9 Backup Strategy
- Database backups
- File backups
- Incremental backups
- Offsite backup
- Restore testing

### 14.10 Disaster Recovery
- RTO/RPO targets
- Recovery procedures
- Documentation
- Testing recovery

---

## 🎯 Tópicos Complementares

### Advanced API Topics
- GraphQL for WordPress
- Custom header validation
- Request throttling
- API versioning
- Deprecation handling
- API documentation (OpenAPI/Swagger)

### Advanced Performance
- Page speed optimization
- Image optimization
- Code splitting
- Progressive enhancement
- Core Web Vitals
- Lighthouse optimization

### WordPress Ecosystem
- WooCommerce integration
- ACF (Advanced Custom Fields)
- Jetpack
- Akismet
- WP Rocket
- Other popular plugins

### Headless WordPress
- REST API as primary interface
- Decoupled frontend
- React/Vue.js frontend
- Static site generation
- Jamstack architecture

### Community e Best Practices
- Contributing to WordPress
- Plugin repository standards
- Code review practices
- Documentation standards
- Community guidelines

---

## 📌 Recursos por Fase

**Fase 1:** WordPress.org Core Handbook  
**Fase 2:** REST API Handbook  
**Fase 3-6:** Plugin Handbook  
**Fase 7-8:** Performance Guide  
**Fase 9:** WP-CLI Handbook  
**Fase 10:** Unit Test Handbook  
**Fase 11:** Multisite & i18n  
**Fase 12:** Security & Plugin Review  
**Fase 13:** Architecture Patterns  
**Fase 14:** Deployment & DevOps  

---

## ✅ Checklist de Masterização

- [ ] Entendo completamente hooks system
- [ ] Domino REST API creation e controllers
- [ ] Estruturo plugins profissionalmente
- [ ] Implemento Settings API completa
- [ ] Crio CPT e taxonomies avançadas
- [ ] Desenvolvo shortcodes e blocks
- [ ] Implemento cron e async tasks
- [ ] Otimizo performance com cache
- [ ] Uso WP-CLI produtivamente
- [ ] Escrevo testes com PHPUnit
- [ ] Implemento multisite corretamente
- [ ] Internationalize plugins
- [ ] Aplico security best practices
- [ ] Arquitetura com padrões SOLID
- [ ] Deploy com CI/CD automation

---

**Versão:** 1.0  
**Status:** Completo e atualizado  
**Próxima revisão:** Q2 2026
