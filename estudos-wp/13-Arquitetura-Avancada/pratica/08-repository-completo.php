<?php
/**
 * REFERÊNCIA RÁPIDA – Repository completo (find, findById, save, delete)
 *
 * find($args): WP_Query; findById($id): get_post; save($data): wp_insert_post; delete($id): wp_delete_post.
 * Abstrair acesso a dados; validar/sanitizar antes de persistir.
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md
 *
 * @package EstudosWP
 * @subpackage 13-Arquitetura-Avancada
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estudos_WP_Post_Repository {

    public function find(array $args = []): array {
        $defaults = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
        ];
        $query = new WP_Query(array_merge($defaults, $args));
        return $query->posts;
    }

    public function findById(int $id): ?WP_Post {
        $post = get_post($id);
        return $post instanceof WP_Post ? $post : null;
    }

    public function save(array $data): int {
        if (empty($data['post_title'])) {
            throw new InvalidArgumentException('post_title is required');
        }
        $post_data = [
            'post_title'   => sanitize_text_field($data['post_title']),
            'post_content' => wp_kses_post($data['post_content'] ?? ''),
            'post_status'  => in_array($data['post_status'] ?? '', ['draft', 'publish', 'pending'], true) ? $data['post_status'] : 'draft',
            'post_type'    => 'post',
        ];
        if (!empty($data['ID'])) {
            $post_data['ID'] = absint($data['ID']);
            $result = wp_update_post($post_data);
        } else {
            $result = wp_insert_post($post_data);
        }
        if (is_wp_error($result)) {
            throw new RuntimeException($result->get_error_message());
        }
        return (int) $result;
    }

    public function delete(int $id): bool {
        $post = $this->findById($id);
        if (!$post) {
            return false;
        }
        return (bool) wp_delete_post($id, true);
    }
}
