<?php
/**
 * REFERÊNCIA RÁPIDA – Enqueue condicional e defer
 *
 * wp_enqueue_script/style só na página/condição necessária; wp_register_script depois wp_enqueue_script com dependências.
 * wp_script_add_data('handle', 'defer', true); wp_script_add_data('handle', 'async', true).
 * Fonte: 008-WordPress-Fase-8-Performance-Cache-Otimizacao.md
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    // Só na single de post
    if (is_singular('post')) {
        wp_enqueue_script(
            'estudos-wp-single',
            get_template_directory_uri() . '/js/single.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_script_add_data('estudos-wp-single', 'defer', true);
    }
    // Só na página de contato (slug)
    if (is_page('contato')) {
        wp_enqueue_script('estudos-wp-contato', get_template_directory_uri() . '/js/contato.js', [], '1.0', true);
    }
});

// Para estilo: wp_enqueue_style('handle', $src, $deps, $ver); carregar só onde precisar (is_admin(), is_page(...), etc.).
