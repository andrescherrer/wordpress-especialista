<?php
/**
 * Menus: register_nav_menus, wp_nav_menu.
 * Location no tema (header, footer).
 *
 * @package EstudosWP
 * @subpackage 05b-Temas-Classicos
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    register_nav_menus([
        'primary' => __('Menu principal', 'estudos-wp'),
        'footer'  => __('Menu rodape', 'estudos-wp'),
    ]);
});
