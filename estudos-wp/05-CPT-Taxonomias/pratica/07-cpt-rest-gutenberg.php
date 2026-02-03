<?php
/**
 * REFERÊNCIA RÁPIDA – CPT com suporte a Gutenberg e REST
 *
 * show_in_rest => true; rest_base => 'produtos'; rest_controller_class => WP_REST_Posts_Controller.
 * Suportes: 'editor' (Gutenberg), 'title', 'thumbnail', 'excerpt', 'custom-fields'.
 * Fonte: 005-WordPress-Fase-5-CPT-Taxonomias.md
 *
 * @package EstudosWP
 * @subpackage 05-CPT-Taxonomias
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    register_post_type('produto', [
        'labels'              => [
            'name'          => 'Produtos',
            'singular_name' => 'Produto',
            'add_new_item'  => 'Adicionar Produto',
            'edit_item'     => 'Editar Produto',
        ],
        'public'               => true,
        'has_archive'         => true,
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest'         => true,
        'rest_base'           => 'produtos',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'capability_type'     => 'post',
    ]);
});

// Com show_in_rest => true o CPT aparece no editor de blocos (Gutenberg) e em /wp-json/wp/v2/produtos
