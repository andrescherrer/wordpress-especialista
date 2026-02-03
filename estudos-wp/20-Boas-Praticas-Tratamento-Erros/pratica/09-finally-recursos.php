<?php
/**
 * REFERÊNCIA RÁPIDA – finally (fechar handle, limpar recurso) em try/catch
 *
 * try { $handle = fopen($path, 'r'); ... } finally { if (isset($handle)) { fclose($handle); } }
 * Garante que recurso seja liberado mesmo se exceção for lançada.
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 *
 * @package EstudosWP
 * @subpackage 20-Boas-Praticas-Tratamento-Erros
 */

if (!defined('ABSPATH')) {
    exit;
}

function estudos_wp_read_lines_safe(string $path): array {
    $handle = null;
    try {
        if (!file_exists($path) || !is_readable($path)) {
            throw new RuntimeException('File not readable: ' . $path);
        }
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException('Failed to open: ' . $path);
        }
        $lines = [];
        while (($line = fgets($handle)) !== false) {
            $lines[] = trim($line);
        }
        return $lines;
    } finally {
        if (isset($handle) && is_resource($handle)) {
            fclose($handle);
        }
    }
}
