<?php
/**
 * REFERÊNCIA RÁPIDA – WPGraphQL: expor CPT e mutations
 *
 * Registrar CPT com suporte a GraphQL (show_in_graphql, graphql_single_name, graphql_plural_name).
 * register_graphql_field para campos custom; conexões (post → terms).
 * Mutations: createPost, updatePost; input types; validação e permissões no resolver.
 *
 * @package EstudosWP
 * @subpackage 15-Topicos-Complementares-Avancados
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('register_post_type')) {
    return;
}

// Exemplo: CPT já registrado com suporte GraphQL (na hora do register_post_type)
// 'show_in_graphql'     => true,
// 'graphql_single_name'  => 'Livro',
// 'graphql_plural_name'  => 'Livros',

// Registrar campo custom no tipo Post (exemplo)
add_action('graphql_register_types', function () {
    if (!function_exists('register_graphql_field')) {
        return;
    }
    register_graphql_field('Post', 'tempoLeitura', [
        'type'        => 'Int',
        'description' => 'Tempo de leitura em minutos',
        'resolve'     => function ($post) {
            $content = get_post_field('post_content', $post->databaseId);
            $words   = str_word_count(strip_tags($content));
            return (int) max(1, ceil($words / 200));
        },
    ]);
});

// Mutations: criar/atualizar são expostas pelo WPGraphQL para post types que têm suporte.
// Validação e permissões: no resolver da mutation (check_allow_create/update, sanitize input).
// Ver documentação WPGraphQL para register_graphql_mutation e input types.
