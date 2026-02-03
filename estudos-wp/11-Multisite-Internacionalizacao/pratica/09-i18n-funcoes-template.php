<?php
/**
 * REFERÊNCIA RÁPIDA – __(), _e(), esc_html__() em template
 *
 * __($text, $domain): retorna traduzido; _e($text, $domain): echo; esc_html__() = esc_html(__()).
 * load_plugin_textdomain($domain, false, dir); Text domain = slug do plugin.
 * Fonte: 011-WordPress-Fase-11-Multisite-Internacionalizacao.md
 *
 * @package EstudosWP
 * @subpackage 11-Multisite-Internacionalizacao
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    load_plugin_textdomain('estudos-wp', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// No template ou plugin:
// echo esc_html(__('Título da página', 'estudos-wp'));
// _e('Texto que será exibido', 'estudos-wp');
// echo esc_attr(__('Valor do atributo', 'estudos-wp'));

function estudos_wp_traducao_exemplo() {
    return [
        'titulo' => __('Título', 'estudos-wp'),
        'descricao' => __('Descrição aqui.', 'estudos-wp'),
        'plural' => _n('%s item', '%s itens', 2, 'estudos-wp'),
    ];
}
