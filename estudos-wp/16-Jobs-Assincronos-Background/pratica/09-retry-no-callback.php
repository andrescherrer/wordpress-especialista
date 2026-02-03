<?php
/**
 * REFERÊNCIA RÁPIDA – Retry manual dentro do callback (tentativas, backoff)
 *
 * Loop com N tentativas; sleep(2 ** $tentativa) entre tentativas; só retry para erros temporários (timeout, 5xx).
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md
 *
 * @package EstudosWP
 * @subpackage 16-Jobs-Assincronos-Background
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('estudos_wp_sync_external', function ($resource_id) {
    $max_tentativas = 3;
    $ultimo_erro = null;
    for ($tentativa = 0; $tentativa < $max_tentativas; $tentativa++) {
        $res = wp_remote_post(
            'https://api.example.com/sync',
            [
                'timeout' => 15,
                'body'    => ['id' => $resource_id],
            ]
        );
        if (!is_wp_error($res)) {
            $code = wp_remote_retrieve_response_code($res);
            if ($code >= 200 && $code < 300) {
                return;
            }
            $ultimo_erro = $code;
            if ($code >= 400 && $code < 500 && $code != 429) {
                break; // não retry
            }
        } else {
            $ultimo_erro = $res->get_error_message();
        }
        if ($tentativa < $max_tentativas - 1) {
            sleep(2 ** $tentativa);
        }
    }
    error_log('estudos_wp_sync_external failed after ' . $max_tentativas . ' attempts: ' . print_r($ultimo_erro, true));
});
