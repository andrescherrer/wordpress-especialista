<?php
/**
 * REFERÊNCIA RÁPIDA – Teste de Repository com mock (stub retornando entidade)
 *
 * $mock = $this->createMock(PostRepository::class); $mock->method('findById')->willReturn($post);
 * Service recebe mock; testar que create() chama repository->save() com dados esperados (expects($this->once())).
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md
 *
 * @package EstudosWP
 * @subpackage 17-Testes-Em-Toda-Fase
 */

if (!defined('ABSPATH')) {
    exit;
}

// Exemplo (PHPUnit):
// $post = new WP_Post((object)['ID' => 1, 'post_title' => 'Test', 'post_status' => 'publish']);
// $mockRepo = $this->createMock(Estudos_WP_Post_Repository::class);
// $mockRepo->method('findById')->with(1)->willReturn($post);
// $mockRepo->method('save')->willReturn(1);
// $service = new Estudos_WP_Post_Service($mockRepo);
// $mockRepo->expects($this->once())->method('save')->with($this->callback(function ($data) {
//     return isset($data['post_title']) && $data['post_title'] === 'Novo';
// }));
// $service->update(1, ['post_title' => 'Novo', 'post_content' => '']);
// $this->assertEquals(1, $service->update(1, ['post_title' => 'Novo']));
