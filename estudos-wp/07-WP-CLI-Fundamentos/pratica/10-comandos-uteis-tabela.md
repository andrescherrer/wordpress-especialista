# Tabela – Comandos WP-CLI úteis

Resumo de comandos comuns. Fonte: **007-WordPress-Fase-7-WP-CLI-Fundamentos.md**.

---

## Core

| Comando | Descrição |
|---------|-----------|
| `wp core download` | Baixar WordPress |
| `wp core config` | Criar wp-config.php |
| `wp core install` | Instalar site (title, url, admin_user, admin_password, admin_email) |
| `wp core update` | Atualizar WordPress |
| `wp core version` | Exibir versão |

---

## Plugin / Theme

| Comando | Descrição |
|---------|-----------|
| `wp plugin list` | Listar plugins |
| `wp plugin install <slug>` | Instalar plugin |
| `wp plugin activate <slug>` | Ativar |
| `wp plugin deactivate <slug>` | Desativar |
| `wp theme list` | Listar temas |
| `wp theme install <slug>` | Instalar tema |

---

## Banco de dados

| Comando | Descrição |
|---------|-----------|
| `wp db export [arquivo.sql]` | Exportar banco |
| `wp db import <arquivo>` | Importar |
| `wp db query "SELECT ..."` | Executar query |
| `wp db reset` | Resetar banco (cuidado!) |

---

## Opções e usuários

| Comando | Descrição |
|---------|-----------|
| `wp option get <name>` | Obter opção |
| `wp option update <name> <value>` | Atualizar |
| `wp user list` | Listar usuários |
| `wp user create <login> <email> --role=administrator` | Criar usuário |

---

## Formato de saída

| Flag | Descrição |
|------|-----------|
| `--format=table` | Tabela (padrão para listas) |
| `--format=json` | JSON |
| `--format=csv` | CSV |
| `--format=count` | Apenas número de itens |
