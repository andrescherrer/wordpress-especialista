# Índice de Tópicos - Roadmap Completo WordPress

**Status:** Roadmap detalhado e completo para especialista em PHP  
**Data:** Fevereiro 2026  
**Versão:** 2.0

---

## Navegação do repositório

| # | Documento |
|:--:|-----------|
| 0 | [Índice (este arquivo)](./000-WordPress-Indice-Topicos.md) |
| 1 | [Fundamentos do WordPress Core](./001-WordPress-Fase-1-Fundamentos-Core.md) |
| 2 | [REST API Fundamentos](./002-WordPress-Fase-2-REST-API-Fundamentos.md) |
| 3 | [REST API Avançado](./003-WordPress-Fase-3-REST-API-Avancado.md) |
| 4 | [Configurações e Admin](./004-WordPress-Fase-4-Configuracoes-Admin.md) |
| 5 | [CPT e Taxonomias](./005-WordPress-Fase-5-CPT-Taxonomias.md) |
| 6 | [Shortcodes, Widgets e Gutenberg](./006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md) |
| 7 | [WP-CLI Fundamentos](./007-WordPress-Fase-7-WP-CLI-Fundamentos.md) |
| 8 | [Performance e Caching](./008-WordPress-Fase-8-Performance-Cache-Otimizacao.md) |
| 9 | [WP-CLI Ferramentas](./009-WordPress-Fase-9-WP-CLI-Ferramentas.md) |
| 10 | [Testes, Debug e Implantação](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md) |
| 11 | [Multisite e Internacionalização](./011-WordPress-Fase-11-Multisite-Internacionalizacao.md) |
| 12 | [Segurança e Boas Práticas](./012-WordPress-Fase-12-Seguranca-Boas-Praticas.md) |
| 13 | [Arquitetura Avançada](./013-WordPress-Fase-13-Arquitetura-Avancada.md) |
| 14 | [Implantação e DevOps](./014-WordPress-Fase-14-Implantacao-DevOps.md) |
| 15 | [Jobs Assíncronos e Background](./016-WordPress-Fase-15-Jobs-Assincronos-Background.md) |
| 16 | [Tópicos Complementares Avançados](./015-WordPress-Fase-16-Topicos-Complementares-Avancados.md) |
| 17 | [Testes em Toda Fase](./017-WordPress-Fase-17-Testes-Em-Toda-Fase.md) |
| 18 | [Caminhos de Aprendizado](./018-WordPress-Fase-18-Caminhos-Aprendizado.md) |
| 19 | [Anti-padrões de Segurança](./019-WordPress-Fase-19-Anti-padroes-Seguranca.md) |
| 20 | [Boas Práticas de Tratamento de Erros](./020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md) |
| — | [README](./README.md) |

---

## 🎯 FASE 1: Fundamentos do WordPress Core

### 1.1 Arquitetura e Estrutura do WordPress
- Estrutura de diretórios (wp-admin, wp-includes, wp-content)
- Arquivos core essenciais
- Ordem de carregamento do WordPress
- Constantes definidas (ABSPATH, WP_CONTENT_DIR)

### 1.2 Sistema de Hooks (Actions e Filters)
- Fundamentos de actions
- Fundamentos de filters
- Diferença entre actions e filters
- Prioridade de hooks (ordem de execução)
- Remover hooks
- Hooks condicionais e dinâmicos
- Funções nomeadas vs funções anônimas
- Múltiplos hooks no mesmo ponto

### 1.3 Estrutura do Banco de Dados
- Tabelas principais do WordPress
- Estrutura de posts (wp_posts)
- Estrutura de meta (wp_postmeta, wp_usermeta, wp_options)
- Estrutura de usuários (wp_users)
- Estrutura de términos (wp_terms)
- Relacionamentos (wp_term_relationships, wp_term_taxonomy)
- Índices e performance
- Tabelas customizadas

### 1.4 WordPress Database API ($wpdb)
- Global $wpdb
- Métodos básicos (get_results, get_row, get_var)
- Inserir, atualizar, excluir
- Prepared statements (proteção contra SQL Injection)
- Prefixos de tabela
- Transações (BEGIN, COMMIT, ROLLBACK)
- Charset e collation

### 1.5 Posts, Pages e Conteúdo Customizado
- Post types nativos
- Status de post (publicado, rascunho, pendente, etc.)
- Posts vs Pages vs Anexos
- Revisões de posts
- Imagens destacadas
- Relacionamentos pai/filho de posts
- Resumos vs conteúdo

