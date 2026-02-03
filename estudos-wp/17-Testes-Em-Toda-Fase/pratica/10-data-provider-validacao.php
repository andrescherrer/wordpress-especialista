<?php
/**
 * REFERÊNCIA RÁPIDA – Data provider (PHPUnit) para vários inputs de validação
 *
 * @dataProvider providerInvalidTitles
 * public function test_validation_rejects_invalid_title($input, $expectedError) { ... }
 * public static function providerInvalidTitles(): array { return [ ['', 'required'], ['a', 'min_length'], [123, 'type']; ]; }
 *
 * Fonte: 017-WordPress-Fase-17-Testes-Em-Toda-Fase.md
 *
 * @package EstudosWP
 * @subpackage 17-Testes-Em-Toda-Fase
 */

if (!defined('ABSPATH')) {
    exit;
}

// Exemplo:
// class Test_Post_Validation extends WP_UnitTestCase {
//     /** @dataProvider invalidTitleProvider */
//     public function test_create_rejects_invalid_title($title, $expectException) {
//         $service = new Estudos_WP_Post_Service(new Estudos_WP_Post_Repository());
//         if ($expectException) {
//             $this->expectException(InvalidArgumentException::class);
//         }
//         $id = $service->create(['post_title' => $title, 'post_content' => '']);
//         $this->assertGreaterThan(0, $id);
//     }
//
//     public static function invalidTitleProvider(): array {
//         return [
//             'empty'   => ['', true],
//             'short'   => ['a', true],
//             'valid'   => ['Título válido', false],
//         ];
//     }
// }
