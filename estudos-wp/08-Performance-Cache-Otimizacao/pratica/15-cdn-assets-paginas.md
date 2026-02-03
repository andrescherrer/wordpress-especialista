# CDN para assets e páginas

O que colocar no **CDN** (imagens, CSS, JS); **invalidação** de cache; CloudFront/Cloudflare como exemplo; integração com WordPress (constante ou plugin).

---

## O que colocar no CDN

| Tipo | Uso |
|------|-----|
| **Imagens** | Uploads (wp-content/uploads); estáticos do tema/plugin (logos, ícones). |
| **CSS e JS** | Arquivos enfileirados (temas, plugins); versionados por query string ou nome de arquivo. |
| **Páginas HTML** | Cache de página inteira (full page cache) na borda; invalidação ao publicar/alterar. |

- Objetivo: reduzir latência e carga no origin (servidor WordPress); CDN entrega da borda mais próxima do usuário.

---

## Invalidação de cache

- **Assets:** usar **versionamento** (query string `?ver=1.2.3` ou nome de arquivo com hash); ao atualizar, nova URL e o CDN entrega o novo arquivo.
- **Páginas:** ao publicar/atualizar post ou página, invalidar a URL (e eventualmente a homepage, listagens) no CDN; CloudFront: create invalidation; Cloudflare: purge by URL ou cache tags (se configurado).

---

## CloudFront / Cloudflare (exemplo)

- **CloudFront:** criar distribution com origin no servidor WordPress; configurar TTL; invalidação via API ou console ao publicar.
- **Cloudflare:** proxy DNS para o site; cache automático; purge via dashboard ou API; regras de page rules para cache de páginas.
- **Integração WordPress:** plugin (ex.: WP Offload Media, Cloudflare plugin) ou constante (ex.: definir URL de uploads para o domínio do CDN) para que links de imagens/CSS/JS apontem para o CDN.

---

## Constante ou plugin

- **Constante:** ex.: `WP_CONTENT_URL` ou filtro que altere a URL base de uploads para o domínio do CDN; temas/plugins que usam `content_url()` passam a gerar URLs do CDN.
- **Plugin:** vários plugins permitem definir “CDN URL” para uploads e/ou assets; alguns fazem substituição em tempo real no HTML.
- **Full page cache:** pode ser no CDN (Cloudflare, Fastly) ou no servidor (Varnish, Nginx FastCGI cache); invalidação ao publicar é essencial.

---

## Recursos

- Documentação CloudFront (invalidation), Cloudflare (caching, purge).
- Ver **06-page-cache-cdn-assets.md**, **13-object-cache-redis-memcached.md**, **14-load-balancer-sessoes.md**; estudos-wp/14: **11-fila-externa-sqs-rabbitmq.md**, **12-banco-read-replica.md**, **13-escalabilidade-checklist.md**.