### 1.6 Hierarquia de Templates
- Ordem de resolução de templates
- Templates de post único
- Templates de arquivo
- Templates de categoria/tag
- Templates de taxonomia
- Templates de busca
- Templates 404
- Fallback de índice

### 1.7 O Loop (The Loop)
- Conceito do loop
- have_posts() e the_post()
- get_the_ID(), get_the_title()
- the_content(), the_excerpt()
- Query reset e loops aninhados

### 1.8 Padrões de Código WordPress
- PHPDoc padrão
- Convenções de nomenclatura
- Formatação de código
- Estrutura de arquivos
- Cabeçalhos de plugin

---

## 🔌 FASE 2: REST API Fundamentos

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

### 2.3 Controllers REST (OOP)
- Estender WP_REST_Controller
- Registrar rotas em classe
- get_items(), create_item(), get_item(), update_item(), delete_item()
- Padrão CRUD completo
- Consistência de resposta

### 2.4 Validação e Sanitização em APIs
- validate_callback para validação
- sanitize_callback para sanitização
- Definição de argumentos (type, required, enum, pattern)
- Funções de validação customizadas
- Tratamento de erros

### 2.5 Essenciais de Segurança
- Validação de entrada vs Sanitização vs Escape
- Funções de sanitização (sanitize_text_field, sanitize_email, etc.)
- Funções de escape (esc_html, esc_attr, esc_url, etc.)
- Nonces básico (wp_nonce_field, wp_verify_nonce)
- Verificação de capabilities (current_user_can)
- Checklist de segurança
- Erros comuns de segurança

### 2.6 REST Authentication
- Autenticação básica
- Nonces em REST
- Senhas de aplicação (WordPress 5.6+)
- JWT tokens customizados
- OAuth2
- Verificação de capabilities

### 2.7 REST Permissions
- current_user_can() em REST
- Verificação de roles
- Capabilities específicas
- Callbacks de permissão por método HTTP
- Endpoints públicos vs autenticados

### 2.8 Resposta REST e Tratamento de Erros
- WP_REST_Response
- WP_Error
- Status codes apropriados
- Mensagens de erro claras
- Headers customizados

### 2.9 Documentação e Schema
- Schema de dados (WP_JSON_Schema)
- Documentação de endpoints
- Descrição de parâmetros
- Documentação de erros
- Integração OpenAPI/Swagger

### 2.10 REST Filters Avançados
- rest_prepare_{post_type}
- rest_insert_{post_type}
- rest_post_query
- rest_{post_type}_collection_params
- Modificação de resposta
- Adicionar campos customizados

---

## 🏗️ FASE 3: REST API Avançado

### 3.1 Conceitos Fundamentais
- O que é uma REST API
- Vantagens da REST API vs funções do WordPress
- Endpoints HTTP estruturados

### 3.2 REST API Controllers
- Estrutura base de um controller
- Estender WP_REST_Controller
- register_routes(), get_items(), get_item(), create_item(), update_item(), delete_item()
- Namespace e rest_base

### 3.3 Resposta Estruturada
- Padrão de resposta consistente
- Estrutura completa de resposta (data, meta, links)
- WP_REST_Response

### 3.4 Validação e Sanitização
- Validação de entrada
- Sanitização de saída (escape)
- Definição de argumentos em rotas
- Funções de validação customizadas

### 3.5 Autenticação e Permissões
- Autenticação JWT (JSON Web Token)
- Callbacks de permissão
- Verificação de capabilities em REST

### 3.6 Tratamento de Erros
- Tratamento de erros avançado em controllers
- WP_Error e status codes
- Mensagens de erro claras

### 3.7 Testes de API
- Testar endpoints com cURL
- Testes com PHPUnit para REST API
- Requisições mock e asserções de resposta

### 3.8 Boas Práticas
- Checklist de qualidade
- Estrutura de projeto recomendada
- Documentação de endpoints

---

## ⚙️ FASE 4: Configurações e Admin

### 4.1 Fundamentos da Settings API
- register_setting(), add_settings_section(), add_settings_field()
- Sanitização automática e nonces
- Registrar settings

### 4.2 Criar Páginas de Configuração
- add_options_page(), add_menu_page(), add_submenu_page()
- Hierarquia de menus, ícones e posição

### 4.3 Admin Styling e Scripts
- admin_enqueue_scripts
- wp_enqueue_style(), wp_enqueue_script(), wp_localize_script()
- Condições de página

