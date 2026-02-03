<?php
/**
 * REFERÊNCIA RÁPIDA – Customizer
 *
 * wp_customize; add_section, add_setting, add_control;
 * tema com opção de cor e logo.
 *
 * @package EstudosWP
 * @subpackage 05b-Temas-Classicos
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('customize_register', function ($wp_customize) {
    // Seção
    $wp_customize->add_section('estudos_wp_theme_options', [
        'title'    => __('Opções do tema', 'estudos-wp'),
        'priority' => 30,
    ]);

    // Cor de destaque
    $wp_customize->add_setting('estudos_wp_accent_color', [
        'default'           => '#0073aa',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'estudos_wp_accent_color', [
        'label'   => __('Cor de destaque', 'estudos-wp'),
        'section' => 'estudos_wp_theme_options',
    ]));

    // Logo (upload)
    $wp_customize->add_setting('estudos_wp_logo', [
        'default'           => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'estudos_wp_logo', [
        'label'   => __('Logo', 'estudos-wp'),
        'section' => 'estudos_wp_theme_options',
        'mime_type'=> 'image',
    ]));
});

// No template: usar get_theme_mod('estudos_wp_accent_color') e wp_get_attachment_image(get_theme_mod('estudos_wp_logo'), 'full') para o logo.
