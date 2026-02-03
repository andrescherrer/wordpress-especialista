# Code review e documentação

Fonte: **015-WordPress-Fase-15-Topicos-Complementares-Avancados.md** (Code Review Checklist, Documentation Standards).

---

## Code review checklist

**Funcionalidade**

- [ ] O código faz o que foi proposto?
- [ ] Casos de uso e edge cases foram considerados?
- [ ] Testes cobrem o novo código?

**Qualidade**

- [ ] Padrões do projeto (PSR/WordPress) respeitados?
- [ ] Código legível e documentado?
- [ ] Sem duplicação desnecessária?
- [ ] Performance aceitável?

**Segurança**

- [ ] Entradas validadas e sanitizadas?
- [ ] Saída escapada conforme contexto?
- [ ] Permissões verificadas (current_user_can, permission_callback)?
- [ ] Sem vulnerabilidades conhecidas?

**Testing**

- [ ] Testes unitários passam?
- [ ] Testes de integração quando aplicável?
- [ ] Cobertura adequada?

**Documentation**

- [ ] Comentários/PHPDoc nos pontos necessários?
- [ ] README atualizado?
- [ ] Changelog atualizado?

---

## Ferramentas

```bash
# PHP CodeSniffer (padrão WordPress)
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs --standard=WordPress plugin/

# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse plugin/

# Auto-correção (PHPCS)
./vendor/bin/phpcbf --standard=WordPress plugin/
```

---

## PHPDoc (exemplo)

```php
/**
 * Recupera um post pelo ID.
 *
 * @param int $post_id ID do post.
 * @return \WP_Post|null Post ou null.
 * @throws \Exception Se post_id inválido.
 */
public static function get_post($post_id) { ... }
```

Incluir: descrição, @param, @return, @throws quando relevante; @since para versão.

---

## README (estrutura)

- Descrição do plugin
- Instalação
- Uso (exemplos básicos)
- API Reference (funções principais, parâmetros, retorno)
- Contribuindo
- Licença (GPL v2 ou posterior)