### 4.4 Meta Boxes
- add_meta_box()
- Callbacks de renderização e salvar dados
- wp_nonce_field(), wp_verify_nonce()
- Contexto (normal, side, advanced)

### 4.5 Admin Notices
- add_settings_error(), settings_errors()
- Tipos (error, warning, success, info)

### 4.6 Validação e Sanitização
- Validação de entrada em formulários
- Sanitização de dados e mensagens de erro

### 4.7 Admin Forms Avançado
- Campos dinâmicos (repeaters)
- Seletor de cor, seletor de mídia, seletor de data/hora

---

## 📝 FASE 5: CPT e Taxonomias

### 5.1 Tipos de Post Customizados (CPT)
- register_post_type()
- Argumentos de CPT
- Labels e strings
- Suportes (título, editor, thumbnail, etc.)
- Posição no menu e ícone
- Regras de rewrite
- Capabilities e roles customizadas
- Arquivos de CPT

### 5.2 Taxonomias Customizadas
- register_taxonomy()
- Argumentos de taxonomia
- Hierárquica vs plana
- Renderização customizada de meta box
- Regras de rewrite
- Exposição na REST API

### 5.3 Suportes do Tipo de Post
- add_post_type_support()
- remove_post_type_support()
- Editor (Gutenberg)
- Imagem destacada
- Revisões
- Campos customizados
- Comentários
- Trackbacks
- Formatos de post
- Autor
- Resumo
- Atributos de página (parent)

### 5.4 Relacionamentos entre CPTs
- Relacionamentos hierárquicos (parent/child)
- Relacionamentos de posts via meta
- Taxonomias compartilhadas
- Consultas entre tipos de post

### 5.5 Páginas de Arquivo para CPTs
- Templates de arquivo customizadas
- Paginação
- Filtros
- Ordenação
- Customização de WP_Query

### 5.6 Templates de Post Único
- Templates single customizados
- Hierarquia de templates para CPTs
- Sidebar e opções de layout
- Consultas de posts relacionados

### 5.7 Meta Boxes para CPTs
- Registrar meta boxes customizadas
- Renderização
- Salvar dados
- Nonces e validação
- Meta boxes condicionais

### 5.8 Exposição em REST API
- Parâmetro show_in_rest
- rest_base
- rest_controller_class
- Capabilities para REST

---

## 📦 FASE 6: Shortcodes, Widgets e Gutenberg Blocks

### 6.1 API de Shortcodes
- add_shortcode()
- Processamento de shortcode
- Atributos (extract)
- Conteúdo aninhado
- Filtros de shortcode

### 6.2 Shortcode Avançado
- Validação de atributos
- Escape de saída
- Aninhamento
- Namespace de shortcodes
- Shortcodes em widgets

### 6.3 API Clássica de Widgets
- Classe WP_Widget
- form(), widget(), update()
- Registrar widgets
- Sidebars e áreas de widget
- Estilização de widgets

### 6.4 Widgets Customizados
- Widgets com opções avançadas
- Seletor de mídia em widgets
- Seletor de cor
- Campos repetidores
- Pré-visualização de widget

### 6.5 Básicos do Editor de Blocos (Gutenberg)
- Blocos nativos
- Registro de blocos
- Atributos de bloco
- Metadados de bloco
- block.json

### 6.6 Criar Blocos Customizados
- JavaScript/React para blocos
- Estrutura de bloco
- Atributos
- Validação
- Salvar dados

### 6.7 Estilos e Variações de Blocos
- CSS padrão de blocos
- Estilos customizados
- Variações de bloco
- Renderização condicional

### 6.8 Blocos Dinâmicos
- Renderizar no backend vs frontend
- Callback de renderização em PHP
- Dados dinâmicos
- Consultas customizadas em blocos

### 6.9 Padrões de Blocos
- Criar padrões de blocos
- Registrar padrões
- Categorias de padrões

---

## ⏰ FASE 7: WP-CLI Fundamentos

### 7.1 Fundamentos do WP-CLI
- O que é WP-CLI
- Instalação
- Informações do sistema (wp --version, wp cli info)

### 7.2 Comandos Básicos Essenciais
- Core WordPress (core download, install, update)
- Plugins (plugin list, install, activate)
- Temas (theme list, install, activate)
- Posts e conteúdo
- Usuários
- Banco de dados
- Cache e transients

