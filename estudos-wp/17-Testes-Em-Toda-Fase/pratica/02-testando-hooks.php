<?php
/**
 * REFERÊNCIA RÁPIDA – Testando hooks (actions e filters)
 *
 * Actions: add_action → do_action → assert flag/count.
 * Ordem: prioridades 5, 10, 20 → assert ordem do array.
 * Remoção: remove_action → do_action → assert não disparou.
 * Filters: add_filter → apply_filters → assert valor.
 * tearDown: remove_all_actions / remove_all_filters.
 *
 * Requer WordPress carregado (WP_UnitTestCase) para add_action/do_action.
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md (Fase 1)
 */

// Estender WP_UnitTestCase quando rodar com WordPress test suite (WP_TESTS_DIR).
// Aqui: PHPUnit\Framework\TestCase para referência; em projeto real use WP_UnitTestCase.

use PHPUnit\Framework\TestCase;

class HookSystemTest extends TestCase
{
    private bool $hook_fired = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hook_fired = false;
    }

    protected function tearDown(): void
    {
        remove_all_actions('test_action');
        remove_all_filters('test_filter');
        parent::tearDown();
    }

    /**
     * Verificar se action é disparada.
     * Em ambiente real: estender WP_UnitTestCase para ter add_action/do_action.
     */
    public function test_action_fires_when_triggered(): void
    {
        if (! function_exists('add_action')) {
            $this->markTestSkipped('WordPress não carregado (use WP_UnitTestCase e WP_TESTS_DIR)');
        }
        add_action('test_action', function () {
            $this->hook_fired = true;
        });
        do_action('test_action');
        $this->assertTrue($this->hook_fired);
    }

    /**
     * Verificar ordem de execução por prioridade (menor número = antes).
     */
    public function test_hooks_execute_in_priority_order(): void
    {
        if (! function_exists('add_action')) {
            $this->markTestSkipped('WordPress não carregado');
        }
        $order = [];
        add_action('sequence_test', function () use (&$order) {
            $order[] = 'first';
        }, 10);
        add_action('sequence_test', function () use (&$order) {
            $order[] = 'second';
        }, 20);
        add_action('sequence_test', function () use (&$order) {
            $order[] = 'third';
        }, 5);
        do_action('sequence_test');
        $this->assertEquals(['third', 'first', 'second'], $order);
    }

    /**
     * Verificar que hook pode ser removido.
     */
    public function test_hook_can_be_removed(): void
    {
        if (! function_exists('add_action')) {
            $this->markTestSkipped('WordPress não carregado');
        }
        $callback = function () {
            $this->hook_fired = true;
        };
        add_action('test_action', $callback, 10);
        remove_action('test_action', $callback, 10);
        do_action('test_action');
        $this->assertFalse($this->hook_fired);
    }
}

// ========== Filtros ==========

class FilterSystemTest extends TestCase
{
    protected function tearDown(): void
    {
        remove_all_filters('test_filter');
        parent::tearDown();
    }

    public function test_filter_modifies_value(): void
    {
        if (! function_exists('add_filter')) {
            $this->markTestSkipped('WordPress não carregado');
        }
        add_filter('test_filter', function ($value) {
            return $value . '_modified';
        });
        $result = apply_filters('test_filter', 'original');
        $this->assertEquals('original_modified', $result);
    }

    public function test_multiple_filters_in_cascade(): void
    {
        if (! function_exists('add_filter')) {
            $this->markTestSkipped('WordPress não carregado');
        }
        add_filter('test_filter', function ($v) {
            return $v . '_first';
        }, 10);
        add_filter('test_filter', function ($v) {
            return $v . '_second';
        }, 20);
        $result = apply_filters('test_filter', 'original');
        $this->assertEquals('original_first_second', $result);
    }
}
