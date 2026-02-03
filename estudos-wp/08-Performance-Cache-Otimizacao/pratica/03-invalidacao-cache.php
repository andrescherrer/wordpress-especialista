<?php
/**
 * Exemplo 03: Invalidação de cache – versionamento de chaves e cascata
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Versionamento: chave = $key . '_v' . $version; invalidar grupo = incrementar versão (uma escrita).
 *   Cascata: ao salvar recurso, invalidar todos os caches que dependem dele (post_list, author_posts, related).
 *   Mapear dependências (ex.: post => [ 'post_list', 'author_posts', 'related_posts' ]) e chamar delete em cada um.
 *
 * Uso: Cache_Key_Versioning para invalidar grupo inteiro; Cascade_Invalidation em save_post.
 *
 * @package EstudosWP
 * @subpackage 08-Performance-Cache-Otimizacao
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Versionamento: invalidar grupo inteiro incrementando versão.
 */
class Estudos_WP_Cache_Key_Versioning {

	const VERSION_GROUP = 'cache_versions';

	/**
	 * Obter versão atual do grupo.
	 *
	 * @param string $group Nome do grupo.
	 * @return int
	 */
	private function get_cache_version( $group ) {
		$version_key = 'cache_version_' . $group;
		$version     = wp_cache_get( $version_key, self::VERSION_GROUP );

		if ( false === $version ) {
			$version = time();
			wp_cache_set( $version_key, $version, self::VERSION_GROUP, 0 );
		}

		return (int) $version;
	}

	/**
	 * Invalidar todo o grupo (incrementa versão; chaves antigas deixam de ser encontradas).
	 *
	 * @param string $group Nome do grupo.
	 */
	public function invalidate_group( $group ) {
		$version_key = 'cache_version_' . $group;
		$current     = $this->get_cache_version( $group );
		wp_cache_set( $version_key, $current + 1, self::VERSION_GROUP, 0 );
	}

	/**
	 * Obter dado com chave versionada.
	 *
	 * @param string $key   Chave lógica.
	 * @param string $group Grupo.
	 * @return mixed|false
	 */
	public function get_cached_data( $key, $group ) {
		$version      = $this->get_cache_version( $group );
		$versioned_key = $key . '_v' . $version;
		return wp_cache_get( $versioned_key, $group );
	}

	/**
	 * Salvar dado com chave versionada.
	 *
	 * @param string $key        Chave lógica.
	 * @param mixed  $data       Dados.
	 * @param string $group      Grupo.
	 * @param int    $expiration TTL em segundos.
	 */
	public function set_cached_data( $key, $data, $group, $expiration = 3600 ) {
		$version       = $this->get_cache_version( $group );
		$versioned_key = $key . '_v' . $version;
		wp_cache_set( $versioned_key, $data, $group, $expiration );
	}
}

/**
 * Invalidação em cascata: ao salvar post, invalidar caches relacionados.
 */
class Estudos_WP_Cascade_Cache_Invalidation {

	private $dependencies = array(
		'post' => array( 'post_list', 'author_posts', 'related_posts' ),
		'author' => array( 'author_stats', 'author_posts' ),
	);

	/**
	 * Invalidar em cascata por tipo de recurso.
	 *
	 * @param string $resource_type Tipo (post, author, etc.).
	 * @param int    $resource_id   ID do recurso.
	 */
	public function invalidate_cascade( $resource_type, $resource_id ) {
		if ( ! isset( $this->dependencies[ $resource_type ] ) ) {
			return;
		}

		foreach ( $this->dependencies[ $resource_type ] as $group ) {
			$this->invalidate_cache_group( $group, $resource_id );
		}
	}

	private function invalidate_cache_group( $group, $resource_id = null ) {
		switch ( $group ) {
			case 'post_list':
				wp_cache_delete( 'all_posts', 'posts' );
				wp_cache_delete( 'featured_posts', 'posts' );
				break;
			case 'author_posts':
				if ( $resource_id ) {
					$post = get_post( $resource_id );
					if ( $post ) {
						wp_cache_delete( 'author_' . $post->post_author . '_posts', 'authors' );
					}
				}
				break;
			case 'related_posts':
				if ( $resource_id ) {
					wp_cache_delete( 'related_' . $resource_id, 'posts' );
				}
				break;
			case 'author_stats':
				if ( $resource_id ) {
					wp_cache_delete( 'author_' . $resource_id, 'author_stats' );
				}
				break;
		}
	}
}

// Hook: invalidar em cascata ao salvar post
add_action( 'save_post', function( $post_id ) {
	$invalidator = new Estudos_WP_Cascade_Cache_Invalidation();
	$invalidator->invalidate_cascade( 'post', $post_id );
}, 10, 1 );