### 7.3 Criar Comandos WP-CLI Customizados
- Estrutura básica de um comando
- WP_CLI_Command
- Registrar comando (argumentos e opções)
- Saída (tabelas, barras de progresso)

### 7.4 Subcomandos e Hierarquia
- Subcomandos e estrutura hierárquica
- Comandos aninhados

### 7.5 Comandos com Interatividade
- Input do usuário
- Confirmações
- Seleção interativa

### 7.6 Comandos com Testes
- Testar comandos WP-CLI
- Integração com PHPUnit

### 7.7 Scaffolding com WP-CLI
- Gerar plugins, temas, CPT
- wp scaffold

### 7.8 Comandos de Database
- wp db export, import, query
- Backups via WP-CLI
- db create, drop, optimize

### 7.9 Boas Práticas
- Documentação de comandos
- Tratamento de erros
- Progresso e output
- Performance e segurança

---

## ⚡ FASE 8: Performance e Caching

### 8.1 Transients API
- set_transient()
- get_transient()
- delete_transient()
- Expiração de cache
- Convenções de nomenclatura de transients

### 8.2 Object Cache
- wp_cache_set()
- wp_cache_get()
- wp_cache_delete()
- Cache groups
- Cache não persistente vs persistente

### 8.3 Object Cache Backends
- Memcached integration
- Redis integration
- WinCache
- APCu
- Persistent cache plugins

### 8.4 Invalidação de Cache
- Quando invalidar cache
- Padrões para invalidação
- Dependências de cache
- Cache baseado em tags

### 8.5 Otimização de Queries
- Problemas de N+1 queries
- Otimização de meta query
- Cache de WP_Query
- Performance de get_posts()
- Resultados em cache

### 8.6 Cache de Post Meta
- update_postmeta_cache()
- update_object_term_cache()
- Carregamento antecipado
- Aquecimento de cache

### 8.7 Carregamento Preguiçoso de Posts
- wp_lazy_load_attr()
- Carregamento preguiçoso nativo
- Carregamento preguiçoso de assets de plugin

### 8.8 Otimização de Assets
- Minificação
- Concatenação
- Ordem de dependência em wp_enqueue_script()
- Posicionamento de scripts (head vs footer)
- Atributos async/defer

### 8.9 Otimização de Banco de Dados
- Otimização de índices
- Análise de queries (EXPLAIN)
- Registro de queries lentas
- Otimização de tabelas
- Arquivamento de dados

### 8.10 Profiling e Depuração
- Plugin Query Monitor
- Xdebug
- Registro de debug
- Métricas de performance
- Identificação de gargalos

---

## 🛠️ FASE 9: WP-CLI Ferramentas

### 9.1 Básicos do WP-CLI
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

### 9.3 WP-CLI Posts e Taxonomias
- wp post create/list/update/delete
- wp term create/list/update/delete
- Operações em lote
- Processamento em lote

### 9.4 WP-CLI Usuários
- wp user create/list/update/delete
- Gerenciar roles e capabilities
- Operações em lote de usuários

### 9.5 WP-CLI Options
- wp option get/set/delete/list
- Modificar opções em lote

### 9.6 WP-CLI Config
- wp-cli.yml
- Aliases para ambientes
- Scripts customizados

### 9.7 Criar Comandos WP-CLI Customizados
- Classe WP_CLI_Command
- Registrar comando
- Argumentos e opções
- Saída customizada
- Barras de progresso e tabelas

### 9.8 WP-CLI para Deploy
- Migrações
- Sincronização de banco de dados
- Instalação de plugins
- Ativação de temas
- Configuração

---

## 🧪 FASE 10: Testes, Debug e Implantação

### 10.1 Básicos do PHPUnit
- Instalação
- Escrever testes
- Preparação e finalização (setup/teardown)
- Asserções
- Suites de teste

### 10.2 Testes Unitários WordPress
- WP_UnitTestCase
- Factory para criar dados
- Fixtures
- Transações de banco de dados
- Isolamento de banco de testes

### 10.3 Testar Plugins
- Bootstrap
- Testes específicos de plugin
- Mock de funções WordPress
- Asserções de query

### 10.4 Testar REST API
- Testes de REST API
- Requisições mock
- Asserções de resposta
- Autenticação em testes

### 10.5 Factories de Dados de Teste
- WP_UnitTest_Factory
- Criar posts, usuários, termos
- Relações entre dados
- Reutilizar métodos de factory

