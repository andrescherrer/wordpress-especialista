<?php
/**
 * REFERÊNCIA RÁPIDA – Princípios: Fail fast, never swallow, exceções específicas
 *
 * Fail fast: detectar e reportar cedo; não retornar '' ou null escondendo falha.
 * Never swallow: nunca catch sem logar/notificar/re-lançar.
 * Tipos: ValidationException, NotFoundException, PermissionException, BusinessRuleException.
 *
 * Fonte: 020-WordPress-Fase-20-Boas-Praticas-Tratamento-Erros.md
 */

// ========== 1. Fail fast (não esconder erro) ==========

// ❌ ERRADO: retorna string vazia em caso de erro
function get_user_email_wrong($user_id) {
    $user = get_userdata($user_id);
    return $user ? $user->user_email : '';
}

// ✅ CORRETO: fail loud
function get_user_email_ok($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        throw new InvalidArgumentException("User with ID {$user_id} not found");
    }
    return $user->user_email;
}

// ========== 2. Never swallow exceptions ==========

// ❌ ERRADO: ignorar erro silenciosamente
// try {
//     process_payment($order_id);
// } catch (Exception $e) {
//     // vazio
// }

// ✅ CORRETO: tratar ou re-lançar
// try {
//     process_payment($order_id);
// } catch (PaymentException $e) {
//     error_log('Payment failed: ' . $e->getMessage());
//     notify_admin($e);
//     throw $e;
// }

// ========== 3. Exceções específicas do domínio ==========

class ValidationException extends DomainException {}
class NotFoundException extends DomainException {}
class PermissionException extends DomainException {}
class BusinessRuleException extends DomainException {}

function create_order_example(array $data) {
    if (empty($data['email'])) {
        throw new ValidationException('Email is required');
    }
    $user_id = $data['user_id'] ?? 0;
    if (!get_userdata($user_id)) {
        throw new NotFoundException("User {$user_id} not found");
    }
    if (!current_user_can('edit_posts')) {
        throw new PermissionException('Insufficient permissions');
    }
    // ...
}
