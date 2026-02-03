<?php
/**
 * REFERÊNCIA RÁPIDA – Progress bar em comando WP-CLI
 *
 * WP_CLI\Utils\make_progress_bar($message, $count); $bar->tick(); $bar->finish();
 * Ou: $bar = \WP_CLI\Utils\make_progress_bar('Processando', $total); loop { $bar->tick(); } $bar->finish();
 *
 * Fonte: 009-WordPress-Fase-9-WP-CLI-Ferramentas.md
 *
 * @package EstudosWP
 * @subpackage 09-WP-CLI-Ferramentas
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

WP_CLI::add_command('estudos-wp progress', function ($args, $assoc_args) {
    $total = isset($assoc_args['total']) ? absint($assoc_args['total']) : 10;
    $bar = \WP_CLI\Utils\make_progress_bar('Processando itens', $total);
    for ($i = 0; $i < $total; $i++) {
        usleep(100000); // 0.1s
        $bar->tick();
    }
    $bar->finish();
    WP_CLI::success("Concluído: {$total} itens.");
}, [
    'shortdesc' => 'Exemplo de progress bar',
    'synopsis'  => [['type' => 'assoc', 'name' => 'total', 'optional' => true, 'default' => 10]],
]);