### 10.6 Mocking em WordPress
- Mockery/Prophecy
- Mockar funções externas
- Mockar chamadas de API
- Stub de funções WordPress

### 10.7 Cobertura de Código
- Gerar relatórios de cobertura
- Analisar cobertura
- Identificar lacunas
- Meta de cobertura

### 10.8 Registro de Debug
- error_log()
- Funções de registro de debug
- Plugins de debug (Query Monitor)
- Stack traces

### 10.9 Configuração do Xdebug
- Instalação
- Integração com IDE (PhpStorm, VSCode)
- Breakpoints
- Inspeção de variáveis
- Depuração passo a passo

### 10.10 Modo Debug do WordPress
- WP_DEBUG
- WP_DEBUG_LOG
- WP_DEBUG_DISPLAY
- SCRIPT_DEBUG
- SAVEQUERIES

### 10.11 Deploy e Implantação
- Estratégias de deploy (blue-green, canary)
- Scripts de deploy
- CI/CD (GitHub Actions)
- Checklist pré e pós-deploy
- Monitoramento (Sentry, Query Monitor)

---

## 🌍 FASE 11: Multisite e Internacionalização

### 11.1 Básicos do WordPress Multisite
- Multisite vs site único
- Configuração de rede
- Subdomínio vs subdiretório
- Tabelas de rede

### 11.2 Estrutura do Banco Multisite
- Tabelas comuns
- Tabelas por site (prefixos dinâmicos)
- Usuários vs Sites vs Blogs
- Opções de rede

### 11.3 Comportamento de Plugin em Multisite
- Ativação de plugin por site vs rede
- Ativação em rede
- Configuração por site
- Dados compartilhados vs por site

### 11.4 Multisite API
- switch_to_blog()
- get_blog_option()
- get_sites()
- get_blog_details()
- Loops multisite

### 11.5 Internacionalização (i18n)
- Domínios de texto
- Função __()
- esc_html__()
- Pluralização (_n())

### 11.6 Localização (l10n)
- Arquivos .pot (template)
- Arquivos .po (traduções)
- Arquivos .mo (compilados)
- load_plugin_textdomain()
- load_textdomain()

### 11.7 Locale e Idioma
- get_locale()
- get_user_locale()
- get_blog_language_attributes()
- date_i18n()

### 11.8 Fluxos de Tradução
- Gerar .pot
- Traduzir .po
- Compilar .mo
- Plataformas de gestão de tradução (GlotPress)

### 11.9 Suporte RTL (Direita para Esquerda)
- Folhas de estilo RTL
- wp-content/languages/
- Detecção de idioma RTL
- is_rtl()

---

## 🔐 FASE 12: Segurança e Boas Práticas

### 12.1 Sanitização vs Validação vs Escaping
- Diferenças conceituais
- Quando aplicar cada uma
- Funções específicas

### 12.2 Funções de Sanitização de Entrada
- sanitize_text_field()
- sanitize_email()
- sanitize_url()
- Variantes de wp_kses()
- Sanitização customizada

### 12.3 Funções de Escape de Saída
- esc_html()
- esc_attr()
- esc_url()
- esc_js()
- wp_kses_post()

### 12.4 Prevenção de SQL Injection
- Prepared statements
- $wpdb->prepare()
- Placeholders (%d, %s, %f)
- Nunca confiar em entrada do usuário

### 12.5 Cross-Site Scripting (XSS)
- XSS baseado em DOM
- XSS armazenado
- XSS refletido
- Prevenção por escape

### 12.6 Falsificação de Requisição entre Sites (CSRF)
- Conceito de nonce
- wp_create_nonce()
- wp_verify_nonce()
- wp_nonce_field()
- wp_nonce_url()

### 12.7 Verificação de Capabilities
- current_user_can()
- Capabilities vs roles
- Capabilities customizadas
- Permissões granulares

### 12.8 Segurança em Upload de Arquivos
- Validação de tipo
- Validação de tamanho
- wp_handle_upload()
- Lista permitida de tipos
- Varredura de malware

### 12.9 Auditoria de Segurança de Plugins
- Revisão de código
- Varredura de vulnerabilidades
- Auditoria de dependências (Composer)
- Validação de APIs de terceiros

### 12.10 Boas Práticas de Segurança
- Nunca desabilitar funções de segurança
- Atualizar regularmente
- Minimizar acesso de admin
- Limitação de taxa
- Autenticação em dois fatores
- Headers de segurança
- Forçar HTTPS

