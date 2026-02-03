<?php
/**
 * REFERÊNCIA RÁPIDA – Testar hooks (action e filter)
 *
 * Action: add_action → do_action → assert que callback foi chamado (variável flag ou contador).
 * Filter: add_filter → apply_filters → assert valor retornado.
 * tearDown: remove_all_actions('hook'); remove_all_filters('hook'); para não vazar entre testes.
 *
 * Fonte: 010-WordPress-Fase-10-Testes-Debug-Implantacao.md
 *
 * @package EstudosWP
 * @subpackage 10-Testes-Debug-Implantacao
 */

if (!defined('ABSPATH')) {
    exit;
}

// Exemplo de teste (requer PHPUnit e, para do_action, WordPress carregado ou mock)
// class Test_Hooks extends WP_UnitTestCase {
//     public function tearDown(): void {
//         remove_all_actions('estudos_wp_after_save');
//         remove_all_filters('estudos_wp_title');
//         parent::tearDown();
//     }
//
//     public function test_action_dispara() {
//         $chamado = false;
//         add_action('estudos_wp_after_save', function () use (&$chamado) { $chamado = true; });
//         do_action('estudos_wp_after_save');
//         $this->assertTrue($chamado);
//     }
//
//     public function test_filter_altera_valor() {
//         add_filter('estudos_wp_title', function ($t) { return $t . ' [editado]'; });
//         $result = apply_filters('estudos_wp_title', 'Título');
//         $this->assertStringContainsString('[editado]', $result);
//     }
// }

$GLOBALS['estudos_wp_hook_chamado'] = false;
add_action('estudos_wp_after_save', function () {
    $GLOBALS['estudos_wp_hook_chamado'] = true;
});
