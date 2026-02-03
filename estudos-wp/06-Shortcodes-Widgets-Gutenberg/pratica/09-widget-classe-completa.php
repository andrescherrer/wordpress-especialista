<?php
/**
 * REFERÊNCIA RÁPIDA – Widget completo (form, update, widget)
 *
 * extends WP_Widget; __construct(id_base, name, widget_options, control_options).
 * widget($args, $instance): echo no front. form($instance): campos no admin. update($new, $old): sanitize e save.
 * register_widget(ClassName); wp_register_sidebar_widget / add_action('widgets_init', ...).
 *
 * Fonte: 006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md
 *
 * @package EstudosWP
 * @subpackage 06-Shortcodes-Widgets-Gutenberg
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estudos_WP_Widget_Texto extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'estudos_wp_texto',
            'Estudos WP – Texto',
            ['description' => 'Widget de texto com título e corpo'],
            ['width' => 400]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['titulo'])) {
            echo $args['before_title'] . esc_html($instance['titulo']) . $args['after_title'];
        }
        if (!empty($instance['texto'])) {
            echo '<div class="widget-texto">' . wp_kses_post($instance['texto']) . '</div>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $titulo = isset($instance['titulo']) ? $instance['titulo'] : '';
        $texto  = isset($instance['texto']) ? $instance['texto'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('titulo')); ?>">Título:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('titulo')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('titulo')); ?>" type="text"
                   value="<?php echo esc_attr($titulo); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('texto')); ?>">Texto:</label>
            <textarea class="widefat" rows="4" id="<?php echo esc_attr($this->get_field_id('texto')); ?>"
                      name="<?php echo esc_attr($this->get_field_name('texto')); ?>"><?php echo esc_textarea($texto); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['titulo'] = !empty($new_instance['titulo']) ? sanitize_text_field($new_instance['titulo']) : '';
        $instance['texto']  = !empty($new_instance['texto']) ? wp_kses_post($new_instance['texto']) : '';
        return $instance;
    }
}

add_action('widgets_init', function () {
    register_widget('Estudos_WP_Widget_Texto');
});
