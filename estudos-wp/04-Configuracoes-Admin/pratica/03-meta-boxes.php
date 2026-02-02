<?php
/**
 * Exemplo 03: Meta boxes (add_meta_box + save_post)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   add_meta_boxes → add_meta_box( $id, $title, $callback, $screen, $context, $priority ). Screen = 'post', 'page' ou CPT.
 *   No callback: wp_nonce_field( 'action', 'name' ); get_post_meta( $post->ID, '_key', true ); esc_attr, checked, selected.
 *   save_post: verificar nonce, !wp_is_post_autosave/revision, current_user_can( 'edit_post', $post_id ), update_post_meta com sanitize.
 *
 * @package EstudosWP
 * @subpackage 04-Configuracoes-Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'estudos_wp_admin_add_meta_boxes' );
add_action( 'save_post', 'estudos_wp_admin_save_meta_box', 10, 2 );

const ESTUDOS_WP_META_NONCE_ACTION = 'estudos_wp_meta_box';
const ESTUDOS_WP_META_NONCE_NAME   = 'estudos_wp_meta_nonce';

function estudos_wp_admin_add_meta_boxes() {
	add_meta_box(
		'estudos_wp_meta_box',
		'Campos extras (Estudos WP)',
		'estudos_wp_admin_render_meta_box',
		'post',
		'normal',
		'default'
	);
}

function estudos_wp_admin_render_meta_box( WP_Post $post ) {
	wp_nonce_field( ESTUDOS_WP_META_NONCE_ACTION, ESTUDOS_WP_META_NONCE_NAME );

	$subtitulo = get_post_meta( $post->ID, '_estudos_wp_subtitulo', true );
	$destaque  = (int) get_post_meta( $post->ID, '_estudos_wp_destaque', true );
	?>
	<p>
		<label for="estudos_wp_subtitulo"><strong>Subtítulo</strong></label><br>
		<input type="text"
		       id="estudos_wp_subtitulo"
		       name="estudos_wp_subtitulo"
		       value="<?php echo esc_attr( $subtitulo ); ?>"
		       class="widefat">
	</p>
	<p>
		<label for="estudos_wp_destaque">
			<input type="checkbox"
			       id="estudos_wp_destaque"
			       name="estudos_wp_destaque"
			       value="1"
			       <?php checked( 1, $destaque ); ?>>
			Marcar como destaque
		</label>
	</p>
	<?php
}

function estudos_wp_admin_save_meta_box( $post_id, WP_Post $post ) {
	if ( ! isset( $_POST[ ESTUDOS_WP_META_NONCE_NAME ] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ ESTUDOS_WP_META_NONCE_NAME ] ) ), ESTUDOS_WP_META_NONCE_ACTION ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estudos_wp_subtitulo'] ) ) {
		update_post_meta(
			$post_id,
			'_estudos_wp_subtitulo',
			sanitize_text_field( wp_unslash( $_POST['estudos_wp_subtitulo'] ) )
		);
	}

	$destaque = isset( $_POST['estudos_wp_destaque'] ) ? 1 : 0;
	update_post_meta( $post_id, '_estudos_wp_destaque', $destaque );
}
