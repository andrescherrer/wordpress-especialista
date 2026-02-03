# Tabela – Subcomandos típicos (list, get, create, delete)

Resumo de padrões para subcomandos. Fonte: **009-WordPress-Fase-9-WP-CLI-Ferramentas.md**.

---

## Padrão por ação

| Subcomando | Descrição | Exemplo |
|------------|-----------|---------|
| **list** | Listar itens (com --format=table, --fields) | `wp meu-plugin items list` |
| **get** | Obter um item por ID/slug | `wp meu-plugin items get 123` |
| **create** | Criar item (args: título, etc.) | `wp meu-plugin items create --title="Título"` |
| **update** | Atualizar item por ID | `wp meu-plugin items update 123 --title="Novo"` |
| **delete** | Remover item (--force para confirmar) | `wp meu-plugin items delete 123 --force` |

---

## Registro com WP_CLI

```php
WP_CLI::add_command('meu-plugin items', [
    'list'   => ['Estudos_WP_Items_Command', 'list_items'],
    'get'    => ['Estudos_WP_Items_Command', 'get_item'],
    'create' => ['Estudos_WP_Items_Command', 'create_item'],
    'delete' => ['Estudos_WP_Items_Command', 'delete_item'],
]);
```

---

## Flags comuns

| Flag | Uso |
|------|-----|
| `--format=table|json|csv|count` | Formato de saída |
| `--fields=id,title,date` | Campos na listagem |
| `--dry-run` | Simular sem alterar |
| `--force` | Pular confirmação |
