<?php
/**
 * Exemplo 03: Posts, Post Meta e The Loop
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   get_post($id); wp_insert_post([...]); wp_update_post([...]); wp_delete_post($id);
 *   get_post_meta($id, 'key', true); update_post_meta(); delete_post_meta();
 *   Loop: if (have_posts()) { while (have_posts()) { the_post(); the_title(); ... } }
 *   WP_Query: $q = new WP_Query($args); $q->have_posts(); $q->the_post(); wp_reset_postdata();
 *   Loop aninhado: sempre wp_reset_postdata() ao terminar o loop interno.
 *
 * @package EstudosWP
 * @subpackage 01-Fundamentos-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// OBTER E CRIAR POSTS
// =============================================================================

/**
 * Exemplo: obter post e meta.
 */
function estudos_wp_exemplo_get_post_meta() {
	$post = get_post( 1 );
	if ( ! $post || $post->post_status !== 'publish' ) {
		return null;
	}

	$subtitulo = get_post_meta( $post->ID, 'subtitulo', true );
	$preco     = get_post_meta( $post->ID, 'preco', true );

	return array(
		'titulo'     => $post->post_title,
		'subtitulo'  => $subtitulo,
		'preco'      => $preco,
		'conteudo'   => $post->post_content,
	);
}

/**
 * Exemplo: criar post programaticamente.
 *
 * @return int|WP_Error ID do post ou erro.
 */
function estudos_wp_exemplo_criar_post() {
	$post_id = wp_insert_post(
		array(
			'post_title'   => 'Post criado por exemplo Fase 1',
			'post_content' => 'Conteúdo do post.',
			'post_type'    => 'post',
			'post_status'  => 'draft',
			'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	update_post_meta( $post_id, 'fonte', 'estudos-wp-01-fundamentos' );
	return $post_id;
}

// =============================================================================
// THE LOOP – template típico (para usar em arquivo de tema, ex: index.php)
// =============================================================================

/**
 * Trecho de template: loop padrão.
 * Cole em index.php, archive.php, etc.
 */
function estudos_wp_template_loop_exemplo() {
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-meta">
					<?php the_date(); ?> | <?php the_author(); ?>
				</div>
				<div class="entry-content">
					<?php the_excerpt(); ?>
				</div>
			</article>
			<?php
		}
	} else {
		echo '<p>Nenhum post encontrado.</p>';
	}
}

// =============================================================================
// LOOP CUSTOMIZADO COM WP_Query
// =============================================================================

/**
 * Lista posts com WP_Query e reset correto.
 *
 * @param int $quantidade Quantidade de posts.
 * @return void
 */
function estudos_wp_loop_customizado( $quantidade = 5 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $quantidade,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $query->have_posts() ) {
		echo '<p>Nenhum post.</p>';
		return;
	}

	while ( $query->have_posts() ) {
		$query->the_post();
		echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
		echo '<p>' . esc_html( wp_trim_words( get_the_excerpt(), 15 ) ) . '</p>';
	}

	wp_reset_postdata();
}

// =============================================================================
// LOOP ANINHADO CORRETO (evitar sobrescrever $post global)
// =============================================================================

/**
 * Exemplo: dentro do loop principal, mostrar "posts relacionados".
 * Usar WP_Query separado e wp_reset_postdata() ao terminar.
 */
function estudos_wp_loop_aninhado_seguro() {
	$principal = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );

	if ( ! $principal->have_posts() ) {
		return;
	}

	while ( $principal->have_posts() ) {
		$principal->the_post();
		echo '<h2>' . esc_html( get_the_title() ) . '</h2>';

		// Loop interno: últimos 2 posts (não usar get_posts() sem reset depois!)
		$relacionados = new WP_Query( array(
			'post_type'      => 'post',
			'posts_per_page' => 2,
			'post__not_in'   => array( get_the_ID() ),
		) );

		if ( $relacionados->have_posts() ) {
			while ( $relacionados->have_posts() ) {
				$relacionados->the_post();
				echo '<li>' . esc_html( get_the_title() ) . '</li>';
			}
			wp_reset_postdata(); // restaura $post do loop principal
		}

		echo '<p>' . esc_html( get_the_excerpt() ) . '</p>';
	}

	wp_reset_postdata();
}

// =============================================================================
// SHORTCODE DE EXEMPLO (lista posts com meta)
// =============================================================================

add_shortcode( 'estudos_wp_lista_posts', 'estudos_wp_shortcode_lista_posts' );

function estudos_wp_shortcode_lista_posts( $atts ) {
	$atts = shortcode_atts( array(
		'numero' => 5,
	), $atts, 'estudos_wp_lista_posts' );

	ob_start();
	estudos_wp_loop_customizado( (int) $atts['numero'] );
	return ob_get_clean();
}

// Uso no editor: [estudos_wp_lista_posts numero="3"]
