<?php
/**
 * REFERÊNCIA RÁPIDA – Admin AJAX
 *
 * wp_ajax_my_action (logado), wp_ajax_nopriv_my_action (não logado).
 * wp_send_json_success($data) / wp_send_json_error($data); envia e encerra.
 * Nonce: wp_create_nonce('my_action'); no front; check_ajax_referer('my_action') no handler.
 *
 * Fonte: 004-WordPress-Fase-4-Configuracoes-Admin.md
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_estudos_wp_ajax_exemplo', function () {
    check_ajax_referer('estudos_wp_ajax_exemplo');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Sem permissão']);
    }
    $valor = isset($_POST['valor']) ? sanitize_text_field($_POST['valor']) : '';
    if (empty($valor)) {
        wp_send_json_error(['message' => 'Valor obrigatório']);
    }
    update_option('estudos_wp_ajax_ultimo', $valor);
    wp_send_json_success(['saved' => $valor]);
});

// Enfileirar script no admin que envia a requisição
add_action('admin_footer', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'options-general') {
        return;
    }
    ?>
    <script>
    (function() {
        var nonce = '<?php echo esc_js(wp_create_nonce('estudos_wp_ajax_exemplo')); ?>';
        // Exemplo: jQuery.post(ajaxurl, { action: 'estudos_wp_ajax_exemplo', nonce: nonce, valor: 'teste' }, function(r) { console.log(r); });
    })();
    </script>
    <?php
});
