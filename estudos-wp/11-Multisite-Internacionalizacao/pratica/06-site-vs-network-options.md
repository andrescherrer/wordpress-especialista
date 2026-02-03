# Site vs Network Options (Multisite)

Referência. Fonte: **011-WordPress-Fase-11-Multisite-Internacionalizacao.md**.

---

## Diferença

| Função | Escopo | Onde fica |
|--------|--------|-----------|
| **get_option** / **update_option** / **delete_option** | Site atual | Tabela `wp_X_options` do blog |
| **get_site_option** / **update_site_option** / **delete_site_option** | Rede inteira | Tabela `wp_sitemeta` |

---

## Quando usar cada uma

- **Opção por site:** configuração que cada site pode ter diferente (ex.: cor do tema, nome do blog). Use **get_option** / **update_option**.
- **Opção da rede:** configuração única para toda a rede (ex.: API key global, feature flag para todos os sites). Use **get_site_option** / **update_site_option**.

---

## Obter opção de outro site (sem switch)

- **get_blog_option( $blog_id, 'option_name', $default );** – lê opção de um blog específico sem precisar de `switch_to_blog`. Útil para listar configurações de vários sites no network admin.

---

## Padrão “network-aware”

Alguns plugins permitem que a rede defina um padrão e cada site possa sobrescrever:

```php
// Ler: primeiro tenta o site; se não existir, usa o valor da rede
function meu_plugin_get_option( $key, $default = false ) {
    $value = get_option( $key, null );
    if ( $value === null && is_multisite() ) {
        $value = get_site_option( $key, $default );
    }
    return $value !== null ? $value : $default;
}
```

---

## Resumo

- **get_option** = sempre do site atual (wp_X_options).
- **get_site_option** = da rede (wp_sitemeta); só existe em Multisite.
- **get_blog_option( $blog_id, ... )** = de um site específico sem trocar contexto.
