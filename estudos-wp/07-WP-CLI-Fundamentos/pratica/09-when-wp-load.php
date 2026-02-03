<?php
/**
 * REFERÊNCIA RÁPIDA – When (registrar comando só quando WP carregado)
 *
 * Comandos que usam get_option(), get_post() etc. devem rodar após WordPress carregar.
 * WP_CLI::add_command(..., [... 'when' => 'wp_loaded']); ou registrar no hook after_wp_load.
 * Mensagem amigável: WP_CLI::error('Execute a partir da raiz do WordPress.') se ABSPATH não definido.
 *
 * Fonte: 007-WordPress-Fase-7-WP-CLI-Fundamentos.md
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

WP_CLI::add_command('estudos-wp quando', function ($args, $assoc_args) {
    if (!defined('ABSPATH')) {
        WP_CLI::error('WordPress não está carregado. Execute a partir da raiz da instalação WP.');
    }
    $blogname = get_option('blogname');
    WP_CLI::log('Blog name: ' . $blogname);
    WP_CLI::success('Comando executado com WP carregado.');
}, [
    'shortdesc' => 'Exemplo que exige WordPress carregado',
    'when'      => 'after_wp_load',
]);

// Com 'when' => 'after_wp_load' o callback só roda após wp-load.php; get_option() e outras funções WP estão disponíveis.
