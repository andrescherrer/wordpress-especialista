<?php
/**
 * REFERÊNCIA RÁPIDA – Tabs em página de settings
 *
 * Abas: links com ?page=xxx&tab=geral|avancado; $_GET['tab']; add_settings_section por aba.
 * Ou: nav-tab-wrapper + div por aba; JavaScript para mostrar/ocultar.
 * Fonte: 004-WordPress-Fase-4-Configuracoes-Admin.md
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function () {
    add_options_page(
        'Configurações com Abas',
        'Config com Abas',
        'manage_options',
        'estudos-wp-tabs',
        'estudos_wp_render_tabs_page'
    );
});

add_action('admin_init', function () {
    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'geral';
    if ($tab === 'geral') {
        register_setting('estudos_wp_tabs_geral', 'estudos_wp_tabs_nome', [
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        add_settings_section('geral_sec', 'Aba Geral', '__return_false', 'estudos-wp-tabs&tab=geral');
        add_settings_field('estudos_wp_tabs_nome', 'Nome', function () {
            $v = get_option('estudos_wp_tabs_nome', '');
            echo '<input type="text" name="estudos_wp_tabs_nome" value="' . esc_attr($v) . '" class="regular-text">';
        }, 'estudos-wp-tabs&tab=geral', 'geral_sec');
    }
    if ($tab === 'avancado') {
        register_setting('estudos_wp_tabs_avancado', 'estudos_wp_tabs_debug', [
            'sanitize_callback' => function ($v) { return $v ? '1' : '0'; },
        ]);
        add_settings_section('avancado_sec', 'Aba Avançado', '__return_false', 'estudos-wp-tabs&tab=avancado');
        add_settings_field('estudos_wp_tabs_debug', 'Modo debug', function () {
            $v = get_option('estudos_wp_tabs_debug', '0');
            echo '<input type="checkbox" name="estudos_wp_tabs_debug" value="1" ' . checked($v, '1', false) . '>';
        }, 'estudos-wp-tabs&tab=avancado', 'avancado_sec');
    }
});

function estudos_wp_render_tabs_page() {
    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'geral';
    $page = 'estudos-wp-tabs';
    ?>
    <div class="wrap">
        <h1>Configurações com Abas</h1>
        <nav class="nav-tab-wrapper">
            <a href="<?php echo esc_url(admin_url('options-general.php?page=' . $page . '&tab=geral')); ?>"
               class="nav-tab <?php echo $tab === 'geral' ? 'nav-tab-active' : ''; ?>">Geral</a>
            <a href="<?php echo esc_url(admin_url('options-general.php?page=' . $page . '&tab=avancado')); ?>"
               class="nav-tab <?php echo $tab === 'avancado' ? 'nav-tab-active' : ''; ?>">Avançado</a>
        </nav>
        <form method="post" action="options.php">
            <?php
            if ($tab === 'geral') {
                settings_fields('estudos_wp_tabs_geral');
                do_settings_sections('estudos-wp-tabs&tab=geral');
            } else {
                settings_fields('estudos_wp_tabs_avancado');
                do_settings_sections('estudos-wp-tabs&tab=avancado');
            }
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
