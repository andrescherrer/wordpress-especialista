<?php
/**
 * REFERÊNCIA RÁPIDA – Recurring (as_schedule_recurring_action) e cancelar série
 *
 * as_schedule_recurring_action($start, $interval_seconds, 'hook', $args, 'group').
 * Cancelar: as_unschedule_action('hook', $args, 'group'); as_unschedule_all_actions('hook', $args, 'group') para toda a série.
 * Fonte: 016-WordPress-Fase-16-Jobs-Assincronos-Background.md
 *
 * @package EstudosWP
 * @subpackage 16-Jobs-Assincronos-Background
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('as_schedule_recurring_action')) {
    return;
}

// Agendar execução a cada 1 hora
$start = time();
$interval = HOUR_IN_SECONDS;
as_schedule_recurring_action(
    $start,
    $interval,
    'estudos_wp_limpar_cache_hora',
    [],
    'estudos_wp_group'
);

add_action('estudos_wp_limpar_cache_hora', function () {
    delete_transient('estudos_wp_feed_cache');
    error_log('Cache limpo (recurring)');
});

// Cancelar uma próxima ocorrência (mesmos args)
// as_unschedule_action('estudos_wp_limpar_cache_hora', [], 'estudos_wp_group');

// Cancelar todas as ocorrências do hook (e args)
// as_unschedule_all_actions('estudos_wp_limpar_cache_hora', [], 'estudos_wp_group');
