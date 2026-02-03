<?php
/**
 * REFERÊNCIA RÁPIDA – Exportação de dados do usuário (GDPR/LGPD)
 *
 * Registrar exportador em wp_privacy_personal_data_exporters.
 * Coletar dados do usuário (user_meta, post_meta, options); gerar itens de exportação;
 * o core gera JSON/ZIP e oferece em Ferramentas > Exportar dados pessoais.
 *
 * @package EstudosWP
 * @subpackage 12c-Privacidade
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('wp_privacy_personal_data_exporters', function ($exporters) {
    $exporters['estudos-wp-dados'] = [
        'exporter_friendly_name' => __('Dados do plugin Estudos WP', 'estudos-wp'),
        'callback'              => 'estudos_wp_privacy_export_personal_data',
    ];
    return $exporters;
});

function estudos_wp_privacy_export_personal_data($email_address, $page = 1) {
    $user = get_user_by('email', $email_address);
    if (!$user) {
        return ['data' => [], 'done' => true];
    }

    $export_items = [];
    $group_id     = 'estudos-wp';
    $group_label  = __('Dados do plugin', 'estudos-wp');

    // Exemplo: user_meta do plugin
    $meta_keys = ['meu_plugin_preferencia', 'meu_plugin_ultimo_acesso'];
    foreach ($meta_keys as $key) {
        $value = get_user_meta($user->ID, $key, true);
        if ($value !== '') {
            $export_items[] = [
                'group_id'    => $group_id,
                'group_label' => $group_label,
                'item_id'     => $key,
                'data'        => [
                    ['name' => $key, 'value' => $value],
                ],
            ];
        }
    }

    return [
        'data' => $export_items,
        'done' => true,
    ];
}
