<?php
/**
 * REFERÊNCIA RÁPIDA – Exclusão de dados (right to be forgotten)
 *
 * Registrar apagador em wp_privacy_personal_data_erasers.
 * Anonimizar ou apagar dados do usuário (user_meta, post_meta); log do que foi removido;
 * integração com Ferramentas > Apagar dados pessoais.
 *
 * @package EstudosWP
 * @subpackage 12c-Privacidade
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('wp_privacy_personal_data_erasers', function ($erasers) {
    $erasers['estudos-wp-dados'] = [
        'eraser_friendly_name' => __('Dados do plugin Estudos WP', 'estudos-wp'),
        'callback'             => 'estudos_wp_privacy_erase_personal_data',
    ];
    return $erasers;
});

function estudos_wp_privacy_erase_personal_data($email_address, $page = 1) {
    $user = get_user_by('email', $email_address);
    $messages = [];

    if (!$user) {
        return ['items_removed' => 0, 'items_retained' => 0, 'messages' => [], 'done' => true];
    }

    $items_removed = 0;
    $meta_keys = ['meu_plugin_preferencia', 'meu_plugin_ultimo_acesso'];

    foreach ($meta_keys as $key) {
        if (delete_user_meta($user->ID, $key)) {
            $items_removed++;
        }
    }

    // Se o plugin armazena dados em post_meta (ex.: autor = user_id), anonimizar ou apagar
    // Exemplo: $posts = get_posts(['author' => $user->ID, 'post_type' => 'meu_cpt']);
    // foreach ($posts as $p) { delete_post_meta($p->ID, 'dado_sensivel'); }

    return [
        'items_removed'  => $items_removed,
        'items_retained' => 0,
        'messages'       => $messages,
        'done'           => true,
    ];
}
