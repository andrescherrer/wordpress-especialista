# Retry e fallback em chamadas externas (REST)

Resumo: quando uma chamada `wp_remote_get`/`wp_remote_post` falha (timeout, 5xx), usar retry com backoff e/ou fallback. Fonte: **003-WordPress-Fase-3-REST-API-Avancado.md**.

---

## Retry com backoff

- Tentar N vezes (ex.: 3) com intervalo crescente (1s, 2s, 4s).
- Só retry para erros temporários: timeout, 503, 429; não retry para 4xx (exceto 429).
- Exemplo em PHP: loop com `sleep(2 ** $tentativa)`; checar `wp_remote_retrieve_response_code()` e `is_wp_error()`.

---

## Fallback

- Se após retries a chamada falhar, usar valor em cache (transient) ou resposta padrão.
- Ex.: `get_transient('meu_plugin_feed')`; se vazio e API falhou, retornar array vazio ou última resposta em cache.

---

## Exemplo (pseudocódigo)

```php
$max_tentativas = 3;
for ($i = 0; $i < $max_tentativas; $i++) {
    $res = wp_remote_get($url, ['timeout' => 15]);
    if (!is_wp_error($res)) {
        $code = wp_remote_retrieve_response_code($res);
        if ($code >= 200 && $code < 300) {
            return json_decode(wp_remote_retrieve_body($res), true);
        }
        if ($code >= 400 && $code < 500 && $code != 429) {
            break; // não retry
        }
    }
    if ($i < $max_tentativas - 1) {
        sleep(2 ** $i);
    }
}
return get_transient('fallback_feed') ?: [];
```