---

## 📊 FASE 13: Arquitetura Avançada

### 13.1 Princípios SOLID em WordPress
- Responsabilidade única
- Aberto/fechado
- Substituição de Liskov
- Segregação de interface
- Inversão de dependência

### 13.2 Domain-Driven Design (DDD)
- Entidades
- Objetos de valor
- Repositórios
- Serviços
- Eventos de domínio

### 13.3 Padrão Service Layer
- Separação de responsabilidades
- Isolamento da lógica de negócio
- Reutilização
- Testabilidade

### 13.4 Padrão Repository
- Abstração de dados
- Isolamento de queries
- Múltiplas camadas de persistência
- Manipulação de coleções

### 13.5 Container de Injeção de Dependência
- Service Container
- Auto-wiring
- Carregamento preguiçoso
- Gerenciamento de ciclo de vida

### 13.6 Arquitetura Orientada a Eventos
- Eventos customizados
- Ouvintes de eventos
- Disparo de eventos
- Desacoplamento via eventos

### 13.7 MVC em WordPress
- Models (dados)
- Views (templates)
- Controllers (lógica)
- Integração com hooks

### 13.8 Padrão Adapter para APIs Externas
- Abstrair integrações
- Suporte a múltiplos provedores
- Estratégias de fallback
- Tratamento de erros

### 13.9 Padrão Strategy
- Diferentes estratégias
- Seleção em tempo de execução
- Gateways de pagamento
- Backends de armazenamento

### 13.10 Padrão Factory
- Criação de objetos
- Instanciação complexa
- Gerenciamento de configuração

---

## 🚀 FASE 14: Implantação e DevOps

### 14.1 Ambiente de Desenvolvimento
- Docker para WordPress
- Arquivo Compose
- Container de banco de dados
- Container Nginx/Apache
- PHP-FPM
- Gerenciamento de volumes

### 14.2 Ambiente de Staging
- Replicar produção
- Sincronização de banco
- Sincronização de assets
- Testes
- Testes de performance

### 14.3 Ambiente de Produção
- Configuração de servidor
- Hardening de segurança
- SSL/TLS
- Configuração PHP
- Otimização de banco
- Estratégia de backup

### 14.4 Controle de Versão (Git)
- Padrões .gitignore
- Organização de commits
- Estratégia de branches
- Merge requests / Pull requests

### 14.5 Pipeline CI/CD
- GitHub Actions
- GitLab CI
- Jenkins
- Automação de testes
- Automação de deploy

### 14.6 Testes Automatizados no Pipeline
- Execução PHPUnit
- Verificações de qualidade de código
- Varredura de segurança
- Artefatos de build

### 14.7 Deploy Automatizado
- Deploy em staging
- Deploy em produção
- Migrações de banco
- Atualizações de plugins
- Estratégia de rollback

### 14.8 Monitoramento e Logging
- Logs de aplicação
- Rastreamento de erros (Sentry)
- Monitoramento de performance
- Monitoramento de uptime
- Configuração de alertas

### 14.9 Estratégia de Backup
- Backups de banco
- Backups de arquivos
- Backups incrementais
- Backup externo
- Teste de restauração

### 14.10 Recuperação de Desastres
- Metas RTO/RPO
- Procedimentos de recuperação
- Documentação
- Teste de recuperação

---

## 🔄 FASE 15: Jobs Assíncronos e Processamento em Background

### 15.1 Por Que Jobs Assíncronos?
- Requisições HTTP bloqueantes
- Timeout em operações longas
- Escalabilidade horizontal
- Experiência do usuário
- Quando usar jobs assíncronos

### 15.2 Limitações do WP-Cron
- WP-Cron não é cron real
- Dependência de requisições HTTP
- Problemas com múltiplos servidores
- Falhas silenciosas
- Desabilitar WP-Cron em produção

### 15.3 Action Scheduler (Pronto para Produção)
- Instalação e configuração
- Ações assíncronas (única vez, imediato)
- Ações agendadas (única vez, com atraso)
- Ações recorrentes
- Verificar e cancelar ações
- Monitoramento de ações

### 15.4 Padrões de Fila (Enterprise)
- Fila simples (FIFO)
- Fila de prioridade
- Fila de mensagens mortas (DLQ)
- Estratégias de retry
- Exponential backoff

### 15.5 Receptores de Webhook (Entrada)
- Verificação de assinatura (HMAC-SHA256)
- Chaves de idempotência
- Processamento assíncrono
- Tratamento de erros
- Endpoints REST API

