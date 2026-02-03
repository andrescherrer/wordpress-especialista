# Prática – Como usar (Testes em Toda Fase)

1. **Setup:** Instale PHPUnit via Composer; crie phpunit.xml (bootstrap, testsuites Unit/Integration); bootstrap carrega WordPress quando precisar de WP_UnitTestCase (defina WP_TESTS_DIR).
2. **Hooks:** Use WP_UnitTestCase para ter add_action/do_action; teste se action dispara, ordem de prioridade, remoção; filters: apply_filters e valor retornado; limpe remove_all_actions no tearDown.
3. **REST:** rest_get_server(); WP_REST_Request; dispatch; assert status (200, 201, 400, 401, 403, 404) e get_data(); use factory para posts/usuários; teste validação, sanitização e capabilities.
4. **CPT/Settings/Shortcodes:** get_post_types(); factory->post->create(['post_type' => 'cpt']); do_shortcode ou the_content com shortcode; get_option após registrar settings.
5. **Arquitetura:** Mock de Repository/Validator com createMock e willReturn; testar Service com expects($this->once()); testar Container bind/make e singleton.
6. **Action Scheduler:** as_has_scheduled_action após enqueue; as_unschedule_action e assertFalse(has_scheduled); do_action('action_scheduler_run_queue') para executar e assert callback.
7. **Boas práticas:** Nome descritivo; AAA; testes independentes; mock só o necessário; coverage em código crítico.

**Arquivos 08–10:** teste REST completo (08), repository mock (09), data provider validação (10).

**Teoria rápida:** [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).
