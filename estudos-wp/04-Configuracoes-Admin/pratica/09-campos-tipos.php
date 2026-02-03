<?php
/**
 * REFERÊNCIA RÁPIDA – Tipos de campo em add_settings_field
 *
 * text, number, checkbox, textarea, select; callback recebe $args (label_for, name, value).
 * Sanitize: sanitize_text_field, absint, sanitize_email, wp_kses_post (HTML).
 * Fonte: 004-WordPress-Fase-4-Configuracoes-Admin.md
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {
    register_setting('estudos_wp_campos_group', 'estudos_wp_campo_texto', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('estudos_wp_campos_group', 'estudos_wp_campo_numero', ['sanitize_callback' => 'absint']);
    register_setting('estudos_wp_campos_group', 'estudos_wp_campo_checkbox', ['sanitize_callback' => function ($v) { return $v ? '1' : '0'; }]);
    register_setting('estudos_wp_campos_group', 'estudos_wp_campo_textarea', ['sanitize_callback' => 'wp_kses_post']);
    register_setting('estudos_wp_campos_group', 'estudos_wp_campo_select', ['sanitize_callback' => 'sanitize_text_field']);

    add_settings_section('campos_sec', 'Exemplo de tipos de campo', '__return_false', 'estudos-wp-campos');

    add_settings_field('estudos_wp_campo_texto', 'Texto', function () {
        $v = get_option('estudos_wp_campo_texto', '');
        echo '<input type="text" name="estudos_wp_campo_texto" value="' . esc_attr($v) . '" class="regular-text">';
    }, 'estudos-wp-campos', 'campos_sec');

    add_settings_field('estudos_wp_campo_numero', 'Número', function () {
        $v = get_option('estudos_wp_campo_numero', 0);
        echo '<input type="number" name="estudos_wp_campo_numero" value="' . esc_attr($v) . '" min="0" class="small-text">';
    }, 'estudos-wp-campos', 'campos_sec');

    add_settings_field('estudos_wp_campo_checkbox', 'Checkbox', function () {
        $v = get_option('estudos_wp_campo_checkbox', '0');
        echo '<input type="checkbox" name="estudos_wp_campo_checkbox" value="1" ' . checked($v, '1', false) . '>';
    }, 'estudos-wp-campos', 'campos_sec');

    add_settings_field('estudos_wp_campo_textarea', 'Textarea', function () {
        $v = get_option('estudos_wp_campo_textarea', '');
        echo '<textarea name="estudos_wp_campo_textarea" rows="4" class="large-text">' . esc_textarea($v) . '</textarea>';
    }, 'estudos-wp-campos', 'campos_sec');

    add_settings_field('estudos_wp_campo_select', 'Select', function () {
        $v = get_option('estudos_wp_campo_select', 'a');
        echo '<select name="estudos_wp_campo_select">';
        foreach (['a' => 'Opção A', 'b' => 'Opção B', 'c' => 'Opção C'] as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($v, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }, 'estudos-wp-campos', 'campos_sec');
});

add_action('admin_menu', function () {
    add_options_page('Campos Tipos', 'Campos Tipos', 'manage_options', 'estudos-wp-campos', function () {
        echo '<div class="wrap"><form method="post" action="options.php">';
        settings_fields('estudos_wp_campos_group');
        do_settings_sections('estudos-wp-campos');
        submit_button();
        echo '</form></div>';
    });
});
