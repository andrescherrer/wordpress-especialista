<?php
/**
 * Sidebar e widgets: register_sidebar, dynamic_sidebar.
 * Tema com uma sidebar e um footer com widgets.
 *
 * @package EstudosWP
 * @subpackage 05b-Temas-Classicos
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Sidebar', 'estudos-wp'),
        'id'            => 'sidebar-1',
        'description'  => __('Widgets da sidebar principal.', 'estudos-wp'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Footer', 'estudos-wp'),
        'id'            => 'footer-1',
        'description'  => __('Widgets do rodape.', 'estudos-wp'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});
