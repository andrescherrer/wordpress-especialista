# Referência rápida – Testes em Toda Fase

Uma página. Use Ctrl+F. Fonte: **017-WordPress-Fase-17-Testes-Em-Toda-Fase.md**.

---

## Por que testing throughout

- Aprender testes no contexto de cada fase (hooks, REST, CPT, etc.), não como tema isolado.
- Código testável desde o início; refatoração segura; testes como documentação de comportamento.

---

## Setup

- **PHPUnit:** `composer require --dev phpunit/phpunit ^11.0`
- **Estrutura:** tests/Unit, tests/Integration, tests/bootstrap.php, phpunit.xml (bootstrap, testsuites).
- **Bootstrap WordPress:** Definir WP_TESTS_DIR; carregar includes/functions.php; tests_add_filter('muplugins_loaded', ...) para carregar plugin; includes/bootstrap.php.
- **Comandos:** `./vendor/bin/phpunit`, `./vendor/bin/phpunit tests/Unit`, `--coverage-html coverage/`

---

## Testando hooks

- **Actions:** add_action → do_action → assert que callback foi chamado (variável flag ou contador).
- **Ordem por prioridade:** add_action com prioridades 5, 10, 20 → do_action → assert ordem do array.
- **Remoção:** add_action → remove_action → do_action → assert que não disparou.
- **Filters:** add_filter → apply_filters → assert valor retornado; múltiplos filters em cascata.
- **Limpeza:** tearDown com remove_all_actions/remove_all_filters para não vazar entre testes.
- **WP_UnitTestCase:** Necessário quando usar add_action/do_action (WordPress carregado).

---

## Testando REST API

- **Setup:** setUp: rest_get_server(), do_action('rest_api_init').
- **Request/Response:** WP_REST_Request('GET'|'POST', "/namespace/endpoint"); set_body_params(); set_param(); $response = $server->dispatch($request); $response->get_status(); $response->get_data(); $response->get_headers().
- **Cenários:** 200/201 para sucesso; 400 validação; 401 não autenticado; 403 sem permissão; 404 recurso inexistente.
- **Factory:** $this->factory->post->create(), $this->factory->user->create(['role' => 'administrator']); wp_set_current_user($user_id).
- **Validação:** body com dados inválidos (ex.: title vazio) → assert 400.
- **Sanitização:** enviar HTML/script → assert que resposta não contém script.
- **Capabilities:** usuário subscriber → POST/DELETE → assert 403.

---

## Testando CPT, Settings, Shortcodes

- **CPT:** get_post_types(); assertArrayHasKey('product', $post_types). Criar post com factory (post_type => 'product'); verificar meta e conteúdo.
- **Settings:** register_setting; get_registered_settings() ou get_option após sanitize; testar valores válidos e inválidos.
- **Shortcodes:** apply_filters('the_content', '[shortcode attr="x"]') ou do_shortcode(); assertStringContainsString esperado.
- **Meta boxes:** Simular save_post; $_POST; do_action('save_post', $post_id); assert meta salva.

---

## Testando arquitetura

- **SOLID/Repository:** assert method_exists(validator, 'validate'); assert method_exists(repository, 'save'); service recebe dependências injetadas.
- **Mock:** $mock = $this->createMock(PostRepository::class); $mock->method('find')->willReturn($post); service usa mock; assert resultado.
- **Service layer:** mock repository, validator, notifier; expects($this->once())->method('create'); service->createPost(); assert chamadas e retorno.
- **DI Container:** container->bind(); container->make(); assertInstanceOf; singleton: assertSame(instance1, instance2).

---

## Testando Action Scheduler

- **Agendado:** as_enqueue_async_action('hook', $args); assertTrue(as_has_scheduled_action('hook', $args)).
- **Cancelar:** as_unschedule_action('hook', $args); assertFalse(as_has_scheduled_action('hook')).
- **Executar:** add_action('hook', callback); as_enqueue_async_action('hook'); do_action('action_scheduler_run_queue'); assert que callback foi chamado.
- **Argumentos:** enqueue com ['a','b']; no handler capturar args; assert igual.
- Requer Action Scheduler instalado; WP_UnitTestCase para WordPress carregado.

---

## Boas práticas

- **Nomenclatura:** test_<o_que>_<condição>_<resultado_esperado> (ex.: test_user_cannot_access_admin_without_permission).
- **Arrange-Act-Assert:** Arrange (dados, mocks), Act (chamar código), Assert (verificar).
- **Um assertion por teste** quando possível; múltiplas assertions para um único conceito é aceitável.
- **Testes independentes:** setUp/tearDown limpos; não depender de ordem de execução.
- **Mocking:** mockar dependências externas (API, DB quando unit); não mockar tudo.
- **Cobertura:** --coverage-html; priorizar código crítico; alvo 80%+ onde fizer sentido.
- **Regressão:** comentar bug/issue no docblock; teste que garante que o bug não volta.
