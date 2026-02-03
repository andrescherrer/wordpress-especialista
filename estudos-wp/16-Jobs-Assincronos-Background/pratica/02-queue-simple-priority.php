<?php
/**
 * REFERÊNCIA RÁPIDA – Fila Simple (FIFO) e prioridade com Action Scheduler
 *
 * Jobs armazenados em option; cada job dispara uma ação do Action Scheduler.
 * Handler processa um job por vez; retry com exponential backoff; após max tentativas → falha (DLQ em produção).
 *
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md (Queue Patterns)
 */

if (! function_exists('as_enqueue_async_action')) {
    return;
}

// ========== Simple Queue (FIFO) ==========

class SimpleJobQueue
{
    private string $queue_name;
    private const OPTION_KEY = 'queue_%s';
    private const MAX_ATTEMPTS = 3;

    public function __construct(string $queue_name = 'default')
    {
        $this->queue_name = $queue_name;
    }

    public function enqueue(string $handler, array $payload, int $delay = 0): string
    {
        $job_id = wp_generate_uuid4();
        $key    = sprintf(self::OPTION_KEY, $this->queue_name);
        $jobs   = (array) get_option($key, []);

        $jobs[$job_id] = [
            'id'          => $job_id,
            'handler'     => $handler,
            'payload'     => $payload,
            'attempts'    => 0,
            'created_at'  => time(),
            'scheduled_at' => time() + $delay,
            'status'      => 'pending',
        ];
        update_option($key, $jobs);

        as_enqueue_async_action('process_simple_queue_job', [
            'queue_name' => $this->queue_name,
            'job_id'     => $job_id,
        ], "queue_{$this->queue_name}");

        return $job_id;
    }

    public static function processNext(string $queue_name): void
    {
        $key  = sprintf(self::OPTION_KEY, $queue_name);
        $jobs = (array) get_option($key, []);

        foreach ($jobs as $job_id => $job) {
            if (($job['status'] ?? '') !== 'pending') {
                continue;
            }
            if (($job['scheduled_at'] ?? 0) > time()) {
                continue;
            }

            $jobs[$job_id]['status']     = 'processing';
            $jobs[$job_id]['started_at'] = time();
            update_option($key, $jobs);

            try {
                do_action("queue_{$queue_name}_{$job['handler']}", $job['payload']);
                $jobs[$job_id]['status']      = 'completed';
                $jobs[$job_id]['completed_at'] = time();
            } catch (Throwable $e) {
                $jobs[$job_id]['attempts'] = ($jobs[$job_id]['attempts'] ?? 0) + 1;
                if ($jobs[$job_id]['attempts'] >= self::MAX_ATTEMPTS) {
                    $jobs[$job_id]['status']   = 'failed';
                    $jobs[$job_id]['error']    = $e->getMessage();
                    $jobs[$job_id]['failed_at'] = time();
                    // Em produção: mover para Dead Letter Queue
                } else {
                    $delay = (int) pow(2, $jobs[$job_id]['attempts']) * 60;
                    $jobs[$job_id]['status']      = 'pending';
                    $jobs[$job_id]['scheduled_at'] = time() + $delay;
                    as_schedule_single_action(
                        time() + $delay,
                        'process_simple_queue_job',
                        ['queue_name' => $queue_name, 'job_id' => $job_id],
                        "queue_{$queue_name}"
                    );
                }
            }

            update_option($key, $jobs);
            break; // um job por vez
        }
    }

    public function getJobStatus(string $job_id): ?array
    {
        $jobs = (array) get_option(sprintf(self::OPTION_KEY, $this->queue_name), []);
        return $jobs[$job_id] ?? null;
    }
}

add_action('process_simple_queue_job', function ($queue_name, $job_id) {
    SimpleJobQueue::processNext($queue_name);
}, 10, 2);

// Uso:
// $queue = new SimpleJobQueue('notifications');
// $job_id = $queue->enqueue('send_email', ['email' => 'u@e.com', 'subject' => 'Hi', 'body' => 'Hello']);
// add_action('queue_notifications_send_email', function ($payload) { wp_mail($payload['email'], $payload['subject'], $payload['body']); });

// ========== Priority Queue (conceito) ==========

// Adicione campo 'priority' (1=baixa, 10=alta) ao job. No processNext (ou processAll),
// filtre pendentes e ordene: usort($pending, fn($a, $b) => $b['priority'] <=> $a['priority']);
// Processe em ordem. Mesmo retry/DLQ que acima.