### 15.6 Integração com Docker
- Docker Compose com workers
- Supervisord para gerenciar workers
- Health checks
- Escalar múltiplos workers

### 15.7 Monitoramento em Produção
- Monitor de fila
- Endpoints de health check
- Widgets de dashboard
- Integração com Sentry
- Comandos WP-CLI

### 15.8 Casos de Uso Práticos
- Processamento de pedidos e-commerce
- Pipeline de processamento de mídia
- Importação de CSV em chunks
- Serviço de fila de e-mail

---

## 🎯 FASE 16: Tópicos complementares

### 16.1 Tópicos Avançados de API
- GraphQL para WordPress
- Validação de headers customizados
- Limitação de requisições (rate limiting)
- Versionamento de API
- Tratamento de depreciação
- Documentação de API (OpenAPI/Swagger)

### 16.2 Performance Avançada
- Otimização de velocidade de página
- Otimização de imagens
- Code splitting
- Progressive enhancement
- Core Web Vitals
- Otimização Lighthouse

### 16.3 Ecossistema WordPress
- Integração WooCommerce (padrões avançados)
- ACF (Advanced Custom Fields)
- Integração API Jetpack
- Akismet
- WP Rocket
- Outros plugins populares

### 16.4 Headless WordPress
- REST API como interface principal
- Frontend desacoplado
- Geração de site estático
- Arquitetura Jamstack

### 16.5 Comunidade e Boas Práticas
- Contribuir com o WordPress
- Padrões do repositório de plugins
- Práticas de revisão de código
- Padrões de documentação
- Diretrizes da comunidade

---

## 🧪 FASE 17: Testes em Toda Fase

### 17.1 Por Que Testar em Toda Fase?
- Testes como tema integrado (não isolado)
- Benefícios de testar em cada fase

### 17.2 Setup Básico de Testes
- Instalação PHPUnit
- Estrutura de diretórios
- phpunit.xml e bootstrap.php
- Executar testes

### 17.3 Testando por Fase
- Fase 1: Hook system (mocking, actions, filters)
- Fase 2: REST API (controllers, error handling)
- Fase 3: REST API avançado (schema, permissions)
- Fase 4: Settings API e meta boxes
- Fase 5: CPT e taxonomias
- Fase 6: Shortcodes e blocos Gutenberg
- Fase 7: Comandos WP-CLI
- Fase 8: Performance e cache (transients, queries)
- Fase 12: Segurança (sanitização)
- Fase 13: Arquitetura (SOLID, repository, service layer, DI)
- Fase 15: Async jobs (Action Scheduler, background)

### 17.4 Boas Práticas
- Nomenclatura de testes
- Arrange-Act-Assert
- Testes independentes
- Mocking apropriado
- Cobertura de código

---

## 🗺️ FASE 18: Caminhos de Aprendizado

### 18.1 Por Que Caminhos de Aprendizado?
- Múltiplos caminhos personalizados (não apenas linear)
- Grafo visual de dependências

### 18.2 Caminhos por Perfil
- Caminho 1: APIs Backend (REST + Jobs)
- Caminho 2: Full Stack (Admin + Frontend)
- Caminho 3: DevOps Primeiro
- Caminho 4: Arquitetura Enterprise
- Caminho 5: Desenvolvedor de Plugins
- Caminho 6: Desenvolvedor de Temas

### 18.3 Recomendações por Perfil
- Desenvolvedor Backend, Full Stack, DevOps
- Arquiteto de Software
- Desenvolvedor de Plugins/Temas

### 18.4 Como Usar Este Documento
- Identificar objetivo
- Escolher caminho
- Ajustar conforme necessidade
- Dicas de aprendizado (prática, testar em toda fase, segurança primeiro)

---

## 🚨 FASE 19: Anti-padrões de Segurança

### 19.1 Fase 1: Erros de Segurança Core
- Exibir entrada do usuário diretamente (XSS)
- Queries SQL diretas (SQL injection)
- Confiar em roles sem verificação
- Armazenar dados sensíveis em post meta

### 19.2 Fase 2: Erros de Segurança na REST API
- Sem validação de entrada
- Sem verificação de permissões
- Expor IDs internos
- Registrar dados sensíveis em log

### 19.3 Fase 4 e 5: Settings e CPT
- Settings: sem validação, entrada bruta, credenciais hardcoded
- CPT: sem verificação de capabilities, expor rascunhos, XSS em meta box

