<?php
/**
 * REFERÊNCIA RÁPIDA – Service que usa Repository + Validator (fluxo create/update)
 *
 * Service recebe Repository e Validator no construtor; create($data): valida → repository->save(); update($id, $data): findById → valida → save.
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md
 *
 * @package EstudosWP
 * @subpackage 13-Arquitetura-Avancada
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estudos_WP_Post_Service {

    private Estudos_WP_Post_Repository $repository;

    public function __construct(Estudos_WP_Post_Repository $repository) {
        $this->repository = $repository;
    }

    public function create(array $data): int {
        $this->validate($data);
        return $this->repository->save($data);
    }

    public function update(int $id, array $data): int {
        $post = $this->repository->findById($id);
        if (!$post) {
            throw new RuntimeException('Post not found');
        }
        $data['ID'] = $id;
        $this->validate($data);
        return $this->repository->save($data);
    }

    private function validate(array $data): void {
        if (empty($data['post_title']) || !is_string($data['post_title'])) {
            throw new InvalidArgumentException('post_title is required and must be a string');
        }
        if (strlen($data['post_title']) < 2) {
            throw new InvalidArgumentException('post_title must be at least 2 characters');
        }
    }
}
