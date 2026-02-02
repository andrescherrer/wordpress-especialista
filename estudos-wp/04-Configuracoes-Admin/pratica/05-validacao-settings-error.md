# Validação e add_settings_error

**Referência rápida:** No **sanitize_callback** do `register_setting`, se o valor for inválido use **add_settings_error( $setting, $code, $message, $type )** e retorne o valor anterior com **get_option( $option_name )**. Na página, **settings_errors( $option_name )** exibe as mensagens.

---

## add_settings_error

```php
add_settings_error(
    $setting,  // ID da setting (geralmente o option_name)
    $code,     // Código único do erro (ex: 'invalid_email')
    $message,  // Mensagem exibida ao usuário
    $type      // 'error' | 'success' | 'warning' | 'info'
);
```

---

## Exemplo no sanitize_callback

```php
register_setting(
    'meu_plugin_group',
    'meu_plugin_opcoes',
    [
        'type'              => 'array',
        'sanitize_callback' => function( $input ) {
            if ( ! is_array( $input ) ) {
                add_settings_error(
                    'meu_plugin_opcoes',
                    'invalid_input',
                    'Formato inválido.',
                    'error'
                );
                return get_option( 'meu_plugin_opcoes', [] );
            }

            $out = [];

            if ( isset( $input['email'] ) ) {
                if ( ! is_email( $input['email'] ) ) {
                    add_settings_error(
                        'meu_plugin_opcoes',
                        'invalid_email',
                        'Email inválido.',
                        'error'
                    );
                    $out['email'] = get_option( 'meu_plugin_opcoes', [] )['email'] ?? '';
                } else {
                    $out['email'] = sanitize_email( $input['email'] );
                }
            }

            if ( isset( $input['numero'] ) ) {
                if ( ! is_numeric( $input['numero'] ) || (int) $input['numero'] < 0 ) {
                    add_settings_error(
                        'meu_plugin_opcoes',
                        'invalid_number',
                        'Deve ser um número positivo.',
                        'error'
                    );
                    $out['numero'] = get_option( 'meu_plugin_opcoes', [] )['numero'] ?? 0;
                } else {
                    $out['numero'] = absint( $input['numero'] );
                }
            }

            return $out;
        }
    ]
);
```

---

## Exibir na página

No callback que renderiza a página de configurações:

```php
<?php settings_errors( 'meu_plugin_opcoes' ); ?>
```

Assim, as mensagens de erro/sucesso aparecem acima do formulário.

---

## Regras rápidas

- **Sanitize** sempre (sanitize_text_field, sanitize_email, etc.).
- **Validar** antes de aceitar: se inválido, add_settings_error e retornar valor anterior.
- **Não** retornar `$input` sem sanitizar; **não** confiar em dados do usuário.

---

*Fonte: 004-WordPress-Fase-4-Configuracoes-Admin.md*
