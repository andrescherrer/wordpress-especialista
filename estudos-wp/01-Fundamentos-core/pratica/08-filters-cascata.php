<?php
/**
 * REFERÊNCIA RÁPIDA – Múltiplos filters em cascata
 *
 * add_filter('hook', callback, prioridade, num_args);
 * Ordem: prioridade menor executa primeiro; mesmo valor = ordem de registro.
 * remove_filter('hook', callback, prioridade); remove_all_filters('hook');
 *
 * Fonte: 001-WordPress-Fase-1-Fundamentos-Core.md
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if (!defined('ABSPATH')) {
    exit;
}

// ========== Exemplo: filters em cascata (prioridade) ==========

add_filter('estudos_wp_titulo_exemplo', function ($titulo) {
    return '[1] ' . $titulo; // prioridade 10 (padrão)
}, 10, 1);

add_filter('estudos_wp_titulo_exemplo', function ($titulo) {
    return $titulo . ' [2]'; // prioridade 20: roda depois
}, 20, 1);

add_filter('estudos_wp_titulo_exemplo', function ($titulo) {
    return strtoupper($titulo); // prioridade 5: roda primeiro
}, 5, 1);

// Uso: apply_filters('estudos_wp_titulo_exemplo', 'Meu título')
// Resultado: [1] MEU TÍTULO [2]  (5 → 10 → 20)

// ========== Remoção de filter ==========

$callback_removivel = function ($x) {
    return $x . '_removido';
};
add_filter('estudos_wp_filtro_removivel', $callback_removivel, 10, 1);
// Em outro lugar (ex.: antes de usar):
// remove_filter('estudos_wp_filtro_removivel', $callback_removivel, 10);

// ========== Filter que recebe mais de um argumento ==========

add_filter('estudos_wp_conteudo_exemplo', function ($conteudo, $post_id, $contexto) {
    if ($contexto === 'excerpt') {
        return wp_trim_words($conteudo, 20);
    }
    return $conteudo;
}, 10, 3); // 3 = número de argumentos

// Uso: apply_filters('estudos_wp_conteudo_exemplo', $content, get_the_ID(), 'full');
