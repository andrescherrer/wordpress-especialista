<?php
/**
 * REFERÊNCIA RÁPIDA – Action Scheduler (básico)
 *
 * Async: as_enqueue_async_action('hook', [$args], 'group')
 * Single: as_schedule_single_action(timestamp, 'hook', [$args], 'group')
 * Recurring: as_schedule_recurring_action(start, interval_seconds, 'hook', [$args], 'group')
 * Verificar: as_has_scheduled_action('hook', $args, 'group'); Cancelar: as_unschedule_action(...)
 *
 * Requer: woocommerce/action-scheduler (Composer ou plugin)
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md
 */

if (! function_exists('as_enqueue_async_action')) {
    return;
}

// ========== 1. Async (one-time, assim que possível) ==========

as_enqueue_async_action(
    'meu_plugin_process_post',
    ['post_id' => 123],
    'meu_plugin_group'
);

add_action('meu_plugin_process_post', function ($post_id) {
    // Processar em background (não bloqueia a requisição)
    if ($post_id && get_post_status($post_id)) {
        // process_high_res_image($post_id);
        error_log("Processed post {$post_id} in background");
    }
}, 10, 1);

// ========== 2. Single (com delay) ==========

as_schedule_single_action(
    time() + (30 * MINUTE_IN_SECONDS),
    'meu_plugin_send_reminder',
    ['post_id' => 456],
    'meu_plugin_group'
);

add_action('meu_plugin_send_reminder', function ($post_id) {
    // Executado após 30 minutos
    // send_reminder_email($post_id);
    error_log("Reminder sent for post {$post_id}");
}, 10, 1);

// ========== 3. Recurring (evitar duplicar) ==========

if (! as_has_scheduled_action('meu_plugin_sync_external', ['batch_id' => 1], 'meu_plugin_group')) {
    as_schedule_recurring_action(
        time(),
        6 * HOUR_IN_SECONDS,
        'meu_plugin_sync_external',
        ['batch_id' => 1],
        'meu_plugin_group'
    );
}

add_action('meu_plugin_sync_external', function ($batch_id) {
    // Executado a cada 6 horas
    // sync_data_with_external_api($batch_id);
    error_log("Synced batch {$batch_id}");
}, 10, 1);

// ========== 4. Serviço de fila de email (exemplo) ==========

class EmailQueueService
{
    private const GROUP = 'email_queue_group';

    public function enqueueOneTime(int $post_id): void
    {
        as_enqueue_async_action('process_email_queue', ['post_id' => $post_id], self::GROUP);
    }

    public function enqueueDelayed(int $post_id, int $delay_seconds = 1800): void
    {
        as_schedule_single_action(time() + $delay_seconds, 'process_email_queue', ['post_id' => $post_id], self::GROUP);
    }

    public static function handleEmailQueue(int $post_id): void
    {
        $emails = get_post_meta($post_id, 'email_queue', true);
        if (! is_array($emails)) {
            return;
        }
        foreach ($emails as $email) {
            wp_mail($email, 'Nova Publicação', 'Uma nova publicação foi criada.');
        }
        delete_post_meta($post_id, 'email_queue');
    }
}

add_action('process_email_queue', [EmailQueueService::class, 'handleEmailQueue'], 10, 1);

// Disparar ao publicar post (exemplo)
// add_action('wp_insert_post', function ($post_id) {
//     if (get_post_status($post_id) === 'publish') {
//         (new EmailQueueService())->enqueueOneTime($post_id);
//     }
// });

// ========== 5. Cancelar ação agendada ==========

// as_unschedule_action('meu_plugin_sync_external', ['batch_id' => 1], 'meu_plugin_group');
