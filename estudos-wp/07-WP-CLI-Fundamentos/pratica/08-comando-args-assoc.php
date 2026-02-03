<?php
/**
 * REFERÊNCIA RÁPIDA – Comando com argumento posicional e assoc
 *
 * $args[0] = primeiro posicional; $assoc_args['campo'] = valor de --campo=valor.
 * WP_CLI::add_command('namespace comando', callback); callback($args, $assoc_args).
 * Fonte: 007-WordPress-Fase-7-WP-CLI-Fundamentos.md
 *
 * @package EstudosWP
 * @subpackage 07-WP-CLI-Fundamentos
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

WP_CLI::add_command('estudos-wp exemplo', function ($args, $assoc_args) {
    $nome = isset($args[0]) ? $args[0] : 'Mundo';
    $vezes = isset($assoc_args['vezes']) ? absint($assoc_args['vezes']) : 1;
    $quiet = isset($assoc_args['quiet']);
    for ($i = 0; $i < $vezes; $i++) {
        WP_CLI::log("Olá, {$nome}!");
    }
    if (!$quiet) {
        WP_CLI::success('Comando concluído.');
    }
}, [
    'shortdesc' => 'Exemplo com argumento posicional e assoc',
    'synopsis'  => [
        ['type' => 'positional', 'name' => 'nome', 'optional' => true, 'description' => 'Nome para saudação'],
        ['type' => 'assoc', 'name' => 'vezes', 'optional' => true, 'description' => 'Quantas vezes repetir', 'default' => 1],
        ['type' => 'flag', 'name' => 'quiet', 'optional' => true, 'description' => 'Não exibir mensagem de sucesso'],
    ],
]);

// Uso: wp estudos-wp exemplo "Maria" --vezes=3
// Uso: wp estudos-wp exemplo --vezes=2 --quiet
