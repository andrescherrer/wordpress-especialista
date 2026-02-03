<?php
/**
 * REFERÊNCIA RÁPIDA – Block mínimo (save, edit, attributes)
 *
 * register_block_type(name, [render_callback => callback] ou [block.json]).
 * Bloco estático: save retorna HTML; edit retorna elemento (React ou wp.element).
 * attributes: definidos no register_block_type; disponíveis em $attributes no callback.
 *
 * Fonte: 006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    if (!function_exists('register_block_type')) {
        return;
    }
    register_block_type('estudos-wp/block-minimo', [
        'api_version'     => 2,
        'attributes'      => [
            'mensagem' => [
                'type'    => 'string',
                'default' => 'Olá, bloco mínimo.',
            ],
        ],
        'render_callback' => function ($attributes) {
            $msg = isset($attributes['mensagem']) ? $attributes['mensagem'] : 'Olá, bloco mínimo.';
            return '<div class="wp-block-estudos-wp-block-minimo">' . esc_html($msg) . '</div>';
        },
    ]);
});

// Para bloco com edit (JavaScript): usar block.json e script + edit.js; aqui só render_callback (bloco dinâmico em PHP).
