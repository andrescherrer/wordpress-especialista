<?php
/**
 * Exemplo 01: Hooks – Actions e Filters
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_action('hook', 'callback', prioridade, num_args);  // prioridade padrão 10
 *   add_filter('hook', 'callback', prioridade, num_args);  // filter RETORNA valor
 *   remove_action('hook', 'callback', prioridade);         // mesma prioridade do add
 *   do_action('meu_hook', $arg);  apply_filters('meu_filter', $valor);
 *   Menor prioridade = executa antes. Não use remove_all_* em hooks core.
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// ACTIONS – Executar código em um ponto específico
// =============================================================================

// Action simples: adicionar comentário no rodapé
add_action( 'wp_footer', 'estudos_wp_footer_credits', 10 );

function estudos_wp_footer_credits() {
	echo '<!-- Desenvolvido com WordPress - Estudos Fase 1 -->';
}

// Action com prioridade: ordem de execução (menor = primeiro)
add_action( 'wp_footer', 'estudos_wp_footer_primeiro', 5 );
add_action( 'wp_footer', 'estudos_wp_footer_segundo', 10 );
add_action( 'wp_footer', 'estudos_wp_footer_terceiro', 15 );

function estudos_wp_footer_primeiro() {
	echo '<!-- 1. Prioridade 5 -->';
}

function estudos_wp_footer_segundo() {
	echo '<!-- 2. Prioridade 10 (padrão) -->';
}

function estudos_wp_footer_terceiro() {
	echo '<!-- 3. Prioridade 15 -->';
}

// Action com argumentos: save_post passa $post_id e $post
add_action( 'save_post', 'estudos_wp_ao_salvar_post', 10, 2 );

/**
 * Callback que recebe os argumentos do hook save_post.
 *
 * @param int     $post_id ID do post.
 * @param WP_Post $post    Objeto do post.
 */
function estudos_wp_ao_salvar_post( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	// Exemplo: log ou atualizar meta ao salvar
	// update_post_meta( $post_id, '_ultima_edicao', current_time( 'mysql' ) );
}

// =============================================================================
// FILTERS – Modificar e retornar valor
// =============================================================================

// Filter simples: modificar o título
add_filter( 'the_title', 'estudos_wp_titulo_prefixo', 10, 2 );

function estudos_wp_titulo_prefixo( $title, $post_id = null ) {
	if ( ! is_admin() && ! empty( $title ) ) {
		return '📌 ' . $title;
	}
	return $title;
}

// Filter no conteúdo: adicionar aviso no final
add_filter( 'the_content', 'estudos_wp_conteudo_aviso', 10, 1 );

function estudos_wp_conteudo_aviso( $content ) {
	if ( ! is_singular( 'post' ) ) {
		return $content;
	}
	$aviso = '<p class="estudos-wp-aviso"><small>Conteúdo exibido via filter the_content (exemplo Fase 1).</small></p>';
	return $content . $aviso;
}

// Filter com prioridade: executar antes de outros
add_filter( 'the_excerpt', 'estudos_wp_excerpt_limpar', 5, 1 );

function estudos_wp_excerpt_limpar( $excerpt ) {
	// Remove caracteres extras; outros filters ainda rodam depois (prioridade 10+)
	return trim( preg_replace( '/\s+/', ' ', $excerpt ) );
}

// =============================================================================
// REMOVER HOOKS (sempre específico, nunca remove_all_* em hooks core)
// =============================================================================

// Remover versão do WordPress do <head> (exemplo de remove_action)
add_action( 'init', 'estudos_wp_remover_wp_generator' );

function estudos_wp_remover_wp_generator() {
	remove_action( 'wp_head', 'wp_generator', 1 );
}

// =============================================================================
// DISPARAR HOOK CUSTOMIZADO (para outros plugins/tema reagirem)
// =============================================================================

add_action( 'wp_footer', 'estudos_wp_disparar_hook_customizado', 20 );

function estudos_wp_disparar_hook_customizado() {
	$dados = array(
		'tempo' => current_time( 'mysql' ),
		'url'   => get_permalink(),
	);
	do_action( 'estudos_wp_antes_fechar_body', $dados );
}

// Quem quiser reagir ao nosso hook:
// add_action( 'estudos_wp_antes_fechar_body', function( $dados ) {
//     error_log( 'Hook customizado: ' . print_r( $dados, true ) );
// }, 10, 1 );
