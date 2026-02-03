<?php
/**
 * Exemplo 03: Widget clássico (WP_Widget)
 *
 * REFERÊNCIA RÁPIDA (este tópico):
 *   Estender WP_Widget. __construct: parent::__construct( $id, $name, $widget_ops ).
 *   widget( $args, $instance ) – front; form( $instance ) – admin; update( $new, $old ) – sanitize e retornar.
 *   get_field_id( 'campo' ), get_field_name( 'campo' ); register_widget( 'Classe' ) em widgets_init.
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'widgets_init', 'estudos_wp_register_widget_recentes' );

function estudos_wp_register_widget_recentes() {
	register_widget( 'Estudos_WP_Widget_Posts_Recentes' );
}

class Estudos_WP_Widget_Posts_Recentes extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'estudos_wp_posts_recentes',
			'Estudos WP – Posts recentes',
			array(
				'classname'                   => 'estudos_wp_widget_recentes',
				'description'                 => 'Exibe uma lista de posts recentes.',
				'customize_selective_refresh' => true,
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo esc_html( $instance['title'] );
			echo $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$numero = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$numero = max( 1, min( 20, $numero ) );

		$query = new WP_Query(
			array(
				'posts_per_page' => $numero,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( $query->have_posts() ) {
			echo '<ul class="estudos-wp-widget-recentes-list">';
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<li>';
				echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>';
				echo ' <span class="date">' . esc_html( get_the_date( 'd/m/Y' ) ) . '</span>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>Nenhum post encontrado.</p>';
		}

		wp_reset_postdata();
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function form( $instance ) {
		$title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Título:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">Número de posts:</label>
			<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"
			       type="number" min="1" max="20" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['number'] = absint( $new_instance['number'] ?? 5 );
		$instance['number'] = max( 1, min( 20, $instance['number'] ) );
		return $instance;
	}
}
