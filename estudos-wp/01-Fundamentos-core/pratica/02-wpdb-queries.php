<?php
/**
 * Exemplo 02: WordPress Database API ($wpdb)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   global $wpdb;  →  $wpdb->posts, $wpdb->postmeta (já com prefixo)
 *   get_results(), get_row(), get_var(), get_col()
 *   SEMPRE: $wpdb->prepare("SELECT ... WHERE ID = %d", $id);  // %d int, %s string, %f float
 *   insert($tabela, $dados, ['%s','%d']);  →  $wpdb->insert_id
 *   update($tabela, $dados, $where, $format, $where_format);  delete($tabela, $where, $where_format);
 *   Transação: START TRANSACTION / COMMIT / ROLLBACK; não misturar com wp_insert_post().
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna os últimos N posts publicados (exemplo get_results).
 *
 * @param int $quantidade Número de posts.
 * @return array Lista de objetos WP_Post-like (stdClass).
 */
function estudos_wp_wpdb_ultimos_posts( $quantidade = 5 ) {
	global $wpdb;

	$quantidade = absint( $quantidade );
	$results    = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_date, post_status 
			 FROM {$wpdb->posts} 
			 WHERE post_type = %s AND post_status = %s 
			 ORDER BY post_date DESC 
			 LIMIT %d",
			'post',
			'publish',
			$quantidade
		)
	);

	return $results;
}

/**
 * Retorna um único post por ID (exemplo get_row).
 *
 * @param int $post_id ID do post.
 * @return object|null Objeto com colunas da tabela ou null.
 */
function estudos_wp_wpdb_post_por_id( $post_id ) {
	global $wpdb;

	$post_id = absint( $post_id );
	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->posts} WHERE ID = %d",
			$post_id
		)
	);
}

/**
 * Conta posts publicados por tipo (exemplo get_var).
 *
 * @param string $post_type post, page, etc.
 * @return int Contagem.
 */
function estudos_wp_wpdb_contar_posts( $post_type = 'post' ) {
	global $wpdb;

	$post_type = sanitize_key( $post_type );
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
			$post_type,
			'publish'
		)
	);
}

/**
 * Busca com IN (...) – placeholders dinâmicos.
 *
 * @param array $ids Lista de IDs.
 * @return array Rows.
 */
function estudos_wp_wpdb_posts_por_ids( $ids ) {
	global $wpdb;

	$ids = array_map( 'absint', $ids );
	$ids = array_filter( $ids );
	if ( empty( $ids ) ) {
		return array();
	}

	$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title FROM {$wpdb->posts} WHERE ID IN ($placeholders)",
			...$ids
		)
	);
}

/**
 * INSERT: criar registro em tabela customizada (exemplo).
 * Use wp_insert_post() para posts; $wpdb->insert para tabelas customizadas.
 */
function estudos_wp_wpdb_exemplo_insert() {
	global $wpdb;

	$tabela = $wpdb->prefix . 'minha_tabela'; // criar tabela no activate do plugin
	$result = $wpdb->insert(
		$tabela,
		array(
			'titulo'   => 'Exemplo',
			'valor'   => 100,
			'criado_em' => current_time( 'mysql' ),
		),
		array( '%s', '%d', '%s' )
	);

	if ( $result === false ) {
		error_log( 'Insert falhou: ' . $wpdb->last_error );
		return false;
	}

	return $wpdb->insert_id;
}

/**
 * UPDATE: atualizar por ID.
 */
function estudos_wp_wpdb_exemplo_update( $id, $novo_titulo ) {
	global $wpdb;

	$tabela = $wpdb->prefix . 'minha_tabela';
	$rows   = $wpdb->update(
		$tabela,
		array( 'titulo' => $novo_titulo ),
		array( 'id' => $id ),
		array( '%s' ),
		array( '%d' )
	);

	return $rows !== false;
}

/**
 * DELETE: remover por condição.
 */
function estudos_wp_wpdb_exemplo_delete( $id ) {
	global $wpdb;

	$tabela  = $wpdb->prefix . 'minha_tabela';
	$deleted = $wpdb->delete( $tabela, array( 'id' => $id ), array( '%d' ) );
	return $deleted !== false;
}

/**
 * Transação: várias operações atômicas (commit ou rollback).
 * Não misturar com wp_insert_post() dentro da mesma transação.
 */
function estudos_wp_wpdb_exemplo_transacao( $titulo_post, $meta_chave, $meta_valor ) {
	global $wpdb;

	$wpdb->query( 'START TRANSACTION' );

	$insert_post = $wpdb->insert(
		$wpdb->posts,
		array(
			'post_title'  => $titulo_post,
			'post_type'   => 'post',
			'post_status' => 'publish',
			'post_author' => 1,
		),
		array( '%s', '%s', '%s', '%d' )
	);

	if ( $insert_post === false ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'insert_failed', $wpdb->last_error );
	}

	$post_id = $wpdb->insert_id;
	$insert_meta = $wpdb->insert(
		$wpdb->postmeta,
		array(
			'post_id'   => $post_id,
			'meta_key'  => $meta_chave,
			'meta_value' => $meta_valor,
		),
		array( '%d', '%s', '%s' )
	);

	if ( $insert_meta === false ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'meta_failed', $wpdb->last_error );
	}

	$wpdb->query( 'COMMIT' );
	return $post_id;
}

// =============================================================================
// USO DOS EXEMPLOS (descomente em ambiente de desenvolvimento)
// =============================================================================

/*
add_action( 'init', function() {
	$ultimos = estudos_wp_wpdb_ultimos_posts( 3 );
	$total   = estudos_wp_wpdb_contar_posts( 'post' );
	$post    = estudos_wp_wpdb_post_por_id( 1 );
});
*/
