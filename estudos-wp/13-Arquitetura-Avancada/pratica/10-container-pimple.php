<?php
/**
 * REFERÊNCIA RÁPIDA – Container Pimple (bind, get, singleton)
 *
 * composer require pimple/pimple (ou já em Slim/outros).
 * $container = new Pimple\Container(); $container['repo'] = fn($c) => new PostRepository();
 * $container['repo'] = $container->factory(fn() => new PostRepository()); // nova instância cada get.
 * $container['service'] = fn($c) => new PostService($c['repo']); $service = $container['service'];
 *
 * Fonte: 013-WordPress-Fase-13-Arquitetura-Avancada.md
 *
 * @package EstudosWP
 * @subpackage 13-Arquitetura-Avancada
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Pimple\Container')) {
    return;
}

$container = new Pimple\Container();

$container['post_repository'] = function () {
    return new Estudos_WP_Post_Repository();
};

$container['post_service'] = function ($c) {
    return new Estudos_WP_Post_Service($c['post_repository']);
};

// Uso: $service = $container['post_service']; $service->create(['post_title' => 'Título', 'post_content' => '']);

// Singleton (padrão no Pimple: mesma instância a cada get):
// $container['post_repository'] = $container->factory(function () { ... }); // factory = nova instância cada vez
// Sem factory = singleton (uma instância por container).