### 19.4 Fase 13: Erros de Segurança em Arquitetura
- DI sem validação
- Orientado a eventos sem logging
- Repository sem sanitização

### 19.5 Fase 14: Erros de Segurança em DevOps
- Secrets hardcoded
- Sem SSL/TLS
- Acesso público ao banco de dados

### 19.6 Checklist de Revisão de Código
- Validação de entrada, escape de saída, SQL injection
- Autenticação, dados sensíveis, uploads de arquivo
- REST API, tratamento de erros, secrets, infraestrutura

---

## ⚠️ FASE 20: Boas Práticas de Tratamento de Erros

### 20.1 Princípios Fundamentais
- Falhar rápido, falhar alto
- Nunca engolir exceções
- Usar tipos de erro apropriados

### 20.2 Padrões de Tratamento de Erros
- Try-catch com contexto
- Objetos de resultado de erro
- Handler de erros centralizado

### 20.3 Tratamento de Erros por Contexto
- REST API
- Jobs em background
- Operações de banco de dados
- Operações de arquivo

### 20.4 Logging e Monitoramento
- Logging estruturado
- Integração com ferramentas (Sentry, etc.)

### 20.5 Estratégias de Recuperação de Erros
- Lógica de retry
- Operações de fallback
- Compensação (padrão Saga)

### 20.6 Boas Práticas e Checklist
- FAZER: validar, logar, tipar erros, mensagens claras
- NÃO FAZER: engolir exceções, expor detalhes em produção
- Checklist de tratamento de erros

---

## 📌 Recursos por Fase

**Fase 1:** WordPress.org Core Handbook  
**Fase 2:** REST API Handbook  
**Fase 3:** REST API avançado (controllers, validação, JWT)  
**Fase 4-6:** Plugin Handbook, Settings API, CPT  
**Fase 7:** WP-CLI Handbook (Fundamentos)  
**Fase 8:** Guia de Performance  
**Fase 9:** Manual WP-CLI (Ferramentas)  
**Fase 10:** Manual de Testes Unitários, Debug, Deploy  
**Fase 11:** Multisite e i18n  
**Fase 12:** Segurança e Revisão de Plugins  
**Fase 13:** Padrões de Arquitetura  
**Fase 14:** Implantação e DevOps  
**Fase 15:** Documentação Action Scheduler  
**Fase 16:** Tópicos complementares (API, performance, ecossistema, headless)  
**Fase 17:** Testar em Toda Fase  
**Fase 18:** Caminhos de Aprendizado  
**Fase 19:** Anti-padrões de Segurança  
**Fase 20:** Boas Práticas de Tratamento de Erros  

---

## ✅ Checklist de Masterização

- [ ] Entendo completamente o sistema de hooks (Fase 1)
- [ ] Domino criação de REST API e controllers (Fase 2)
- [ ] Domino REST API avançado (controllers, JWT, validação) (Fase 3)
- [ ] Implemento Settings API e admin completa (Fase 4)
- [ ] Crio CPT e taxonomias avançadas (Fase 5)
- [ ] Desenvolvo shortcodes e blocos (Fase 6)
- [ ] Uso e crio comandos WP-CLI (Fase 7)
- [ ] Otimizo performance com cache (Fase 8)
- [ ] Uso WP-CLI para deploy e automação (Fase 9)
- [ ] Escrevo testes com PHPUnit e faço deploy (Fase 10)
- [ ] Implemento multisite e internacionalização (Fase 11)
- [ ] Aplico boas práticas de segurança (Fase 12)
- [ ] Aplico arquitetura com padrões SOLID e DDD (Fase 13)
- [ ] Faço deploy com CI/CD e Docker (Fase 14)
- [ ] Implemento jobs assíncronos e Action Scheduler (Fase 15)
- [ ] Conheço tópicos complementares (API, headless, ecossistema) (Fase 16)
- [ ] Aplico testes em toda fase do desenvolvimento (Fase 17)
- [ ] Sigo um caminho de aprendizado adequado ao meu perfil (Fase 18)
- [ ] Evito anti-padrões de segurança (Fase 19)
- [ ] Aplico boas práticas de tratamento de erros (Fase 20)

---

**Versão:** 2.0  
**Status:** Completo e atualizado  
**Última atualização:** Fevereiro 2026 (Fases 1-20, índice completo)  
**Próxima revisão:** Q2 2026
