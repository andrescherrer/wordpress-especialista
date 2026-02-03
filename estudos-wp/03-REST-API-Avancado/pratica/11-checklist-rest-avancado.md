# Checklist – REST API Avançado

Use em revisão de endpoints. Fonte: **003-WordPress-Fase-3-REST-API-Avancado.md**.

---

## Schema e resposta

- [ ] Schema definido (get_item_schema ou array em register_rest_route) quando a resposta é estruturada
- [ ] Tipos e descrições dos campos documentados
- [ ] Context (view, edit) considerado quando há campos sensíveis

## Permissões

- [ ] permission_callback definido para cada método (GET, POST, etc.)
- [ ] Capability por recurso quando aplicável (ex.: edit_post, $post_id)
- [ ] Retorno 401 para não autenticado e 403 para sem permissão

## Paginação

- [ ] Parâmetros page e per_page com limites (per_page máx. 100)
- [ ] Headers X-WP-Total e X-WP-TotalPages na resposta de listagem

## Erros

- [ ] WP_Error com status HTTP adequado (400, 401, 403, 404, 500)
- [ ] Mensagens de erro não expõem detalhes internos em produção
- [ ] Retry/fallback considerado para chamadas externas

## Validação e sanitização

- [ ] args com validate_callback e sanitize_callback para todos os inputs
- [ ] Tipos (string, integer, etc.) e enum quando aplicável
