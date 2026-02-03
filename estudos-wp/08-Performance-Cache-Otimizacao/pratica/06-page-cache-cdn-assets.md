# Page cache, CDN e otimização de assets

Referência rápida. Fonte: **008-WordPress-Fase-8-Performance-Cache-Otimizacao.md**.

---

## Page cache (página completa)

- **Ideia:** servir HTML estático (arquivo em disco ou Redis) em `template_redirect`; não executar WordPress para visitantes anônimos em páginas cacheadas.
- **Não cachear:** admin (`is_admin()`), usuário logado (`is_user_logged_in()`), URLs com query string (ex.: `?s=busca`).
- **Chave do cache:** normalmente hash da URL (ex.: `md5( home_url( $request_uri ) )`).
- **Invalidar:** ao `save_post`, deletar arquivo/entrada correspondente à homepage, ao single do post e, se aplicável, arquivos que listam esse post.
- **Limpeza:** remover arquivos de cache mais antigos que X dias (cron ou job).

---

## CDN (rewrite de URLs)

- **Objetivo:** servir assets (imagens, CSS, JS) por outro domínio (CDN) para reduzir latência e usar cache do edge.
- **Implementação:** filters que trocam o domínio da URL:
  - `wp_get_attachment_url` – URL de mídia
  - `wp_calculate_image_srcset` – srcset de imagens responsivas (percorrer `$sources` e reescrever `url`)
  - `style_loader_src`, `script_loader_src` – CSS e JS enfileirados
  - Conteúdo do post: filter em `the_content` para trocar `home_url()` por URL do CDN em `/wp-content/`
- **Não reescrever em admin;** só no front. Verificar se a URL é do próprio site antes de trocar.

---

## Otimização de assets

- **CSS crítico:** inline no `<head>` (wp_add_inline_style) para above-the-fold; resto carregar async ou no footer.
- **JavaScript:** colocar no footer (`true` no 5º parâmetro de wp_enqueue_script); **defer** com `wp_script_add_data( 'handle', 'defer', true )`.
- **Remover o que não usa:** emojis (`remove_action('wp_head', 'print_emoji_detection_script', 7)` e `print_emoji_styles`), RSD, WLW manifest, REST link no head, etc., em `wp_enqueue_scripts` prioridade alta.
- **Minificação:** usar arquivos .min.css / .min.js em produção; versionar (query string ou nome de arquivo) para cache busting.
- **Imagens:** WebP quando possível; lazy load (loading="lazy"); tamanhos adequados (srcset).

---

## Resumo

| Técnica      | Uso principal                          |
|-------------|-----------------------------------------|
| Page cache  | HTML completo para anônimos             |
| CDN         | Assets estáticos (imagens, CSS, JS)     |
| Defer/async | Não bloquear render; CSS não crítico   |
| Invalidação | Sempre que conteúdo ou configuração mudar |
