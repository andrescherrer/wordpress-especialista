# 🔄 FASE 15: Async Jobs, Background Processing e Queues

**Versão:** 1.0  
**Data:** Fevereiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar processamento assíncrono, jobs em background e padrões de fila em WordPress

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 14](./014-WordPress-Fase-14-Implantacao-DevOps.md) | [Fase 16 →](./015-WordPress-Fase-16-Topicos-Complementares-Avancados.md)

---

## 📑 Índice

1. [Por Que Async Jobs?](#por-que-async-jobs)
2. [Limitações do WP-Cron](#limitações-do-wp-cron)
3. [Action Scheduler (Production-Ready)](#action-scheduler-production-ready)
4. [Queue Patterns (Enterprise)](#queue-patterns-enterprise)
5. [Webhook Receivers (Inbound)](#webhook-receivers-inbound)
6. [Integração com Docker](#integração-com-docker)
7. [Monitoramento em Produção](#monitoramento-em-produção)
8. [Case Studies Práticos](#case-studies-práticos)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Entender quando e por que usar async jobs ao invés de processamento síncrono
2. ✅ Substituir WP-Cron por Action Scheduler para processamento de jobs pronto para produção
3. ✅ Implementar padrões de fila (Simple, Priority, Dead Letter Queue)
4. ✅ Criar webhook receivers seguros com idempotency keys
5. ✅ Configurar workers Docker para processar async jobs
6. ✅ Monitorar filas e jobs usando Prometheus e Grafana
7. ✅ Tratar falhas de jobs, retries e dead letter queues adequadamente
8. ✅ Aplicar padrões de async jobs a cenários do mundo real (e-commerce, processamento de mídia)

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Quais são as limitações do WP-Cron e por que você deve usar Action Scheduler?
- [ ] Como você implementa idempotência em webhook receivers?
- [ ] O que é uma Dead Letter Queue e quando você deve usá-la?
- [ ] Como você previne processamento duplicado de jobs?
- [ ] Qual é a diferença entre async actions e scheduled actions?
- [ ] Como você monitora profundidade de fila e taxas de processamento de jobs?
- [ ] Quais estratégias você pode usar para tratar falhas de jobs?
- [ ] Como você escala processamento de async jobs horizontalmente?

## 🛠️ Projeto Prático

**Construir:** Sistema de Processamento de Async Jobs

Crie um sistema completo de processamento de async jobs que:
- Use Action Scheduler para gerenciamento de jobs
- Implemente filas de prioridade
- Tenha Dead Letter Queue para jobs falhados
- Receba webhooks com idempotência
- Inclua monitoramento com métricas Prometheus
- Trate retries e falhas de jobs graciosamente
- Processe jobs em workers Docker
- Demonstre casos de uso do mundo real

**Tempo estimado:** 15-20 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "WP-Cron é confiável para produção"
**Realidade:** WP-Cron só roda quando alguém visita o site. É não confiável para tarefas sensíveis ao tempo. Use Action Scheduler ou cron real para produção.

**Por que é importante:** WP-Cron pode perder tarefas agendadas se o tráfego do site for baixo. Produção precisa de confiabilidade.

**Como lembrar:** WP-Cron = acionado por visitante, não confiável. Action Scheduler = confiável, pronto para produção.

### Equívoco 2: "Async jobs sempre melhoram performance"
**Realidade:** Async jobs melhoram performance percebida (resposta mais rápida) mas não reduzem trabalho total. Eles podem aumentar complexidade e uso de recursos.

**Por que é importante:** Async jobs têm overhead. Use-os quando benefícios de experiência do usuário superam custos.

**Como lembrar:** Async = resposta mais rápida, não menos trabalho. Use quando UX importa.

### Equívoco 3: "Idempotência é apenas para APIs"
**Realidade:** Idempotência (mesma operação, mesmo resultado) é importante para qualquer operação que possa ser repetida: webhooks, jobs, pagamentos, etc.

**Por que é importante:** Retries são comuns (problemas de rede, timeouts). Idempotência previne operações duplicadas.

**Como lembrar:** Idempotência = retries seguros. Use para qualquer operação que possa ser repetida.

### Equívoco 4: "Dead Letter Queue é apenas para jobs falhados"
**Realidade:** DLQ armazena jobs que falharam após todas as tentativas. É para análise e intervenção manual, não retry automático.

**Por que é importante:** Entender o propósito da DLQ ajuda a projetar estratégias adequadas de retry e monitoramento.

**Como lembrar:** DLQ = falhas permanentes, não temporárias. Use para análise e retry manual.

### Equívoco 5: "Mais workers sempre processam jobs mais rápido"
**Realidade:** Mais workers ajudam com processamento paralelo, mas gargalos de banco de dados/API podem limitar ganhos. Monitore e escale apropriadamente.

**Por que é importante:** Adicionar workers a um problema nem sempre ajuda. Identifique e corrija gargalos primeiro.

**Como lembrar:** Workers = processamento paralelo. Gargalos = limitam velocidade. Corrija gargalos primeiro.

---

## Por Que Async Jobs?

### Problema Real: Requisições HTTP Bloqueantes

Em aplicações WordPress enterprise, muitas operações demoram para executar:

```php
<?php
/**
 * ❌ PROBLEMA: Requisição HTTP bloqueante
 * 
 * Usuário espera 10+ segundos para resposta
 * Risco de timeout
 * Impossível escalar
 */
add_action('wp_insert_post', function($post_id) {
    // Processar imagem de alta resolução (5 segundos)
    process_high_res_image($post_id);
    
    // Enviar email de notificação (2 segundos)
    send_notification_email($post_id);
    
    // Sincronizar com API externa (3 segundos)
    sync_with_external_api($post_id);
    
    // Total: 10 segundos bloqueando usuário! 🐌
});
```

**Consequências:**
- ⚠️ Timeout em requisições longas (> 30s)
- ⚠️ Experiência do usuário ruim
- ⚠️ Impossibilidade de escalar horizontalmente
- ⚠️ Falhas em integrações externas
- ⚠️ Perda de dados em caso de erro

### Solução: Background Processing

```php
<?php
/**
 * ✅ SOLUÇÃO: Processamento assíncrono
 * 
 * Resposta imediata (< 50ms)
 * Processamento em background
 * Escalável e confiável
 */
add_action('wp_insert_post', function($post_id) {
    // Enqueue para processamento assíncrono
    as_enqueue_async_action('process_post_image', [$post_id]);
    as_enqueue_async_action('send_post_notification', [$post_id]);
    as_enqueue_async_action('sync_post_external_api', [$post_id]);
    
    // Retorna imediatamente (< 50ms) ✅
});

// Handlers executados em background
add_action('process_post_image', function($post_id) {
    process_high_res_image($post_id);
});

add_action('send_post_notification', function($post_id) {
    send_notification_email($post_id);
});

add_action('sync_post_external_api', function($post_id) {
    sync_with_external_api($post_id);
});
```

**Benefícios:**
- ✅ Resposta imediata ao usuário
- ✅ Processamento confiável em background
- ✅ Escalável (múltiplos workers)
- ✅ Retry automático em caso de falha
- ✅ Visibilidade de status e erros

### Quando Usar Async Jobs?

**Use Async Jobs para:**
- ✅ Processamento de mídia (imagens, vídeos)
- ✅ Envio de emails em bulk
- ✅ Sincronização com APIs externas
- ✅ Processamento de arquivos grandes (CSV, XML)
- ✅ Geração de relatórios
- ✅ Webhooks recebidos
- ✅ Operações que demoram > 1 segundo

**Não use Async Jobs para:**
- ❌ Validações simples (< 100ms)
- ❌ Operações críticas que precisam de resposta imediata
- ❌ Operações que dependem do contexto da requisição HTTP

---

## Limitações do WP-Cron

### Problema Fundamental

**WP-Cron não é um cron real!** É um sistema pseudo-cron que depende de requisições HTTP.

```php
<?php
// WP-Cron não é um cron real!
wp_schedule_event(time(), 'hourly', 'my_hourly_task');

// ❌ PROBLEMAS CRÍTICOS:
// 1. Depende de requisições HTTP (sem tráfego = não executa)
// 2. Uma requisição = uma execução (não paralelo)
// 3. Sem retry automático em caso de falha
// 4. Sem visibilidade de falhas
// 5. Inseguro em produção com múltiplos servidores
// 6. Timeout em jobs longos
// 7. Sem controle de prioridade
```

### Quando WP-Cron Falha

#### Cenário 1: Site com Pouco Tráfego

```php
<?php
// Schedule: executar a cada 6 horas
wp_schedule_event(time(), 'six_hours', 'sync_data');

// Realidade:
// - Site recebe 10 visitas/dia
// - Job pode executar a cada 2-3 dias (quando tem requisição)
// - Dados ficam desatualizados
```

#### Cenário 2: Múltiplos Servidores (Load Balancer)

```php
<?php
// Setup: 3 servidores WordPress atrás de load balancer
// Schedule: executar a cada hora

// Realidade:
// - Cada servidor executa o job independentemente
// - 3 execuções/hora em vez de 1
// - Overhead massivo e dados duplicados
```

#### Cenário 3: Jobs Longos (Timeout)

```php
<?php
// Schedule: job que processa 10.000 registros (5 minutos)
wp_schedule_event(time(), 'hourly', 'process_bulk_data');

// Realidade:
// - PHP timeout padrão: 30 segundos
// - Job nunca completa
// - Dados ficam inconsistentes
```

#### Cenário 4: Falhas Silenciosas

```php
<?php
add_action('my_scheduled_task', function() {
    // Erro não tratado
    $result = external_api_call(); // Pode falhar silenciosamente
    
    // WP-Cron não sabe que falhou
    // Não há retry automático
    // Dados ficam inconsistentes
});
```

### Desabilitar WP-Cron em Produção

```php
<?php
// wp-config.php

// Desabilitar WP-Cron (usar cron real do sistema)
define('DISABLE_WP_CRON', true);

// Configurar cron real do sistema (crontab)
// */15 * * * * curl -s https://seusite.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```

**Mas mesmo assim, WP-Cron não é adequado para:**
- Jobs assíncronos (não agendados)
- Processamento em background
- Queues complexas
- Retry strategies
- Dead letter queues

**Solução:** Usar Action Scheduler ou sistema de queue dedicado.

---

## Action Scheduler (Production-Ready)

### O Que É Action Scheduler?

**Action Scheduler** é uma biblioteca robusta desenvolvida pela WooCommerce para processamento assíncrono e agendamento de ações em WordPress. É usado em produção por milhões de sites.

**Características:**
- ✅ Cron real (não depende de HTTP)
- ✅ Processamento assíncrono
- ✅ Retry automático
- ✅ Visibilidade de status
- ✅ Suporte a múltiplos servidores
- ✅ Jobs recorrentes e one-time
- ✅ Grupos e prioridades

### Instalação

#### Via Composer (Recomendado para Plugins)

```bash
composer require woocommerce/action-scheduler
```

#### Como Plugin Standalone

```bash
# Download de https://actionscheduler.org/
# Ou via WordPress Dashboard: Plugins > Add New > Buscar "Action Scheduler"
```

#### Verificar Instalação

```php
<?php
// Verificar se Action Scheduler está disponível
if (function_exists('as_enqueue_async_action')) {
    // Action Scheduler disponível
} else {
    // Instalar Action Scheduler primeiro
}
```

### Conceitos Fundamentais

#### 1. Async Actions (One-Time, Imediato)

```php
<?php
/**
 * Enqueue uma ação assíncrona
 * 
 * Executa assim que possível (próxima execução do cron)
 */
as_enqueue_async_action(
    'my_action_hook',           // Hook name
    [$arg1, $arg2],            // Arguments array
    'my_plugin_group'          // Group (opcional)
);

// Handler
add_action('my_action_hook', function($arg1, $arg2) {
    // Processamento em background
    process_data($arg1, $arg2);
});
```

#### 2. Scheduled Actions (One-Time, com Delay)

```php
<?php
/**
 * Schedule uma ação para executar no futuro
 * 
 * Executa após 30 minutos
 */
as_schedule_single_action(
    time() + (30 * MINUTE_IN_SECONDS),  // Timestamp
    'my_delayed_action',                 // Hook name
    [$post_id],                          // Arguments
    'my_plugin_group'                    // Group (opcional)
);

// Handler
add_action('my_delayed_action', function($post_id) {
    // Executado após 30 minutos
    send_reminder_email($post_id);
});
```

#### 3. Recurring Actions (Recorrente)

```php
<?php
/**
 * Schedule uma ação recorrente
 * 
 * Executa a cada 6 horas
 */
if (!as_has_scheduled_action('sync_external_api', ['batch_id' => 1])) {
    as_schedule_recurring_action(
        time(),                          // First run timestamp
        (6 * HOUR_IN_SECONDS),          // Interval (segundos)
        'sync_external_api',            // Hook name
        ['batch_id' => 1],              // Arguments
        'my_plugin_group'               // Group (opcional)
    );
}

// Handler
add_action('sync_external_api', function($batch_id) {
    // Executado a cada 6 horas
    sync_data_with_external_api($batch_id);
});
```

### Exemplo Completo: Email Queue Service

```php
<?php
/**
 * Email Queue Service
 * 
 * Processa emails em background sem bloquear requisições HTTP
 */
class EmailQueueService {
    
    /**
     * Enqueue uma ação one-time
     * 
     * Executa assim que possível
     */
    public function enqueueOneTime(int $post_id): void {
        as_enqueue_async_action(
            'process_email_queue',
            ['post_id' => $post_id],
            'email_queue_group'
        );
        
        // Retorna imediatamente
    }
    
    /**
     * Schedule com delay
     * 
     * Executa após 30 minutos
     */
    public function enqueueDelayed(int $post_id): void {
        as_schedule_single_action(
            time() + (30 * MINUTE_IN_SECONDS),
            'process_email_queue',
            ['post_id' => $post_id],
            'email_queue_group'
        );
    }
    
    /**
     * Schedule recorrente
     * 
     * Executa a cada 6 horas
     */
    public function enqueueRecurring(int $batch_id): void {
        if (!as_has_scheduled_action(
            'sync_external_api',
            ['batch_id' => $batch_id],
            'email_queue_group'
        )) {
            as_schedule_recurring_action(
                time(),
                (6 * HOUR_IN_SECONDS),
                'sync_external_api',
                ['batch_id' => $batch_id],
                'email_queue_group'
            );
        }
    }
    
    /**
     * Handler de ação
     * 
     * Executado em background
     */
    public static function handleEmailQueue(int $post_id): void {
        // Lógica de processamento
        $emails = get_post_meta($post_id, 'email_queue', true) ?: [];
        
        foreach ($emails as $email) {
            wp_mail(
                $email,
                'Nova Publicação',
                'Uma nova publicação foi criada!'
            );
        }
        
        // Limpar queue após processamento
        delete_post_meta($post_id, 'email_queue');
    }
}

// Setup
$service = new EmailQueueService();

// Enqueue quando post é publicado
add_action('wp_insert_post', function($post_id) {
    if (get_post_status($post_id) === 'publish') {
        $service->enqueueOneTime($post_id);
    }
});

// Registrar handler
add_action('process_email_queue', [
    EmailQueueService::class,
    'handleEmailQueue'
]);
```

### Exemplo Prático: Processamento de CSV

```php
<?php
/**
 * CSV Import Service
 * 
 * Importa CSV grande em chunks sem bloquear requisições HTTP
 */
class CSVImportService {
    
    /**
     * Importar CSV grande (não-bloqueante)
     * 
     * Processa em chunks de 100 linhas
     */
    public function import(int $file_id): void {
        $filepath = get_attached_file($file_id);
        
        if (!file_exists($filepath)) {
            throw new Exception("Arquivo não encontrado: {$filepath}");
        }
        
        // Conta total de linhas
        $total_lines = $this->countLines($filepath);
        $chunk_size = 100;
        
        // Salvar total para tracking
        update_post_meta($file_id, 'total_rows', $total_lines);
        update_post_meta($file_id, 'imported_rows', 0);
        
        // Enqueue em chunks
        for ($offset = 0; $offset < $total_lines; $offset += $chunk_size) {
            as_enqueue_async_action('import_csv_chunk', [
                'file_id' => $file_id,
                'offset' => $offset,
                'chunk_size' => $chunk_size,
            ], 'csv_import_group');
        }
        
        // Notifica conclusão (verifica em 1 minuto)
        as_schedule_single_action(
            time() + 60,
            'check_csv_import_complete',
            ['file_id' => $file_id],
            'csv_import_group'
        );
    }
    
    /**
     * Conta linhas do arquivo
     */
    private function countLines(string $filepath): int {
        $count = 0;
        $handle = fopen($filepath, 'r');
        
        while (!feof($handle)) {
            fgets($handle);
            $count++;
        }
        
        fclose($handle);
        return $count;
    }
    
    /**
     * Processa um chunk
     */
    public static function processChunk(int $file_id, int $offset, int $chunk_size): void {
        $filepath = get_attached_file($file_id);
        
        if (!file_exists($filepath)) {
            error_log("CSV Import Error: Arquivo não encontrado para file_id {$file_id}");
            return;
        }
        
        $handle = fopen($filepath, 'r');
        
        // Pular header (primeira linha)
        if ($offset === 0) {
            fgetcsv($handle);
        }
        
        // Pular até offset
        for ($i = 0; $i < $offset; $i++) {
            fgetcsv($handle);
        }
        
        $count = 0;
        $inserted = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle)) && $count < $chunk_size) {
            try {
                // Validar linha
                if (empty($row[0]) || empty($row[1])) {
                    $errors[] = "Linha {$offset + $count}: Dados incompletos";
                    $count++;
                    continue;
                }
                
                // Criar post
                $post_id = wp_insert_post([
                    'post_type' => 'item',
                    'post_title' => sanitize_text_field($row[0]),
                    'post_content' => sanitize_textarea_field($row[1]),
                    'post_status' => 'publish',
                ]);
                
                if (!is_wp_error($post_id)) {
                    $inserted++;
                } else {
                    $errors[] = "Linha {$offset + $count}: " . $post_id->get_error_message();
                }
            } catch (Exception $e) {
                $errors[] = "Linha {$offset + $count}: " . $e->getMessage();
                do_action('csv_import_error', $e, $row);
            }
            
            $count++;
        }
        
        fclose($handle);
        
        // Atualizar progresso
        $current_imported = (int)get_post_meta($file_id, 'imported_rows', true);
        update_post_meta($file_id, 'imported_rows', $current_imported + $inserted);
        
        // Log erros
        if (!empty($errors)) {
            $existing_errors = get_post_meta($file_id, 'import_errors', true) ?: [];
            update_post_meta($file_id, 'import_errors', array_merge($existing_errors, $errors));
        }
    }
}

// Setup
add_action('wp_handle_upload', function($upload_data) {
    if (strpos($upload_data['type'], 'csv') !== false) {
        $file_id = $upload_data['attachment_id'] ?? null;
        if ($file_id) {
            try {
                (new CSVImportService())->import($file_id);
            } catch (Exception $e) {
                error_log("CSV Import Error: " . $e->getMessage());
            }
        }
    }
    return $upload_data;
});

// Registrar handlers
add_action('import_csv_chunk', [
    CSVImportService::class,
    'processChunk'
]);

add_action('check_csv_import_complete', function($file_id) {
    $total = (int)get_post_meta($file_id, 'total_rows', true);
    $imported = (int)get_post_meta($file_id, 'imported_rows', true);
    
    if ($total === $imported) {
        // Importação completa
        do_action('csv_import_complete', $file_id);
        update_post_meta($file_id, 'import_status', 'completed');
    } else {
        // Reagendar verificação
        as_schedule_single_action(
            time() + 60,
            'check_csv_import_complete',
            ['file_id' => $file_id],
            'csv_import_group'
        );
    }
});
```

### Verificar e Cancelar Ações Agendadas

```php
<?php
/**
 * Verificar se ação está agendada
 */
if (as_has_scheduled_action('my_action', ['arg1' => 'value'])) {
    // Ação já está agendada
}

/**
 * Cancelar ação agendada
 */
as_unschedule_action('my_action', ['arg1' => 'value']);

/**
 * Cancelar todas as ações de um hook
 */
as_unschedule_all_actions('my_action');

/**
 * Cancelar todas as ações de um grupo
 */
as_unschedule_all_actions('my_action', [], 'my_plugin_group');
```

### Monitoramento de Ações

```php
<?php
/**
 * Action Scheduler Monitor
 * 
 * Dashboard widget com status das ações
 */
class ActionSchedulerMonitor {
    
    /**
     * Renderizar widget no dashboard
     */
    public function renderDashboard(): void {
        $pending = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_PENDING
        ]);
        
        $processing = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_RUNNING
        ]);
        
        $failed = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_FAILED
        ]);
        
        $completed = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_COMPLETE
        ]);
        
        echo sprintf(
            '<div class="scheduler-status">
                <h3>Action Scheduler Status</h3>
                <p><strong>Pending:</strong> %d</p>
                <p><strong>Processing:</strong> %d</p>
                <p><strong>Failed:</strong> %d</p>
                <p><strong>Completed:</strong> %d</p>
            </div>',
            $pending,
            $processing,
            $failed,
            $completed
        );
    }
    
    /**
     * Listar ações por grupo
     */
    public function getGroupStatus(string $group): array {
        $actions = as_get_scheduled_actions([
            'group' => $group,
            'per_page' => -1,
        ]);
        
        return array_map(function($action) {
            return [
                'hook' => $action->get_hook(),
                'status' => $action->get_status(),
                'scheduled' => $action->get_schedule()->get_timestamp(),
                'args' => $action->get_args(),
            ];
        }, $actions);
    }
    
    /**
     * Listar ações falhadas
     */
    public function getFailedActions(int $limit = 10): array {
        $actions = as_get_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_FAILED,
            'per_page' => $limit,
        ]);
        
        return array_map(function($action) {
            return [
                'hook' => $action->get_hook(),
                'args' => $action->get_args(),
                'scheduled' => $action->get_schedule()->get_timestamp(),
                'log_entries' => $action->get_log_entries(),
            ];
        }, $actions);
    }
}

// Adicionar widget ao dashboard
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'action_scheduler_status',
        'Action Scheduler Status',
        [new ActionSchedulerMonitor(), 'renderDashboard']
    );
});
```

---

## Queue Patterns (Enterprise)

Para aplicações mais complexas, você pode implementar padrões de queue avançados sobre o Action Scheduler.

### Pattern 1: Simple Queue (FIFO)

```php
<?php
/**
 * Simple Job Queue (First In, First Out)
 * 
 * Processa jobs na ordem que foram enfileirados
 */
class SimpleJobQueue {
    
    private string $queue_name;
    
    public function __construct(string $queue_name = 'default') {
        $this->queue_name = $queue_name;
    }
    
    /**
     * Enqueue um job
     * 
     * @param string $handler Nome do handler
     * @param array $payload Dados do job
     * @param int $delay Delay em segundos (opcional)
     * @return string Job ID
     */
    public function enqueue(string $handler, array $payload, int $delay = 0): string {
        $job_id = wp_generate_uuid4();
        
        $job = [
            'id' => $job_id,
            'handler' => $handler,
            'payload' => $payload,
            'attempts' => 0,
            'created_at' => time(),
            'scheduled_at' => time() + $delay,
            'status' => 'pending',
        ];
        
        // Armazenar job
        $jobs = (array)get_option("queue_{$this->queue_name}", []);
        $jobs[$job_id] = $job;
        update_option("queue_{$this->queue_name}", $jobs);
        
        // Enqueue Action Scheduler para processar
        as_enqueue_async_action('process_queue_job', [
            'queue_name' => $this->queue_name,
            'job_id' => $job_id,
        ], "queue_{$this->queue_name}");
        
        return $job_id;
    }
    
    /**
     * Processar próximo job
     */
    public static function processNext(string $queue_name): void {
        $jobs = (array)get_option("queue_{$queue_name}", []);
        
        foreach ($jobs as $job_id => $job) {
            // Pular se não está pendente ou ainda não está no horário
            if ($job['status'] !== 'pending' || $job['scheduled_at'] > time()) {
                continue;
            }
            
            try {
                // Marcar como processando
                $job['status'] = 'processing';
                $job['started_at'] = time();
                $jobs[$job_id] = $job;
                update_option("queue_{$queue_name}", $jobs);
                
                // Executar handler
                do_action("queue_{$queue_name}_{$job['handler']}", $job['payload']);
                
                // Marcar como concluído
                $job['status'] = 'completed';
                $job['completed_at'] = time();
                
            } catch (Exception $e) {
                // Retry logic
                $job['attempts']++;
                
                if ($job['attempts'] >= 3) {
                    // Máximo de tentativas atingido
                    $job['status'] = 'failed';
                    $job['error'] = $e->getMessage();
                    $job['failed_at'] = time();
                    
                    // Mover para Dead Letter Queue
                    DeadLetterQueue::moveToDeadLetter($job, $e);
                } else {
                    // Retry com exponential backoff
                    $delay = pow(2, $job['attempts']) * 60; // 2, 4, 8 minutos
                    $job['status'] = 'pending';
                    $job['scheduled_at'] = time() + $delay;
                }
            }
            
            $jobs[$job_id] = $job;
            update_option("queue_{$queue_name}", $jobs);
            
            // Processar apenas um job por vez
            break;
        }
    }
    
    /**
     * Obter status de um job
     */
    public function getJobStatus(string $job_id): ?array {
        $jobs = (array)get_option("queue_{$this->queue_name}", []);
        return $jobs[$job_id] ?? null;
    }
}

// Uso
$queue = new SimpleJobQueue('notifications');

// Enqueue job
$job_id = $queue->enqueue('send_email', [
    'email' => 'user@example.com',
    'subject' => 'Hello',
    'body' => 'Welcome!',
]);

// Handler
add_action('queue_notifications_send_email', function($payload) {
    wp_mail(
        $payload['email'],
        $payload['subject'],
        $payload['body']
    );
});

// Processar jobs
add_action('process_queue_job', [SimpleJobQueue::class, 'processNext']);
```

### Pattern 2: Priority Queue

```php
<?php
/**
 * Priority Job Queue
 * 
 * Processa jobs por prioridade (1=baixa, 10=alta)
 */
class PriorityJobQueue {
    
    private string $queue_name;
    
    public function __construct(string $queue_name = 'default') {
        $this->queue_name = $queue_name;
    }
    
    /**
     * Enqueue com prioridade
     * 
     * @param string $handler Nome do handler
     * @param array $payload Dados do job
     * @param int $priority Prioridade (1=baixa, 10=alta)
     * @return string Job ID
     */
    public function enqueue(
        string $handler,
        array $payload,
        int $priority = 5
    ): string {
        $job_id = wp_generate_uuid4();
        
        $job = [
            'id' => $job_id,
            'handler' => $handler,
            'payload' => $payload,
            'priority' => max(1, min(10, $priority)), // Clamp entre 1-10
            'attempts' => 0,
            'created_at' => time(),
            'status' => 'pending',
        ];
        
        // Armazenar job
        $jobs = (array)get_option("queue_{$this->queue_name}", []);
        $jobs[$job_id] = $job;
        update_option("queue_{$this->queue_name}", $jobs);
        
        // Enqueue Action Scheduler
        as_enqueue_async_action('process_priority_queue', [
            'queue_name' => $this->queue_name,
        ], "queue_{$this->queue_name}");
        
        return $job_id;
    }
    
    /**
     * Processar todos em ordem de prioridade
     */
    public static function processAll(string $queue_name): void {
        $jobs = (array)get_option("queue_{$queue_name}", []);
        
        // Filtrar apenas pendentes
        $pending_jobs = array_filter($jobs, function($job) {
            return $job['status'] === 'pending';
        });
        
        // Ordenar por prioridade (maior primeiro)
        usort($pending_jobs, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        // Processar em ordem de prioridade
        foreach ($pending_jobs as $job_id => $job) {
            try {
                // Marcar como processando
                $jobs[$queue_name][$job_id]['status'] = 'processing';
                $jobs[$queue_name][$job_id]['started_at'] = time();
                update_option("queue_{$queue_name}", $jobs[$queue_name]);
                
                // Executar handler
                do_action("queue_{$queue_name}_{$job['handler']}", $job['payload']);
                
                // Marcar como concluído
                $jobs[$queue_name][$job_id]['status'] = 'completed';
                $jobs[$queue_name][$job_id]['completed_at'] = time();
                
            } catch (Exception $e) {
                // Retry logic (similar ao SimpleJobQueue)
                $jobs[$queue_name][$job_id]['attempts']++;
                
                if ($jobs[$queue_name][$job_id]['attempts'] >= 3) {
                    $jobs[$queue_name][$job_id]['status'] = 'failed';
                    $jobs[$queue_name][$job_id]['error'] = $e->getMessage();
                    DeadLetterQueue::moveToDeadLetter($job, $e);
                } else {
                    $delay = pow(2, $jobs[$queue_name][$job_id]['attempts']) * 60;
                    $jobs[$queue_name][$job_id]['status'] = 'pending';
                    $jobs[$queue_name][$job_id]['scheduled_at'] = time() + $delay;
                }
            }
            
            update_option("queue_{$queue_name}", $jobs[$queue_name]);
        }
    }
}

// Uso
$queue = new PriorityJobQueue('notifications');

// Job de alta prioridade
$queue->enqueue('send_urgent_email', [
    'email' => 'admin@example.com',
    'subject' => 'URGENT: System Alert',
], 10); // Prioridade máxima

// Job de baixa prioridade
$queue->enqueue('send_newsletter', [
    'email' => 'user@example.com',
    'subject' => 'Monthly Newsletter',
], 1); // Prioridade mínima

// Processar (alta prioridade primeiro)
add_action('process_priority_queue', [PriorityJobQueue::class, 'processAll']);
```

### Pattern 3: Dead Letter Queue (DLQ) - Implementação Completa

**Conceito:** DLQ armazena jobs que falharam permanentemente após todas as tentativas de retry, permitindo análise e reprocessamento manual.

**Quando usar DLQ:**
- Jobs que falharam após máximo de tentativas
- Erros permanentes (não temporários)
- Necessidade de análise de falhas
- Requisito de auditoria

#### Implementação Completa de Dead Letter Queue

```php
<?php
/**
 * Dead Letter Queue - Implementação Production-Ready
 * 
 * Armazena jobs que falharam permanentemente para análise
 */
class DeadLetterQueue {
    
    private const TABLE_NAME = 'dlq_jobs';
    private const MAX_RETENTION_DAYS = 30;
    
    /**
     * Criar tabela customizada para DLQ (melhor performance que options)
     */
    public static function createTable(): void {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            job_id varchar(255) NOT NULL,
            queue_name varchar(100) NOT NULL,
            handler varchar(255) NOT NULL,
            payload longtext NOT NULL,
            original_job longtext NOT NULL,
            error_message text NOT NULL,
            error_code varchar(50) DEFAULT NULL,
            error_trace longtext DEFAULT NULL,
            attempts int(11) NOT NULL DEFAULT 0,
            last_attempt_at datetime DEFAULT NULL,
            moved_to_dlq_at datetime NOT NULL,
            retried_at datetime DEFAULT NULL,
            retry_count int(11) NOT NULL DEFAULT 0,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY job_id (job_id),
            KEY queue_name (queue_name),
            KEY handler (handler),
            KEY moved_to_dlq_at (moved_to_dlq_at)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Mover job para DLQ após falhas permanentes
     */
    public static function moveToDeadLetter(
        array $job, 
        Exception $exception,
        ?string $queue_name = null
    ): void {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        // Criar tabela se não existir
        self::createTable();
        
        $dlq_data = [
            'job_id' => $job['id'] ?? wp_generate_uuid4(),
            'queue_name' => $queue_name ?? $job['queue_name'] ?? 'default',
            'handler' => $job['handler'] ?? 'unknown',
            'payload' => json_encode($job['payload'] ?? []),
            'original_job' => json_encode($job),
            'error_message' => $exception->getMessage(),
            'error_code' => (string)$exception->getCode(),
            'error_trace' => $exception->getTraceAsString(),
            'attempts' => $job['attempts'] ?? 0,
            'last_attempt_at' => current_time('mysql'),
            'moved_to_dlq_at' => current_time('mysql'),
            'metadata' => json_encode([
                'user_id' => get_current_user_id(),
                'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]),
        ];
        
        // Inserir ou atualizar (se já existe)
        $wpdb->replace($table_name, $dlq_data);
        
        // Notificar admin via hook
        do_action('dlq_job_stored', $dlq_data);
        
        // Enviar para Sentry (se disponível)
        if (function_exists('Sentry\\captureException')) {
            Sentry\captureException($exception, [
                'tags' => [
                    'type' => 'dlq_job',
                    'job_id' => $dlq_data['job_id'],
                    'queue_name' => $dlq_data['queue_name'],
                    'handler' => $dlq_data['handler'],
                ],
                'extra' => [
                    'job' => $job,
                    'dlq_data' => $dlq_data,
                ],
                'level' => 'error',
            ]);
        }
        
        // Log estruturado
        error_log(sprintf(
            '[DLQ] Job moved to dead letter queue | job_id=%s | queue=%s | handler=%s | attempts=%d | error=%s',
            $dlq_data['job_id'],
            $dlq_data['queue_name'],
            $dlq_data['handler'],
            $dlq_data['attempts'],
            $exception->getMessage()
        ));
        
        // Enviar email de alerta (se configurado)
        $alert_threshold = apply_filters('dlq_alert_threshold', 5);
        $recent_count = self::countRecentJobs($dlq_data['queue_name'], 1); // Última hora
        
        if ($recent_count >= $alert_threshold) {
            self::sendAlertEmail($dlq_data['queue_name'], $recent_count);
        }
    }
    
    /**
     * Listar jobs na DLQ com filtros
     */
    public static function getDeadLetters(array $args = []): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $defaults = [
            'queue_name' => null,
            'handler' => null,
            'limit' => 50,
            'offset' => 0,
            'orderby' => 'moved_to_dlq_at',
            'order' => 'DESC',
        ];
        
        $args = wp_parse_args($args, $defaults);
        
        $where = ['1=1'];
        $where_values = [];
        
        if ($args['queue_name']) {
            $where[] = 'queue_name = %s';
            $where_values[] = $args['queue_name'];
        }
        
        if ($args['handler']) {
            $where[] = 'handler = %s';
            $where_values[] = $args['handler'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        if (!empty($where_values)) {
            $where_clause = $wpdb->prepare($where_clause, $where_values);
        }
        
        $orderby = sanitize_sql_orderby("{$args['orderby']} {$args['order']}");
        
        $query = "SELECT * FROM {$table_name} 
                  WHERE {$where_clause}
                  ORDER BY {$orderby}
                  LIMIT %d OFFSET %d";
        
        $results = $wpdb->get_results(
            $wpdb->prepare($query, $args['limit'], $args['offset']),
            ARRAY_A
        );
        
        // Decodificar JSON fields
        foreach ($results as &$result) {
            $result['payload'] = json_decode($result['payload'], true);
            $result['original_job'] = json_decode($result['original_job'], true);
            $result['metadata'] = json_decode($result['metadata'], true);
        }
        
        return $results;
    }
    
    /**
     * Contar jobs na DLQ
     */
    public static function countDeadLetters(?string $queue_name = null): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        if ($queue_name) {
            return (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE queue_name = %s",
                $queue_name
            ));
        }
        
        return (int)$wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }
    
    /**
     * Contar jobs recentes (últimas N horas)
     */
    public static function countRecentJobs(?string $queue_name = null, int $hours = 24): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $since = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        if ($queue_name) {
            return (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} 
                 WHERE queue_name = %s AND moved_to_dlq_at >= %s",
                $queue_name,
                $since
            ));
        }
        
        return (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE moved_to_dlq_at >= %s",
            $since
        ));
    }
    
    /**
     * Reprocessar job da DLQ
     */
    public static function retry(string $job_id, bool $keep_in_dlq = false): bool {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $dlq_job = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE job_id = %s", $job_id),
            ARRAY_A
        );
        
        if (!$dlq_job) {
            throw new Exception("Job não encontrado na DLQ: {$job_id}");
        }
        
        $original_job = json_decode($dlq_job['original_job'], true);
        
        // Reset attempts e status
        $original_job['attempts'] = 0;
        $original_job['status'] = 'pending';
        $original_job['retried_from_dlq'] = true;
        $original_job['retry_count'] = ($dlq_job['retry_count'] ?? 0) + 1;
        
        // Requeue usando Action Scheduler
        $action_id = as_enqueue_async_action(
            $dlq_job['handler'],
            json_decode($dlq_job['payload'], true),
            $dlq_job['queue_name']
        );
        
        if ($action_id) {
            // Atualizar registro na DLQ
            $wpdb->update(
                $table_name,
                [
                    'retried_at' => current_time('mysql'),
                    'retry_count' => $dlq_job['retry_count'] + 1,
                ],
                ['job_id' => $job_id]
            );
            
            // Remover da DLQ se não deve manter
            if (!$keep_in_dlq) {
                self::delete($job_id);
            }
            
            // Log
            error_log(sprintf(
                '[DLQ] Job retried | job_id=%s | handler=%s | retry_count=%d',
                $job_id,
                $dlq_job['handler'],
                $dlq_job['retry_count'] + 1
            ));
            
            // Hook
            do_action('dlq_job_retried', $job_id, $dlq_job);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Reprocessar múltiplos jobs
     */
    public static function retryBatch(array $job_ids, bool $keep_in_dlq = false): array {
        $results = [
            'success' => [],
            'failed' => [],
        ];
        
        foreach ($job_ids as $job_id) {
            try {
                if (self::retry($job_id, $keep_in_dlq)) {
                    $results['success'][] = $job_id;
                } else {
                    $results['failed'][] = $job_id;
                }
            } catch (Exception $e) {
                $results['failed'][] = $job_id;
                error_log(sprintf(
                    '[DLQ] Failed to retry job %s: %s',
                    $job_id,
                    $e->getMessage()
                ));
            }
        }
        
        return $results;
    }
    
    /**
     * Deletar job da DLQ
     */
    public static function delete(string $job_id): bool {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $deleted = $wpdb->delete(
            $table_name,
            ['job_id' => $job_id]
        );
        
        if ($deleted) {
            error_log(sprintf('[DLQ] Job deleted | job_id=%s', $job_id));
            do_action('dlq_job_deleted', $job_id);
        }
        
        return (bool)$deleted;
    }
    
    /**
     * Limpar jobs antigos (manutenção)
     */
    public static function cleanup(int $days = null): int {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $days = $days ?? self::MAX_RETENTION_DAYS;
        
        $before_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table_name} WHERE moved_to_dlq_at < %s",
            $before_date
        ));
        
        if ($deleted) {
            error_log(sprintf('[DLQ] Cleaned up %d old jobs (older than %d days)', $deleted, $days));
            do_action('dlq_cleanup_completed', $deleted);
        }
        
        return $deleted;
    }
    
    /**
     * Estatísticas da DLQ
     */
    public static function getStats(?string $queue_name = null): array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $where = $queue_name 
            ? $wpdb->prepare("WHERE queue_name = %s", $queue_name)
            : '';
        
        $stats = [
            'total' => 0,
            'by_queue' => [],
            'by_handler' => [],
            'recent_24h' => 0,
            'oldest_job' => null,
        ];
        
        // Total
        $stats['total'] = (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} {$where}"
        );
        
        // Por queue
        $by_queue = $wpdb->get_results(
            "SELECT queue_name, COUNT(*) as count 
             FROM {$table_name} 
             GROUP BY queue_name",
            ARRAY_A
        );
        foreach ($by_queue as $row) {
            $stats['by_queue'][$row['queue_name']] = (int)$row['count'];
        }
        
        // Por handler
        $by_handler = $wpdb->get_results(
            "SELECT handler, COUNT(*) as count 
             FROM {$table_name} 
             {$where}
             GROUP BY handler 
             ORDER BY count DESC 
             LIMIT 10",
            ARRAY_A
        );
        foreach ($by_handler as $row) {
            $stats['by_handler'][$row['handler']] = (int)$row['count'];
        }
        
        // Recentes (24h)
        $since = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $stats['recent_24h'] = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} 
             {$where} AND moved_to_dlq_at >= %s",
            $since
        ));
        
        // Job mais antigo
        $oldest = $wpdb->get_var(
            "SELECT moved_to_dlq_at FROM {$table_name} 
             {$where}
             ORDER BY moved_to_dlq_at ASC LIMIT 1"
        );
        $stats['oldest_job'] = $oldest;
        
        return $stats;
    }
    
    /**
     * Enviar email de alerta
     */
    private static function sendAlertEmail(string $queue_name, int $count): void {
        $to = apply_filters('dlq_alert_email', get_option('admin_email'));
        $subject = sprintf('[ALERT] High DLQ count in queue: %s', $queue_name);
        $message = sprintf(
            "Queue '%s' has %d jobs in Dead Letter Queue in the last hour.\n\n" .
            "Please review and take action.\n\n" .
            "View DLQ: %s",
            $queue_name,
            $count,
            admin_url('admin.php?page=dlq')
        );
        
        wp_mail($to, $subject, $message);
    }
}

// Admin interface completa para DLQ
add_action('admin_menu', function() {
    add_submenu_page(
        'tools.php',
        'Dead Letter Queue',
        'DLQ',
        'manage_options',
        'dlq',
        [DeadLetterQueueAdmin::class, 'renderPage']
    );
});

class DeadLetterQueueAdmin {
    
    public static function renderPage(): void {
        // Processar ações
        if (isset($_POST['action']) && check_admin_referer('dlq_action')) {
            self::handleAction();
        }
        
        // Obter filtros
        $queue_name = sanitize_text_field($_GET['queue'] ?? '');
        $handler = sanitize_text_field($_GET['handler'] ?? '');
        $page = max(1, intval($_GET['paged'] ?? 1));
        $per_page = 20;
        
        // Obter jobs
        $args = [
            'queue_name' => $queue_name ?: null,
            'handler' => $handler ?: null,
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ];
        
        $dlq_jobs = DeadLetterQueue::getDeadLetters($args);
        $total = DeadLetterQueue::countDeadLetters($queue_name ?: null);
        $stats = DeadLetterQueue::getStats($queue_name ?: null);
        
        ?>
        <div class="wrap">
            <h1>Dead Letter Queue</h1>
            
            <?php self::renderStats($stats); ?>
            
            <?php self::renderFilters($queue_name, $handler); ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('dlq_action'); ?>
                
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="bulk_action">
                            <option value="">Bulk Actions</option>
                            <option value="retry">Retry Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="button action">Apply</button>
                    </div>
                    
                    <div class="tablenav-pages">
                        <?php
                        $total_pages = ceil($total / $per_page);
                        echo paginate_links([
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $page,
                        ]);
                        ?>
                    </div>
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all">
                            </td>
                            <th>Job ID</th>
                            <th>Queue</th>
                            <th>Handler</th>
                            <th>Error</th>
                            <th>Attempts</th>
                            <th>Moved to DLQ</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dlq_jobs)): ?>
                            <tr>
                                <td colspan="8">No jobs in Dead Letter Queue.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dlq_jobs as $job): ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="job_ids[]" value="<?php echo esc_attr($job['job_id']); ?>">
                                    </th>
                                    <td>
                                        <code><?php echo esc_html(substr($job['job_id'], 0, 8)); ?>...</code>
                                    </td>
                                    <td><?php echo esc_html($job['queue_name']); ?></td>
                                    <td><code><?php echo esc_html($job['handler']); ?></code></td>
                                    <td>
                                        <details>
                                            <summary style="cursor: pointer; color: #dc3232;">
                                                <?php echo esc_html(wp_trim_words($job['error_message'], 20)); ?>
                                            </summary>
                                            <pre style="max-width: 500px; overflow: auto;"><?php 
                                                echo esc_html($job['error_message']); 
                                                if ($job['error_trace']) {
                                                    echo "\n\n" . esc_html($job['error_trace']);
                                                }
                                            ?></pre>
                                        </details>
                                    </td>
                                    <td><?php echo esc_html($job['attempts']); ?></td>
                                    <td><?php echo esc_html($job['moved_to_dlq_at']); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(wp_nonce_url(
                                            add_query_arg(['action' => 'retry', 'job_id' => $job['job_id']]),
                                            'dlq_retry_' . $job['job_id']
                                        )); ?>" class="button button-small">Retry</a>
                                        <a href="<?php echo esc_url(wp_nonce_url(
                                            add_query_arg(['action' => 'delete', 'job_id' => $job['job_id']]),
                                            'dlq_delete_' . $job['job_id']
                                        )); ?>" class="button button-small" onclick="return confirm('Delete this job?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#cb-select-all').on('change', function() {
                $('input[name="job_ids[]"]').prop('checked', this.checked);
            });
        });
        </script>
        <?php
    }
    
    private static function renderStats(array $stats): void {
        ?>
        <div class="dlq-stats" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4;">
            <h2>Statistics</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <strong>Total Jobs:</strong> <?php echo number_format($stats['total']); ?>
                </div>
                <div>
                    <strong>Last 24h:</strong> <?php echo number_format($stats['recent_24h']); ?>
                </div>
                <?php if ($stats['oldest_job']): ?>
                    <div>
                        <strong>Oldest Job:</strong> <?php echo esc_html($stats['oldest_job']); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($stats['by_handler'])): ?>
                <h3>Top Failed Handlers</h3>
                <ul>
                    <?php foreach (array_slice($stats['by_handler'], 0, 5, true) as $handler => $count): ?>
                        <li><code><?php echo esc_html($handler); ?></code>: <?php echo number_format($count); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
    }
    
    private static function renderFilters(string $queue_name, string $handler): void {
        ?>
        <div class="dlq-filters" style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4;">
            <form method="get" action="">
                <input type="hidden" name="page" value="dlq">
                <label>
                    Queue: 
                    <input type="text" name="queue" value="<?php echo esc_attr($queue_name); ?>" placeholder="Filter by queue">
                </label>
                <label>
                    Handler: 
                    <input type="text" name="handler" value="<?php echo esc_attr($handler); ?>" placeholder="Filter by handler">
                </label>
                <button type="submit" class="button">Filter</button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dlq')); ?>" class="button">Clear</a>
            </form>
        </div>
        <?php
    }
    
    private static function handleAction(): void {
        $action = sanitize_text_field($_POST['action'] ?? $_GET['action'] ?? '');
        
        if ($action === 'retry' && isset($_GET['job_id'])) {
            $job_id = sanitize_text_field($_GET['job_id']);
            check_admin_referer('dlq_retry_' . $job_id);
            
            try {
                DeadLetterQueue::retry($job_id);
                wp_redirect(add_query_arg(['message' => 'retried'], admin_url('admin.php?page=dlq')));
                exit;
            } catch (Exception $e) {
                wp_die('Error retrying job: ' . $e->getMessage());
            }
        }
        
        if ($action === 'delete' && isset($_GET['job_id'])) {
            $job_id = sanitize_text_field($_GET['job_id']);
            check_admin_referer('dlq_delete_' . $job_id);
            
            DeadLetterQueue::delete($job_id);
            wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=dlq')));
            exit;
        }
        
        if ($action === 'bulk_action' && isset($_POST['bulk_action']) && isset($_POST['job_ids'])) {
            $bulk_action = sanitize_text_field($_POST['bulk_action']);
            $job_ids = array_map('sanitize_text_field', $_POST['job_ids']);
            
            if ($bulk_action === 'retry') {
                $results = DeadLetterQueue::retryBatch($job_ids);
                $message = sprintf('Retried %d jobs, %d failed', count($results['success']), count($results['failed']));
            } elseif ($bulk_action === 'delete') {
                foreach ($job_ids as $job_id) {
                    DeadLetterQueue::delete($job_id);
                }
                $message = sprintf('Deleted %d jobs', count($job_ids));
            }
            
            wp_redirect(add_query_arg(['message' => urlencode($message)], admin_url('admin.php?page=dlq')));
            exit;
        }
    }
}

// Agendar limpeza automática (diária)
add_action('wp_scheduled_dlq_cleanup', function() {
    DeadLetterQueue::cleanup();
});

if (!wp_next_scheduled('wp_scheduled_dlq_cleanup')) {
    wp_schedule_event(time(), 'daily', 'wp_scheduled_dlq_cleanup');
}

// Criar tabela na ativação
register_activation_hook(__FILE__, [DeadLetterQueue::class, 'createTable']);
                    echo '<tr>';
                    echo '<td>' . esc_html($job_id) . '</td>';
                    echo '<td>' . esc_html($dlq_job['original_job']['handler']) . '</td>';
                    echo '<td>' . esc_html($dlq_job['error']['message']) . '</td>';
                    echo '<td>' . esc_html($dlq_job['attempts']) . '</td>';
                    echo '<td>';
                    echo '<a href="?page=dlq&retry=' . esc_attr($job_id) . '" class="button">Retry</a> ';
                    echo '<a href="?page=dlq&delete=' . esc_attr($job_id) . '" class="button button-danger">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            }
            
            echo '</div>';
            
            // Handle actions
            if (isset($_GET['retry'])) {
                check_admin_referer('dlq_retry');
                DeadLetterQueue::retry(sanitize_text_field($_GET['retry']));
                echo '<div class="notice notice-success"><p>Job retried.</p></div>';
            }
            
            if (isset($_GET['delete'])) {
                check_admin_referer('dlq_delete');
                DeadLetterQueue::delete(sanitize_text_field($_GET['delete']));
                echo '<div class="notice notice-success"><p>Job deleted.</p></div>';
            }
        }
    );
});
```

---

## Webhook Receivers (Inbound)

### Padrão Seguro para Receber Webhooks

```php
<?php
/**
 * Webhook Receiver
 * 
 * Recebe webhooks de forma segura e processa em background
 */
class WebhookReceiver {
    
    /**
     * Verificar assinatura (HMAC-SHA256)
     * 
     * Previne timing attacks usando hash_equals
     */
    public static function verifySignature(
        string $payload,
        string $signature,
        string $secret
    ): bool {
        $expected = hash_hmac('sha256', $payload, $secret);
        
        // Constant-time comparison (previne timing attacks)
        return hash_equals($expected, $signature);
    }
    
    /**
     * Endpoint que recebe webhooks
     * 
     * Deve ser registrado como rota REST
     */
    public static function handleIncoming(WP_REST_Request $request): WP_REST_Response {
        // 1. Verificar método
        if ($request->get_method() !== 'POST') {
            return new WP_REST_Response([
                'error' => 'Method not allowed'
            ], 405);
        }
        
        // 2. Obter payload
        $payload = $request->get_body();
        
        // 3. Verificar assinatura
        $signature = $request->get_header('X-Webhook-Signature');
        $secret = defined('WEBHOOK_SECRET') ? WEBHOOK_SECRET : '';
        
        if (empty($secret)) {
            return new WP_REST_Response([
                'error' => 'Webhook secret not configured'
            ], 500);
        }
        
        if (!self::verifySignature($payload, $signature, $secret)) {
            return new WP_REST_Response([
                'error' => 'Unauthorized'
            ], 401);
        }
        
        // 4. Parse JSON
        $data = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_REST_Response([
                'error' => 'Invalid JSON'
            ], 400);
        }
        
        // 5. Verificar idempotency key (previne duplicatas)
        $idempotency_key = $request->get_header('X-Idempotency-Key');
        
        if ($idempotency_key && self::hasBeenProcessed($idempotency_key)) {
            // Retornar sucesso (já foi processado)
            return new WP_REST_Response([
                'message' => 'Already processed',
                'webhook_id' => get_option("webhook_id_{$idempotency_key}")
            ], 200);
        }
        
        // 6. Enqueue para processamento assíncrono
        $webhook_id = wp_generate_uuid4();
        
        as_enqueue_async_action('process_webhook', [
            'webhook_id' => $webhook_id,
            'payload' => $data,
            'idempotency_key' => $idempotency_key,
            'source' => $request->get_header('X-Webhook-Source'),
        ], 'webhook_processing');
        
        // 7. Salvar idempotency key
        if ($idempotency_key) {
            update_option("webhook_id_{$idempotency_key}", $webhook_id);
        }
        
        // 8. Retornar 202 (Accepted) imediatamente
        // Não esperar processamento
        return new WP_REST_Response([
            'webhook_id' => $webhook_id,
            'status' => 'accepted'
        ], 202);
    }
    
    /**
     * Verificar se webhook já foi processado
     */
    public static function hasBeenProcessed(string $idempotency_key): bool {
        return (bool)get_option("webhook_processed_{$idempotency_key}");
    }
    
    /**
     * Marcar como processado
     */
    public static function markProcessed(string $idempotency_key): void {
        set_transient(
            "webhook_processed_{$idempotency_key}",
            true,
            7 * DAY_IN_SECONDS // Manter por 7 dias
        );
    }
    
    /**
     * Handler assíncrono
     */
    public static function process(
        string $webhook_id,
        array $payload,
        ?string $idempotency_key = null,
        ?string $source = null
    ): void {
        try {
            // Log recebimento
            error_log(sprintf(
                'Processing webhook %s from %s',
                $webhook_id,
                $source ?? 'unknown'
            ));
            
            // Processar payload
            do_action('webhook_received', $payload, $source);
            
            // Processar por tipo de evento
            $event_type = $payload['event'] ?? 'unknown';
            do_action("webhook_event_{$event_type}", $payload, $source);
            
            // Marcar como processado
            if ($idempotency_key) {
                self::markProcessed($idempotency_key);
            }
            
            // Log sucesso
            error_log("Webhook {$webhook_id} processed successfully");
            
        } catch (Exception $e) {
            // Log erro
            error_log("Webhook {$webhook_id} failed: " . $e->getMessage());
            
            // Pode ser retentado pelo Action Scheduler
            throw $e;
        }
    }
}

// Registrar rota REST
add_action('rest_api_init', function() {
    register_rest_route('myapp/v1', '/webhook', [
        'methods' => 'POST',
        'callback' => [WebhookReceiver::class, 'handleIncoming'],
        'permission_callback' => '__return_true', // Signature verifica!
    ]);
});

// Registrar handler assíncrono
add_action('process_webhook', [WebhookReceiver::class, 'process']);

// Exemplo: Processar evento específico
add_action('webhook_event_order.created', function($payload, $source) {
    // Criar pedido no WordPress
    $order_id = wp_insert_post([
        'post_type' => 'shop_order',
        'post_title' => 'Order #' . $payload['order']['id'],
        'post_status' => 'publish',
    ]);
    
    // Salvar dados do pedido
    update_post_meta($order_id, '_order_data', $payload['order']);
    
    // Notificar admin
    wp_mail(
        get_option('admin_email'),
        'Novo Pedido Recebido',
        'Um novo pedido foi criado via webhook.'
    );
});
```

---

### Idempotency Keys - Implementação Completa

**Conceito:** Idempotency Keys garantem que operações idempotentes (que podem ser executadas múltiplas vezes sem efeitos colaterais) sejam processadas apenas uma vez, mesmo se a requisição for repetida.

**Quando usar:**
- APIs REST que podem ser chamadas múltiplas vezes
- Webhooks que podem ser reenviados
- Operações de pagamento
- Criação de recursos (evitar duplicatas)

#### Implementação Completa de Idempotency Keys

```php
<?php
/**
 * Idempotency Key Manager - Production-Ready
 * 
 * Gerencia chaves de idempotência para prevenir processamento duplicado
 */
class IdempotencyKeyManager {
    
    private const TABLE_NAME = 'idempotency_keys';
    private const DEFAULT_TTL = 86400; // 24 horas
    
    /**
     * Criar tabela para idempotency keys
     */
    public static function createTable(): void {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            idempotency_key varchar(255) NOT NULL,
            request_hash varchar(64) NOT NULL,
            response_code int(11) NOT NULL,
            response_body longtext NOT NULL,
            response_headers longtext DEFAULT NULL,
            created_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY idempotency_key (idempotency_key),
            KEY expires_at (expires_at),
            KEY request_hash (request_hash)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Verificar se idempotency key já foi processada
     */
    public static function get(string $idempotency_key, ?string $request_hash = null): ?array {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        self::cleanup();
        
        $where = $wpdb->prepare("idempotency_key = %s AND expires_at > NOW()", $idempotency_key);
        
        if ($request_hash) {
            $where .= $wpdb->prepare(" AND request_hash = %s", $request_hash);
        }
        
        $result = $wpdb->get_row("SELECT * FROM {$table_name} WHERE {$where}", ARRAY_A);
        
        if (!$result) {
            return null;
        }
        
        return [
            'response_code' => (int)$result['response_code'],
            'response_body' => json_decode($result['response_body'], true),
            'response_headers' => json_decode($result['response_headers'], true),
            'created_at' => $result['created_at'],
        ];
    }
    
    /**
     * Salvar resposta para idempotency key
     */
    public static function store(
        string $idempotency_key,
        string $request_hash,
        int $response_code,
        $response_body,
        array $response_headers = [],
        int $ttl = null
    ): void {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        $data = [
            'idempotency_key' => $idempotency_key,
            'request_hash' => $request_hash,
            'response_code' => $response_code,
            'response_body' => json_encode($response_body),
            'response_headers' => json_encode($response_headers),
            'created_at' => current_time('mysql'),
            'expires_at' => date('Y-m-d H:i:s', time() + $ttl),
        ];
        
        $wpdb->replace($table_name, $data);
    }
    
    /**
     * Gerar hash da requisição para validação
     */
    public static function hashRequest(string $method, string $path, $body = null): string {
        $data = [
            'method' => strtoupper($method),
            'path' => $path,
            'body' => is_string($body) ? $body : json_encode($body),
        ];
        
        return hash('sha256', json_encode($data));
    }
    
    /**
     * Middleware para REST API
     */
    public static function middleware(WP_REST_Request $request): ?WP_REST_Response {
        $idempotency_key = $request->get_header('Idempotency-Key');
        
        if (!$idempotency_key) {
            return null;
        }
        
        if (!self::isValidKey($idempotency_key)) {
            return new WP_REST_Response(['error' => 'Invalid idempotency key format'], 400);
        }
        
        $request_hash = self::hashRequest(
            $request->get_method(),
            $request->get_route(),
            $request->get_body()
        );
        
        $cached_response = self::get($idempotency_key, $request_hash);
        
        if ($cached_response) {
            $response = new WP_REST_Response(
                $cached_response['response_body'],
                $cached_response['response_code']
            );
            $response->header('X-Idempotency-Replayed', 'true');
            return $response;
        }
        
        return null;
    }
    
    private static function isValidKey(string $key): bool {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        return (bool)preg_match($pattern, $key);
    }
    
    public static function generateKey(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    public static function cleanup(): int {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        return $wpdb->query("DELETE FROM {$table_name} WHERE expires_at < NOW()");
    }
}

// Middleware REST
add_filter('rest_pre_dispatch', function($result, $server, $request) {
    if ($result !== null) return $result;
    $idempotency_response = IdempotencyKeyManager::middleware($request);
    return $idempotency_response ?? $result;
}, 10, 3);

// Salvar resposta
add_filter('rest_post_dispatch', function($response, $server, $request) {
    $idempotency_key = $request->get_header('Idempotency-Key');
    if (!$idempotency_key) return $response;
    
    $request_hash = IdempotencyKeyManager::hashRequest(
        $request->get_method(),
        $request->get_route(),
        $request->get_body()
    );
    
    IdempotencyKeyManager::store(
        $idempotency_key,
        $request_hash,
        $response->get_status(),
        $response->get_data(),
        $response->get_headers()
    );
    
    $response->header('X-Idempotency-Key', $idempotency_key);
    return $response;
}, 10, 3);

register_activation_hook(__FILE__, [IdempotencyKeyManager::class, 'createTable']);
```

**Exemplo de uso no cliente:**

```javascript
// Cliente JavaScript
async function createOrder(orderData) {
    const idempotencyKey = generateUUID(); // UUID v4
    
    const response = await fetch('/wp-json/myapp/v1/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Idempotency-Key': idempotencyKey,
        },
        body: JSON.stringify(orderData),
    });
    
    // Se a requisição for repetida com mesma chave, retorna resposta em cache
    if (response.headers.get('X-Idempotency-Replayed') === 'true') {
        console.log('Response was replayed from cache');
    }
    
    return response.json();
}
```

---

## Integração com Docker

### Docker Compose com Workers

```yaml
version: '3.8'

services:
  wordpress:
    image: wordpress:php8.2-fpm
    volumes:
      - ./:/var/www/html
      - ./wp-config.php:/var/www/html/wp-config.php
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: ${DB_NAME}
    depends_on:
      - mysql
    networks:
      - wordpress_network

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - wordpress_network

  # Action Scheduler Worker
  scheduler:
    image: wordpress:php8.2-cli
    volumes:
      - ./:/var/www/html
      - ./wp-config.php:/var/www/html/wp-config.php
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: ${DB_NAME}
    depends_on:
      - mysql
    networks:
      - wordpress_network
    command: >
      sh -c "
        while true; do
          wp action-scheduler run --batches=1 --batch-size=50
          sleep 60
        done
      "
    restart: unless-stopped

  # Queue Worker (alta prioridade)
  worker-high:
    image: wordpress:php8.2-cli
    volumes:
      - ./:/var/www/html
      - ./wp-config.php:/var/www/html/wp-config.php
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: ${DB_NAME}
    depends_on:
      - mysql
    networks:
      - wordpress_network
    command: >
      sh -c "
        while true; do
          wp action-scheduler run --batches=1 --batch-size=10 --group=high_priority
          sleep 30
        done
      "
    restart: unless-stopped

  # Queue Worker (baixa prioridade)
  worker-low:
    image: wordpress:php8.2-cli
    volumes:
      - ./:/var/www/html
      - ./wp-config.php:/var/www/html/wp-config.php
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: ${DB_USER}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: ${DB_NAME}
    depends_on:
      - mysql
    networks:
      - wordpress_network
    command: >
      sh -c "
        while true; do
          wp action-scheduler run --batches=1 --batch-size=5 --group=low_priority
          sleep 60
        done
      "
    restart: unless-stopped

volumes:
  db_data:

networks:
  wordpress_network:
    driver: bridge
```

### Supervisord para Gerenciar Workers

```ini
; /etc/supervisor/conf.d/wordpress-workers.conf

[program:wp-scheduler]
process_name=%(program_name)s_%(process_num)02d
command=/usr/local/bin/wp action-scheduler run --batches=1 --batch-size=50
directory=/var/www/html
user=www-data
numprocs=1
autostart=true
autorestart=true
startsecs=10
stopwaitsecs=10
stdout_logfile=/var/log/supervisor/wp-scheduler.log
stderr_logfile=/var/log/supervisor/wp-scheduler.err

[program:wp-worker-high]
process_name=%(program_name)s_%(process_num)02d
command=/usr/local/bin/wp action-scheduler run --batches=1 --batch-size=10 --group=high_priority
directory=/var/www/html
user=www-data
numprocs=2
autostart=true
autorestart=true
startsecs=10
stopwaitsecs=10
stdout_logfile=/var/log/supervisor/wp-worker-high.log
stderr_logfile=/var/log/supervisor/wp-worker-high.err

[program:wp-worker-low]
process_name=%(program_name)s_%(process_num)02d
command=/usr/local/bin/wp action-scheduler run --batches=1 --batch-size=5 --group=low_priority
directory=/var/www/html
user=www-data
numprocs=1
autostart=true
autorestart=true
startsecs=10
stopwaitsecs=10
stdout_logfile=/var/log/supervisor/wp-worker-low.log
stderr_logfile=/var/log/supervisor/wp-worker-low.err
```

### Health Checks

```dockerfile
# Dockerfile com health check
FROM wordpress:php8.2-cli

# Health check para worker
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD wp action-scheduler status || exit 1

WORKDIR /var/www/html
```

---

## Monitoramento em Produção

### Monitoring com Prometheus/Grafana

**Conceito:** Expor métricas em formato Prometheus para visualização em Grafana, permitindo monitoramento avançado de queues e jobs.

#### Expor Métricas Prometheus

```php
<?php
/**
 * Prometheus Metrics Exporter para Action Scheduler
 */
class PrometheusMetricsExporter {
    
    /**
     * Coletar métricas do Action Scheduler
     */
    public static function collectMetrics(): array {
        global $wpdb;
        
        $metrics = [];
        
        // Contar ações por status
        $statuses = [
            'pending' => ActionScheduler_Store::STATUS_PENDING,
            'in-progress' => ActionScheduler_Store::STATUS_IN_PROGRESS,
            'complete' => ActionScheduler_Store::STATUS_COMPLETE,
            'failed' => ActionScheduler_Store::STATUS_FAILED,
            'canceled' => ActionScheduler_Store::STATUS_CANCELED,
        ];
        
        foreach ($statuses as $label => $status) {
            $count = as_count_scheduled_actions(['status' => $status]);
            $metrics[] = [
                'name' => 'action_scheduler_actions_total',
                'labels' => ['status' => $label],
                'value' => $count,
                'type' => 'counter',
            ];
        }
        
        // Métricas por grupo
        $groups = $wpdb->get_col(
            "SELECT DISTINCT group_slug FROM {$wpdb->prefix}actionscheduler_groups"
        );
        
        foreach ($groups as $group) {
            $pending = as_count_scheduled_actions([
                'status' => ActionScheduler_Store::STATUS_PENDING,
                'group' => $group,
            ]);
            
            $metrics[] = [
                'name' => 'action_scheduler_actions_by_group',
                'labels' => ['group' => $group, 'status' => 'pending'],
                'value' => $pending,
                'type' => 'gauge',
            ];
        }
        
        // Ações agendadas para próximas horas
        $next_hour = time() + 3600;
        $scheduled_soon = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}actionscheduler_actions 
             WHERE status = %s AND scheduled_date_gmt <= %s",
            ActionScheduler_Store::STATUS_PENDING,
            date('Y-m-d H:i:s', $next_hour)
        ));
        
        $metrics[] = [
            'name' => 'action_scheduler_actions_scheduled_next_hour',
            'labels' => [],
            'value' => (int)$scheduled_soon,
            'type' => 'gauge',
        ];
        
        // Tempo médio de execução (últimas 100 ações completadas)
        $avg_duration = $wpdb->get_var(
            "SELECT AVG(TIMESTAMPDIFF(SECOND, scheduled_date_gmt, completed_date_gmt))
             FROM {$wpdb->prefix}actionscheduler_actions
             WHERE status = 'complete'
             ORDER BY completed_date_gmt DESC
             LIMIT 100"
        );
        
        if ($avg_duration) {
            $metrics[] = [
                'name' => 'action_scheduler_action_duration_seconds',
                'labels' => ['quantile' => 'mean'],
                'value' => (float)$avg_duration,
                'type' => 'histogram',
            ];
        }
        
        // Métricas de Dead Letter Queue
        if (class_exists('DeadLetterQueue')) {
            $dlq_stats = DeadLetterQueue::getStats();
            
            $metrics[] = [
                'name' => 'dead_letter_queue_jobs_total',
                'labels' => [],
                'value' => $dlq_stats['total'],
                'type' => 'gauge',
            ];
            
            $metrics[] = [
                'name' => 'dead_letter_queue_jobs_recent_24h',
                'labels' => [],
                'value' => $dlq_stats['recent_24h'],
                'type' => 'counter',
            ];
        }
        
        return $metrics;
    }
    
    /**
     * Formatar métricas no formato Prometheus
     */
    public static function formatPrometheus(array $metrics): string {
        $output = [];
        
        // Agrupar por nome
        $grouped = [];
        foreach ($metrics as $metric) {
            $name = $metric['name'];
            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'type' => $metric['type'],
                    'metrics' => [],
                ];
            }
            $grouped[$name]['metrics'][] = $metric;
        }
        
        foreach ($grouped as $name => $group) {
            // Adicionar tipo
            $output[] = "# TYPE {$name} {$group['type']}";
            
            // Adicionar métricas
            foreach ($group['metrics'] as $metric) {
                $labels = [];
                foreach ($metric['labels'] as $key => $value) {
                    $labels[] = sprintf('%s="%s"', $key, addslashes($value));
                }
                
                $label_str = !empty($labels) ? '{' . implode(',', $labels) . '}' : '';
                $output[] = sprintf('%s%s %s', $name, $label_str, $metric['value']);
            }
            
            $output[] = '';
        }
        
        return implode("\n", $output);
    }
    
    /**
     * Endpoint REST para métricas Prometheus
     */
    public static function metricsEndpoint(): WP_REST_Response {
        $metrics = self::collectMetrics();
        $formatted = self::formatPrometheus($metrics);
        
        return new WP_REST_Response($formatted, 200, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8',
        ]);
    }
}

// Registrar endpoint
add_action('rest_api_init', function() {
    register_rest_route('prometheus/v1', '/metrics', [
        'methods' => 'GET',
        'callback' => [PrometheusMetricsExporter::class, 'metricsEndpoint'],
        'permission_callback' => function() {
            // Permitir acesso apenas de IPs específicos ou com autenticação
            $allowed_ips = apply_filters('prometheus_allowed_ips', ['127.0.0.1']);
            $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
            
            return in_array($client_ip, $allowed_ips) || current_user_can('manage_options');
        },
    ]);
});
```

#### Configuração Prometheus

```yaml
# prometheus.yml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

scrape_configs:
  - job_name: 'wordpress-action-scheduler'
    static_configs:
      - targets: ['wordpress.local:80']
    metrics_path: '/wp-json/prometheus/v1/metrics'
    scrape_interval: 30s
```

#### Dashboard Grafana

```json
{
  "dashboard": {
    "title": "Action Scheduler Monitoring",
    "panels": [
      {
        "title": "Actions by Status",
        "type": "graph",
        "targets": [
          {
            "expr": "action_scheduler_actions_total",
            "legendFormat": "{{status}}"
          }
        ]
      },
      {
        "title": "Pending Actions",
        "type": "stat",
        "targets": [
          {
            "expr": "action_scheduler_actions_total{status=\"pending\"}"
          }
        ]
      },
      {
        "title": "Failed Actions",
        "type": "stat",
        "targets": [
          {
            "expr": "action_scheduler_actions_total{status=\"failed\"}"
          }
        ],
        "thresholds": {
          "steps": [
            {"value": 0, "color": "green"},
            {"value": 10, "color": "yellow"},
            {"value": 50, "color": "red"}
          ]
        }
      },
      {
        "title": "DLQ Jobs",
        "type": "graph",
        "targets": [
          {
            "expr": "dead_letter_queue_jobs_total"
          }
        ]
      },
      {
        "title": "Average Action Duration",
        "type": "graph",
        "targets": [
          {
            "expr": "action_scheduler_action_duration_seconds"
          }
        ]
      }
    ]
  }
}
```

#### Alertas Prometheus

```yaml
# alerts.yml
groups:
  - name: action_scheduler
    rules:
      - alert: HighPendingActions
        expr: action_scheduler_actions_total{status="pending"} > 1000
        for: 5m
        annotations:
          summary: "High number of pending actions"
          description: "{{ $value }} actions are pending"
      
      - alert: HighFailedActions
        expr: rate(action_scheduler_actions_total{status="failed"}[5m]) > 10
        for: 5m
        annotations:
          summary: "High failure rate"
          description: "{{ $value }} actions failing per minute"
      
      - alert: DLQJobsIncreasing
        expr: rate(dead_letter_queue_jobs_recent_24h[1h]) > 5
        for: 10m
        annotations:
          summary: "DLQ jobs increasing rapidly"
          description: "{{ $value }} jobs moved to DLQ per hour"
```

### Queue Monitor

```php
<?php
/**
 * Queue Monitor
 * 
 * Monitora status das queues e ações
 */
class QueueMonitor {
    
    /**
     * WP-CLI command para status
     */
    public static function status(): void {
        if (!class_exists('WP_CLI')) {
            return;
        }
        
        $pending = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_PENDING
        ]);
        
        $running = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_RUNNING
        ]);
        
        $failed = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_FAILED
        ]);
        
        $completed = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_COMPLETE
        ]);
        
        WP_CLI::log("Queue Status:");
        WP_CLI::log("  Pending: $pending");
        WP_CLI::log("  Running: $running");
        WP_CLI::log("  Failed: $failed");
        WP_CLI::log("  Completed: $completed");
        
        // Alertar se há muitos jobs falhados
        if ($failed > 10) {
            WP_CLI::warning("High number of failed jobs: $failed");
        }
        
        // Alertar se há muitos jobs pendentes
        if ($pending > 100) {
            WP_CLI::warning("High number of pending jobs: $pending");
        }
    }
    
    /**
     * Integração com Sentry para erros
     */
    public static function trackError(Exception $e, array $context = []): void {
        if (function_exists('Sentry\\captureException')) {
            Sentry\captureException($e, [
                'tags' => array_merge(['component' => 'queue'], $context),
                'level' => 'error',
            ]);
        }
        
        // Log local também
        error_log(sprintf(
            'Queue Error: %s | Context: %s',
            $e->getMessage(),
            json_encode($context)
        ));
    }
    
    /**
     * Health check endpoint
     */
    public static function healthCheck(): array {
        $pending = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_PENDING,
            'claimed' => false,
        ]);
        
        $failed = as_count_scheduled_actions([
            'status' => ActionScheduler_Store::STATUS_FAILED
        ]);
        
        $healthy = ($pending < 100 && $failed < 10);
        
        return [
            'healthy' => $healthy,
            'pending' => $pending,
            'failed' => $failed,
            'timestamp' => time(),
        ];
    }
}

// Registrar WP-CLI command
if (class_exists('WP_CLI')) {
    WP_CLI::add_command('queue status', [QueueMonitor::class, 'status']);
}

// Health check endpoint REST
add_action('rest_api_init', function() {
    register_rest_route('myapp/v1', '/health/queue', [
        'methods' => 'GET',
        'callback' => [QueueMonitor::class, 'healthCheck'],
        'permission_callback' => '__return_true',
    ]);
});
```

### Dashboard Widget

```php
<?php
/**
 * Adicionar widget ao dashboard
 */
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'queue_monitor_widget',
        'Queue Monitor',
        function() {
            $status = QueueMonitor::healthCheck();
            
            $health_class = $status['healthy'] ? 'healthy' : 'unhealthy';
            $health_text = $status['healthy'] ? 'Healthy' : 'Unhealthy';
            
            echo "<div class='queue-status {$health_class}'>";
            echo "<h3>Status: <span>{$health_text}</span></h3>";
            echo "<p><strong>Pending:</strong> {$status['pending']}</p>";
            echo "<p><strong>Failed:</strong> {$status['failed']}</p>";
            echo "<p><strong>Last Check:</strong> " . date('Y-m-d H:i:s', $status['timestamp']) . "</p>";
            echo "</div>";
            
            // Estilos inline
            echo "<style>
                .queue-status.healthy { border-left: 4px solid #46b450; }
                .queue-status.unhealthy { border-left: 4px solid #dc3232; }
                .queue-status h3 { margin-top: 0; }
            </style>";
        }
    );
});
```

---

## Error Handling em Async Jobs

### Error Handling Patterns para Background Jobs

**Padrão 1: Retry com Exponential Backoff**

```php
<?php
/**
 * Job handler com retry automático
 */
class EmailJobHandler {
    
    private const MAX_RETRIES = 3;
    private const BASE_DELAY = 60; // 1 minuto
    
    public function handle(array $args): void {
        $email_id = $args['email_id'];
        $attempt = $args['attempt'] ?? 1;
        
        try {
            // Processar email
            $this->sendEmail($email_id);
            
            // Sucesso - logar
            error_log(sprintf('Email %d sent successfully', $email_id));
            
        } catch (TransientException $e) {
            // Erro temporário - retentar
            if ($attempt < self::MAX_RETRIES) {
                $delay = self::BASE_DELAY * pow(2, $attempt - 1); // Exponential backoff
                
                error_log(sprintf(
                    'Email %d failed (attempt %d/%d), retrying in %d seconds: %s',
                    $email_id,
                    $attempt,
                    self::MAX_RETRIES,
                    $delay,
                    $e->getMessage()
                ));
                
                // Reagendar com delay
                as_schedule_single_action(
                    time() + $delay,
                    'send_email',
                    [
                        'email_id' => $email_id,
                        'attempt' => $attempt + 1,
                    ],
                    'email_processing'
                );
                
            } else {
                // Máximo de tentativas atingido - mover para DLQ
                $this->moveToDeadLetterQueue($email_id, $e);
            }
            
        } catch (PermanentException $e) {
            // Erro permanente - não retentar, mover para DLQ imediatamente
            error_log(sprintf(
                'Email %d failed permanently: %s',
                $email_id,
                $e->getMessage()
            ));
            
            $this->moveToDeadLetterQueue($email_id, $e);
            
        } catch (Exception $e) {
            // Erro inesperado - logar e mover para DLQ
            error_log(sprintf(
                'Unexpected error sending email %d: %s',
                $email_id,
                $e->getMessage()
            ));
            
            $this->moveToDeadLetterQueue($email_id, $e);
        }
    }
    
    private function moveToDeadLetterQueue(int $email_id, Exception $e): void {
        if (class_exists('DeadLetterQueue')) {
            DeadLetterQueue::moveToDeadLetter([
                'id' => $email_id,
                'handler' => 'send_email',
                'payload' => ['email_id' => $email_id],
                'attempts' => self::MAX_RETRIES,
            ], $e, 'email_processing');
        }
    }
}
```

**Padrão 2: Circuit Breaker para Jobs**

```php
<?php
/**
 * Circuit Breaker para prevenir sobrecarga em serviços externos
 */
class CircuitBreaker {
    
    private const FAILURE_THRESHOLD = 5;
    private const TIMEOUT = 60; // 1 minuto
    private const HALF_OPEN_MAX_CALLS = 3;
    
    private string $service_name;
    private int $failure_count = 0;
    private ?int $last_failure_time = null;
    private string $state = 'closed'; // closed, open, half-open
    
    public function __construct(string $service_name) {
        $this->service_name = $service_name;
    }
    
    /**
     * Executar operação com circuit breaker
     */
    public function execute(callable $operation) {
        // Verificar estado do circuit breaker
        $this->checkState();
        
        if ($this->state === 'open') {
            throw new CircuitBreakerOpenException(
                "Circuit breaker is open for service: {$this->service_name}"
            );
        }
        
        try {
            $result = $operation();
            
            // Sucesso - reset contador
            $this->onSuccess();
            
            return $result;
            
        } catch (Exception $e) {
            // Falha - incrementar contador
            $this->onFailure();
            throw $e;
        }
    }
    
    private function checkState(): void {
        if ($this->state === 'open') {
            // Verificar se timeout expirou
            if ($this->last_failure_time && 
                (time() - $this->last_failure_time) > self::TIMEOUT) {
                $this->state = 'half-open';
                $this->failure_count = 0;
            }
        }
    }
    
    private function onSuccess(): void {
        if ($this->state === 'half-open') {
            // Sucesso em half-open - fechar circuit breaker
            $this->state = 'closed';
        }
        
        $this->failure_count = 0;
    }
    
    private function onFailure(): void {
        $this->failure_count++;
        $this->last_failure_time = time();
        
        if ($this->failure_count >= self::FAILURE_THRESHOLD) {
            $this->state = 'open';
            
            error_log(sprintf(
                'Circuit breaker opened for service: %s (failures: %d)',
                $this->service_name,
                $this->failure_count
            ));
        }
    }
}

// Uso em job handler
class ExternalAPICallHandler {
    private CircuitBreaker $circuitBreaker;
    
    public function __construct() {
        $this->circuitBreaker = new CircuitBreaker('external_api');
    }
    
    public function handle(array $args): void {
        try {
            $this->circuitBreaker->execute(function() use ($args) {
                return wp_remote_post('https://api.example.com/webhook', [
                    'body' => json_encode($args['data']),
                    'timeout' => 10,
                ]);
            });
            
        } catch (CircuitBreakerOpenException $e) {
            // Circuit breaker aberto - reagendar job para depois
            error_log('Circuit breaker open, rescheduling job');
            
            as_schedule_single_action(
                time() + 300, // 5 minutos
                'call_external_api',
                $args,
                'api_calls'
            );
            
        } catch (Exception $e) {
            // Outro erro - tratar normalmente
            throw $e;
        }
    }
}
```

**Padrão 3: Error Recovery e Compensation**

```php
<?php
/**
 * Job com compensação em caso de falha
 */
class OrderProcessingJob {
    
    public function handle(array $args): void {
        $order_id = $args['order_id'];
        $compensation_stack = [];
        
        try {
            // 1. Reservar estoque
            $this->reserveInventory($order_id);
            $compensation_stack[] = ['action' => 'release_inventory', 'order_id' => $order_id];
            
            // 2. Processar pagamento
            $this->processPayment($order_id);
            $compensation_stack[] = ['action' => 'refund_payment', 'order_id' => $order_id];
            
            // 3. Enviar confirmação
            $this->sendConfirmation($order_id);
            
            // Sucesso - limpar compensation stack
            $compensation_stack = [];
            
        } catch (Exception $e) {
            // Falha - executar compensação
            error_log(sprintf(
                'Order processing failed for order %d: %s',
                $order_id,
                $e->getMessage()
            ));
            
            $this->compensate($compensation_stack);
            
            // Re-lançar exceção para que Action Scheduler saiba que falhou
            throw $e;
        }
    }
    
    /**
     * Executar compensação (rollback)
     */
    private function compensate(array $compensation_stack): void {
        // Executar compensações na ordem reversa
        $compensation_stack = array_reverse($compensation_stack);
        
        foreach ($compensation_stack as $compensation) {
            try {
                switch ($compensation['action']) {
                    case 'release_inventory':
                        $this->releaseInventory($compensation['order_id']);
                        break;
                        
                    case 'refund_payment':
                        $this->refundPayment($compensation['order_id']);
                        break;
                }
                
            } catch (Exception $e) {
                // Log erro na compensação mas continuar
                error_log(sprintf(
                    'Compensation failed: %s - %s',
                    $compensation['action'],
                    $e->getMessage()
                ));
            }
        }
    }
}
```

**Padrão 4: Error Classification e Handling**

```php
<?php
/**
 * Classificador de erros para determinar estratégia de tratamento
 */
class ErrorClassifier {
    
    /**
     * Classificar erro e determinar ação
     */
    public static function classify(Exception $e): ErrorClassification {
        // Erros temporários (rede, timeout, serviço indisponível)
        if ($e instanceof WP_Error) {
            $code = $e->get_error_code();
            
            if (in_array($code, ['http_request_failed', 'timeout', 'connection_refused'])) {
                return new ErrorClassification('transient', 'retry');
            }
        }
        
        // Erros de validação (não retentar)
        if ($e instanceof ValidationException) {
            return new ErrorClassification('permanent', 'dlq');
        }
        
        // Erros de permissão (não retentar)
        if ($e instanceof PermissionException) {
            return new ErrorClassification('permanent', 'dlq');
        }
        
        // Erros de negócio (não retentar)
        if ($e instanceof BusinessRuleException) {
            return new ErrorClassification('permanent', 'dlq');
        }
        
        // Erros de banco de dados (pode ser temporário)
        if ($e instanceof DatabaseException) {
            // Verificar se é deadlock ou timeout
            if (strpos($e->getMessage(), 'Deadlock') !== false ||
                strpos($e->getMessage(), 'Lock wait timeout') !== false) {
                return new ErrorClassification('transient', 'retry');
            }
            
            return new ErrorClassification('permanent', 'dlq');
        }
        
        // Padrão: erro desconhecido - tratar como permanente
        return new ErrorClassification('permanent', 'dlq');
    }
}

class ErrorClassification {
    private string $type; // transient, permanent
    private string $action; // retry, dlq, ignore
    
    public function __construct(string $type, string $action) {
        $this->type = $type;
        $this->action = $action;
    }
    
    public function isTransient(): bool {
        return $this->type === 'transient';
    }
    
    public function shouldRetry(): bool {
        return $this->action === 'retry';
    }
    
    public function shouldMoveToDLQ(): bool {
        return $this->action === 'dlq';
    }
}

// Uso em job handler
class GenericJobHandler {
    public function handle(array $args): void {
        try {
            // Executar operação
            $this->executeOperation($args);
            
        } catch (Exception $e) {
            $classification = ErrorClassifier::classify($e);
            
            if ($classification->shouldRetry()) {
                // Reagendar com retry
                $this->scheduleRetry($args, $e);
                
            } elseif ($classification->shouldMoveToDLQ()) {
                // Mover para DLQ
                $this->moveToDLQ($args, $e);
                
            } else {
                // Ignorar (log apenas)
                error_log('Job error ignored: ' . $e->getMessage());
            }
        }
    }
}
```

---

## Case Studies Práticos

### Case Study 1: E-commerce Order Processing

```php
<?php
/**
 * Processamento de Pedidos em E-commerce
 * 
 * Quando um pedido é criado:
 * 1. Validar estoque (assíncrono)
 * 2. Processar pagamento (assíncrono)
 * 3. Enviar emails (assíncrono)
 * 4. Sincronizar com ERP (assíncrono)
 * 5. Atualizar analytics (assíncrono)
 */
class OrderProcessor {
    
    public static function processOrder(int $order_id): void {
        // Enqueue todas as tarefas
        as_enqueue_async_action('validate_order_stock', [$order_id], 'order_processing');
        as_enqueue_async_action('process_order_payment', [$order_id], 'order_processing');
        as_enqueue_async_action('send_order_emails', [$order_id], 'order_processing');
        as_enqueue_async_action('sync_order_erp', [$order_id], 'order_processing');
        as_enqueue_async_action('update_order_analytics', [$order_id], 'order_processing');
    }
}

// Quando pedido é criado
add_action('woocommerce_new_order', [OrderProcessor::class, 'processOrder']);

// Handlers
add_action('validate_order_stock', function($order_id) {
    $order = wc_get_order($order_id);
    
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $quantity = $item->get_quantity();
        
        if (!has_enough_stock($product_id, $quantity)) {
            // Cancelar pedido
            $order->update_status('cancelled', 'Estoque insuficiente');
            return;
        }
    }
    
    update_post_meta($order_id, '_stock_validated', true);
});

add_action('process_order_payment', function($order_id) {
    $order = wc_get_order($order_id);
    
    // Processar pagamento via gateway
    $payment_result = process_payment_gateway($order);
    
    if ($payment_result['success']) {
        $order->update_status('processing', 'Pagamento processado');
        update_post_meta($order_id, '_payment_processed', true);
    } else {
        $order->update_status('failed', $payment_result['message']);
    }
});
```

### Case Study 2: Media Processing Pipeline

```php
<?php
/**
 * Pipeline de Processamento de Mídia
 * 
 * Quando imagem é enviada:
 * 1. Gerar thumbnails (assíncrono)
 * 2. Otimizar imagem (assíncrono)
 * 3. Upload para CDN (assíncrono)
 * 4. Gerar metadados (assíncrono)
 */
class MediaProcessor {
    
    public static function processMedia(int $attachment_id): void {
        // Pipeline sequencial
        as_enqueue_async_action('generate_thumbnails', [$attachment_id], 'media_processing');
    }
}

// Quando mídia é enviada
add_action('add_attachment', [MediaProcessor::class, 'processMedia']);

// Pipeline sequencial
add_action('generate_thumbnails', function($attachment_id) {
    generate_image_sizes($attachment_id);
    
    // Próximo passo
    as_enqueue_async_action('optimize_image', [$attachment_id], 'media_processing');
});

add_action('optimize_image', function($attachment_id) {
    optimize_image_file($attachment_id);
    
    // Próximo passo
    as_enqueue_async_action('upload_to_cdn', [$attachment_id], 'media_processing');
});

add_action('upload_to_cdn', function($attachment_id) {
    upload_to_cdn($attachment_id);
    
    // Próximo passo
    as_enqueue_async_action('generate_metadata', [$attachment_id], 'media_processing');
});

add_action('generate_metadata', function($attachment_id) {
    generate_image_metadata($attachment_id);
    
    // Pipeline completo
    update_post_meta($attachment_id, '_processing_complete', true);
});
```

---

## Resumo e Próximos Passos

### O Que Você Aprendeu

✅ **Por que usar Async Jobs** - Evitar bloqueios em requisições HTTP  
✅ **Limitações do WP-Cron** - Por que não usar em produção  
✅ **Action Scheduler** - Solução production-ready  
✅ **Queue Patterns** - Simple, Priority, Dead Letter Queue  
✅ **Webhook Receivers** - Receber webhooks de forma segura  
✅ **Docker Integration** - Workers em containers  
✅ **Monitoramento** - Health checks e dashboards  

### Próximos Passos

1. **Implementar Action Scheduler** no seu projeto
2. **Criar workers Docker** para processamento
3. **Implementar monitoramento** de queues
4. **Testar em staging** antes de produção
5. **Documentar** padrões específicos do seu projeto

### Recursos Adicionais

- [Action Scheduler Documentation](https://actionscheduler.org/)
- [WooCommerce Action Scheduler](https://github.com/woocommerce/action-scheduler)
- [WP-CLI Action Scheduler Commands](https://github.com/woocommerce/action-scheduler-cli)

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 14](./014-WordPress-Fase-14-Implantacao-DevOps.md) | [Fase 16 →](./015-WordPress-Fase-16-Topicos-Complementares-Avancados.md)
