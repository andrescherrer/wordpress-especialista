<?php
/**
 * REFERÊNCIA RÁPIDA – Transações com $wpdb
 *
 * $wpdb->query('START TRANSACTION');
 * $wpdb->query('COMMIT');   // sucesso
 * $wpdb->query('ROLLBACK'); // em catch
 * Não misturar com wp_insert_post() no meio da transação (usa sua própria conexão).
 *
 * Fonte: 001-WordPress-Fase-1-Fundamentos-Core.md
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Exemplo: transferir meta de um post para outro (transação).
 * Se qualquer passo falhar, desfaz tudo.
 *
 * @param int $from_id Post de origem.
 * @param int $to_id   Post de destino.
 * @return bool Sucesso.
 */
function estudos_wp_transferir_meta_transacao($from_id, $to_id) {
    global $wpdb;

    $from_id = absint($from_id);
    $to_id   = absint($to_id);
    if (!$from_id || !$to_id) {
        return false;
    }

    $wpdb->query('START TRANSACTION');

    try {
        $metas = $wpdb->get_results($wpdb->prepare(
            "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d",
            $from_id
        ));

        foreach ($metas as $row) {
            $r = $wpdb->insert(
                $wpdb->postmeta,
                [
                    'post_id'    => $to_id,
                    'meta_key'   => $row->meta_key,
                    'meta_value' => $row->meta_value,
                ],
                ['%d', '%s', '%s']
            );
            if ($r === false) {
                throw new Exception('Falha ao inserir meta');
            }
        }

        $deleted = $wpdb->delete($wpdb->postmeta, ['post_id' => $from_id], ['%d']);
        if ($deleted === false) {
            throw new Exception('Falha ao remover meta antiga');
        }

        $wpdb->query('COMMIT');
        return true;
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('estudos_wp_transferir_meta_transacao: ' . $e->getMessage());
        }
        return false;
    }
}
