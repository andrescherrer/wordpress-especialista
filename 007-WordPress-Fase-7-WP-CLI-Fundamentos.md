# 🎯 FASE 7: WP-CLI e Ferramentas de Desenvolvimento

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar WP-CLI para automação e produtividade  

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 6](./006-WordPress-Fase-6-Shortcodes-Widgets-Gutenberg.md) | [Fase 8 →](./008-WordPress-Fase-8-Performance-Cache-Otimizacao.md)

---

## 📑 Índice

1. [Fundamentos do WP-CLI](#fundamentos-do-wp-cli)
2. [Comandos Básicos Essenciais](#comandos-básicos-essenciais)
3. [Criar Comandos WP-CLI Customizados](#criar-comandos-wp-cli-customizados)
4. [Subcomandos e Estrutura Hierárquica](#subcomandos-e-estrutura-hierárquica)
5. [Comandos com Interatividade](#comandos-com-interatividade)
6. [Comandos de Database](#comandos-de-database)
7. [Scaffolding com WP-CLI](#scaffolding-com-wp-cli)
8. [Boas Práticas](#boas-práticas)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Usar comandos WP-CLI para operações comuns do WordPress (posts, users, options)
2. ✅ Criar comandos WP-CLI customizados usando `WP_CLI::add_command()`
3. ✅ Construir estruturas de comando hierárquicas com subcomandos
4. ✅ Implementar comandos interativos com prompts e confirmações
5. ✅ Usar WP-CLI para operações de banco de dados e migrações
6. ✅ Gerar scaffolding de plugin/tema com WP-CLI
7. ✅ Integrar comandos WP-CLI em scripts de automação
8. ✅ Aplicar boas práticas e tratamento de erros do WP-CLI

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Como você cria um comando WP-CLI customizado com argumentos e opções?
- [ ] Qual é a diferença entre `WP_CLI::add_command()` e `WP_CLI::add_command()` com callable?
- [ ] Como você trata erros em comandos WP-CLI?
- [ ] Qual é o propósito de `WP_CLI::confirm()` e `WP_CLI::prompt()`?
- [ ] Como você cria subcomandos no WP-CLI?
- [ ] Qual é a diferença entre `WP_CLI::success()` e `WP_CLI::log()`?
- [ ] Como você usa WP-CLI em scripts shell para automação?
- [ ] Quais são as considerações de segurança ao usar comandos WP-CLI?

## 🛠️ Projeto Prático

**Construir:** Gerenciador de Plugin WP-CLI

Crie um plugin com comandos WP-CLI customizados que:
- Gerencie configurações do plugin via CLI
- Importe/exporte dados
- Execute tarefas de manutenção
- Inclua prompts interativos para configuração
- Tenha subcomandos para diferentes operações
- Forneça mensagens de erro úteis e indicadores de progresso

**Tempo estimado:** 6-8 horas  
**Dificuldade:** Iniciante-Intermediário

---

## ❌ Equívocos Comuns

### Equívoco 1: "WP-CLI requer acesso SSH"
**Realidade:** WP-CLI roda localmente no servidor. Você precisa de acesso shell, mas não necessariamente SSH (pode ser terminal local).

**Por que é importante:** Entender requisitos de acesso ajuda a configurar workflows de desenvolvimento corretamente.

**Como lembrar:** WP-CLI = ferramenta de linha de comando, precisa de acesso shell (local ou remoto).

### Equívoco 2: "Comandos WP-CLI são mais lentos que a interface admin"
**Realidade:** WP-CLI é frequentemente mais rápido para operações em lote e automação. Ele ignora overhead HTTP e pode processar múltiplos itens eficientemente.

**Por que é importante:** WP-CLI se destaca em operações em lote e scripting, não apenas tarefas pontuais.

**Como lembrar:** WP-CLI = mais rápido para operações em lote, automação e scripting.

### Equívoco 3: "WP-CLI não pode fazer tudo que o admin pode"
**Realidade:** WP-CLI pode fazer a maioria das tarefas admin e muitas coisas que o admin não pode (operaciones em lote, automação, scripting).

**Por que é importante:** WP-CLI é mais poderoso para desenvolvedores que a interface admin para muitas tarefas.

**Como lembrar:** WP-CLI = ferramenta de desenvolvedor, mais poderoso que admin para automação.

---

## Fundamentos do WP-CLI

### O que é WP-CLI?

**WP-CLI** é uma interface de linha de comando poderosa para WordPress que permite:

- ✅ Gerenciar WordPress sem usar navegador
- ✅ Automatizar tarefas repetitivas
- ✅ Criar comandos customizados para seu plugin
- ✅ Integrar com scripts de deploy e CI/CD
- ✅ Fazer operações em lote rapidamente
- ✅ Essencial para DevOps e desenvolvimento profissional

### Instalação

```bash
# Download do executável
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Tornar executável
chmod +x wp-cli.phar

# Mover para path global
sudo mv wp-cli.phar /usr/local/bin/wp

# Verificar instalação
wp --version
wp --info
```

### Verificar Instalação

```bash
$ wp --version
WP-CLI 2.8.0

$ wp --info
OS:            Linux 5.10.0
Shell:         /bin/bash
PHP version:   8.1.0
PHP SAPI:      cli
php.ini used:  /etc/php/8.1/cli/php.ini
MySQL version: MySQL 8.0.23
SQL modes:     
WP-CLI root dir:   /home/user/.wp-cli
WP-CLI packages dir:  /home/user/.wp-cli/packages/
WP-CLI global config:
WP-CLI project config:
WP-CLI version: 2.8.0
```

---

## Comandos Básicos Essenciais

### WordPress Core

```bash
# Baixar última versão do WordPress
wp core download

# Baixar versão específica
wp core download --version=6.0

# Instalar WordPress
wp core install \
  --url="http://exemplo.com" \
  --title="Meu Site" \
  --admin_user="admin" \
  --admin_password="senha123" \
  --admin_email="admin@exemplo.com"

# Atualizar WordPress
wp core update

# Verificar versão
wp core version

# Verificar atualizações disponíveis
wp core update-db

# Gerar arquivo wp-config.php
wp config create --dbname=wordpress --dbuser=root --dbpass=
```

### Plugins

```bash
# Listar todos os plugins
wp plugin list

# Listar com mais detalhes
wp plugin list --format=table

# Instalar plugin
wp plugin install hello-dolly

# Instalar e ativar
wp plugin install akismet --activate

# Ativar plugin
wp plugin activate hello-dolly

# Ativar todos os plugins
wp plugin activate --all

# Desativar plugin
wp plugin deactivate hello-dolly

# Desativar todos
wp plugin deactivate --all

# Deletar plugin
wp plugin delete hello-dolly

# Atualizar plugin específico
wp plugin update akismet

# Atualizar todos os plugins
wp plugin update --all

# Verificar status de plugin
wp plugin get hello-dolly

# Search para plugin
wp plugin search woocommerce

# Instalar plugin via github
wp plugin install https://github.com/username/plugin.git
```

### Temas

```bash
# Listar temas
wp theme list

# Instalar tema
wp theme install twentytwentytwo

# Ativar tema
wp theme activate twentytwentytwo

# Deletar tema
wp theme delete twentytwentythree

# Atualizar tema
wp theme update twentytwentytwo

# Atualizar todos os temas
wp theme update --all

# Obter informações do tema ativo
wp theme get twentytwentytwo
```

### Posts

```bash
# Listar posts
wp post list

# Listar com filtro
wp post list --post_type=post --posts_per_page=5

# Criar post
wp post create \
  --post_title="Meu Post" \
  --post_content="Conteúdo aqui" \
  --post_status=publish

# Criar rascunho
wp post create \
  --post_title="Rascunho" \
  --post_status=draft

# Atualizar post
wp post update 123 --post_title="Novo Título"

# Deletar post
wp post delete 123

# Forçar deleção
wp post delete 123 --force

# Restaurar post
wp post delete 123 --restore

# Obter informações do post
wp post get 123

# Listar por autor
wp post list --post_author=2
```

### Usuários

```bash
# Listar usuários
wp user list

# Criar usuário
wp user create joao joao@exemplo.com \
  --role=subscriber \
  --user_pass=senha123

# Criar com role específica
wp user create admin admin@exemplo.com \
  --role=administrator \
  --user_pass=senha123

# Listar roles disponíveis
wp role list

# Atualizar usuário
wp user update 2 --user_email=novoemail@exemplo.com

# Deletar usuário
wp user delete 2

# Deletar e reatribuir conteúdo
wp user delete 2 --reassign=1

# Obter informações do usuário
wp user get 2

# Resetar senha
wp user update 2 --prompt=user_pass

# Adicionar role
wp user add-role 2 editor

# Remover role
wp user remove-role 2 editor
```

### Database

```bash
# Exportar banco de dados
wp db export

# Exportar com nome específico
wp db export backup-20250129.sql

# Importar banco
wp db import backup-20250129.sql

# Executar query customizada
wp db query "SELECT * FROM wp_posts LIMIT 5"

# Otimizar banco
wp db optimize

# Reparar banco
wp db repair

# Criar banco
wp db create

# Deletar banco (com confirmação)
wp db drop

# Resetar (deletar e criar)
wp db reset --yes

# Checar status
wp db check

# Listar tabelas
wp db tables
```

### Cache

```bash
# Limpar cache
wp cache flush

# Tipo específico
wp cache flush --type=transient

# Deletar todos os transients
wp transient delete --all

# Deletar transient específico
wp transient delete my_transient

# Listar transients
wp transient list

# Obter valor de transient
wp transient get my_transient

# Setar transient
wp transient set my_transient "valor" 3600
```

### Search & Replace

```bash
# Replacer simples
wp search-replace 'http://old.com' 'https://new.com'

# Em todas as tabelas
wp search-replace 'http://old.com' 'https://new.com' --all-tables

# Específico para tabelas
wp search-replace 'old.com' 'new.com' wp_posts wp_postmeta

# Dry run (mostrar sem fazer)
wp search-replace 'old' 'new' --dry-run

# Mostrar resultado
wp search-replace 'old' 'new' --all-tables --verbose
```

---

## Criar Comandos WP-CLI Customizados

### Estrutura Básica

```php
<?php
/**
 * Comandos WP-CLI para o Plugin
 * 
 * Arquivo: includes/class-plugin-cli.php
 */

// Verificar se WP-CLI está disponível
if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Comandos principais do plugin
 * 
 * ## EXAMPLES
 *
 *     wp meu-plugin cleanup
 *     wp meu-plugin cleanup --days=60
 *     wp meu-plugin status
 *
 * @when after_wp_load
 */
class Meu_Plugin_CLI_Command {
    
    /**
     * Limpar dados antigos do plugin
     *
     * ## OPTIONS
     *
     * [--days=<days>]
     * : Quantos dias para manter (padrão: 30)
     * ---
     * default: 30
     * ---
     *
     * [--dry-run]
     * : Mostrar o que será deletado sem fazer
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin cleanup
     *     wp meu-plugin cleanup --days=60
     *     wp meu-plugin cleanup --dry-run
     *
     * @when after_wp_load
     */
    public function cleanup($args, $assoc_args) {
        global $wpdb;
        
        // Obter opções
        $days = isset($assoc_args['days']) ? (int) $assoc_args['days'] : 30;
        $dry_run = isset($assoc_args['dry-run']);
        
        // Calcular data
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        // Preparar query
        $table = $wpdb->prefix . 'meu_plugin_logs';
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id FROM {$table} WHERE created_at < %s",
            $date
        ));
        
        $count = count($results);
        
        // Modo dry-run
        if ($dry_run) {
            WP_CLI::log("Encontrados {$count} registros para deletar");
            WP_CLI::log("Data limite: {$date}");
            WP_CLI::log("Execute sem --dry-run para confirmar");
            return;
        }
        
        // Deletar
        if ($count > 0) {
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$table} WHERE created_at < %s",
                $date
            ));
            WP_CLI::success("Deletados {$count} registros!");
        } else {
            WP_CLI::log("Nenhum registro para deletar");
        }
    }
    
    /**
     * Ver status do plugin
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin status
     *
     * @when after_wp_load
     */
    public function status($args, $assoc_args) {
        // Obter dados
        $version = get_option('meu_plugin_version');
        $enabled = get_option('meu_plugin_enabled', false);
        
        // Exibir
        WP_CLI::log('=== Status do Plugin ===');
        WP_CLI::log('');
        WP_CLI::log('Versão: ' . $version);
        WP_CLI::log('Status: ' . ($enabled ? '✓ Habilitado' : '✗ Desabilitado'));
        
        // Verificar tabelas
        global $wpdb;
        $table = $wpdb->prefix . 'meu_plugin_data';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        
        WP_CLI::log('Registros: ' . $count);
        WP_CLI::log('');
    }
    
    /**
     * Reiniciar o plugin
     *
     * ## OPTIONS
     *
     * [--hard]
     * : Reset completo (deletar todos os dados)
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin reset
     *     wp meu-plugin reset --hard
     *
     * @when after_wp_load
     */
    public function reset($args, $assoc_args) {
        global $wpdb;
        
        $hard = isset($assoc_args['hard']);
        
        if ($hard) {
            // Confirmar
            WP_CLI::confirm('Tem certeza que deseja DELETAR todos os dados?');
            
            $table = $wpdb->prefix . 'meu_plugin_data';
            $wpdb->query("TRUNCATE TABLE {$table}");
            
            WP_CLI::success('Plugin resetado completamente!');
        } else {
            // Reset suave
            delete_option('meu_plugin_cache');
            WP_CLI::success('Cache do plugin limpo!');
        }
    }
}

// Registrar comando
WP_CLI::add_command('meu-plugin', 'Meu_Plugin_CLI_Command');
```

### Output com Formatação

```php
/**
 * Diferentes formas de output
 */
class Meu_Plugin_CLI_Output_Command {
    
    public function example($args, $assoc_args) {
        // Log simples
        WP_CLI::log('Mensagem informativa');
        
        // Sucesso (verde)
        WP_CLI::success('Operação completada com sucesso!');
        
        // Warning (amarelo)
        WP_CLI::warning('Cuidado: isso pode levar tempo');
        
        // Error (vermelho)
        WP_CLI::error('Erro grave!', false); // false = não encerra
        
        // Confirmation (pede sim/não)
        WP_CLI::confirm('Continuar?');
        
        // Tabela
        $items = [
            ['id' => 1, 'nome' => 'João', 'email' => 'joao@exemplo.com'],
            ['id' => 2, 'nome' => 'Maria', 'email' => 'maria@exemplo.com'],
        ];
        
        WP_CLI::table(
            ['id', 'nome', 'email'],
            array_map(function($item) {
                return [$item['id'], $item['nome'], $item['email']];
            }, $items)
        );
        
        // Progress bar
        $progress = \WP_CLI\Utils\make_progress_bar('Processando', 100);
        for ($i = 0; $i < 100; $i++) {
            // fazer algo
            $progress->tick();
        }
        $progress->finish();
        
        // Colorido
        WP_CLI::line('%GVerde%n');
        WP_CLI::line('%RVermelho%n');
        WP_CLI::line('%YAmarelo%n');
        WP_CLI::line('%BAzul%n');
        WP_CLI::line('%CAzul Claro%n');
        WP_CLI::line('%MMagenta%n');
        WP_CLI::line('%WBranco%n');
    }
}

WP_CLI::add_command('exemplo', 'Meu_Plugin_CLI_Output_Command');
```

---

## Subcomandos e Estrutura Hierárquica

### Criar Subcomandos

```php
<?php
/**
 * Classe para subcomandos de database
 */
class Meu_Plugin_DB_CLI_Command {
    
    /**
     * Verificar integridade do banco
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin db check
     *
     * @when after_wp_load
     */
    public function check($args, $assoc_args) {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'meu_plugin_data',
            $wpdb->prefix . 'meu_plugin_logs',
        ];
        
        WP_CLI::log('Checando integridade das tabelas...');
        WP_CLI::log('');
        
        foreach ($tables as $table) {
            WP_CLI::log("Checando {$table}...");
            $result = $wpdb->get_row("CHECK TABLE {$table}");
            
            if ($result && $result->Msg_type === 'status') {
                WP_CLI::success("✓ {$table}");
            } else {
                WP_CLI::warning("⚠ {$table}");
            }
        }
        
        WP_CLI::log('');
        WP_CLI::success('Check completo!');
    }
    
    /**
     * Reparar tabelas
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin db repair
     *
     * @when after_wp_load
     */
    public function repair($args, $assoc_args) {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'meu_plugin_data',
            $wpdb->prefix . 'meu_plugin_logs',
        ];
        
        foreach ($tables as $table) {
            WP_CLI::log("Reparando {$table}...");
            $wpdb->query("REPAIR TABLE {$table}");
        }
        
        WP_CLI::success('Tabelas reparadas!');
    }
    
    /**
     * Otimizar tabelas
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin db optimize
     *
     * @when after_wp_load
     */
    public function optimize($args, $assoc_args) {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'meu_plugin_data',
            $wpdb->prefix . 'meu_plugin_logs',
        ];
        
        WP_CLI::log('Otimizando tabelas...');
        
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE {$table}");
            WP_CLI::log("  ✓ {$table}");
        }
        
        WP_CLI::success('Otimização completa!');
    }
}

// Registrar subcomando
WP_CLI::add_command('meu-plugin db', 'Meu_Plugin_DB_CLI_Command');
```

---

## Comandos com Interatividade

### Prompts e Confirmações

```php
<?php
/**
 * Classe para comandos interativos
 */
class Meu_Plugin_Interactive_CLI_Command {
    
    /**
     * Configurar plugin interativamente
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin setup
     *
     * @when after_wp_load
     */
    public function setup($args, $assoc_args) {
        WP_CLI::log('');
        WP_CLI::log('%G=== Configuração do Meu Plugin ===%n');
        WP_CLI::log('');
        
        // Prompt simples
        $api_key = WP_CLI::prompt('Sua API Key');
        update_option('meu_plugin_api_key', sanitize_text_field($api_key));
        
        // Prompt com padrão
        $site_name = WP_CLI::prompt(
            'Nome do site',
            get_bloginfo('name')
        );
        update_option('meu_plugin_site_name', sanitize_text_field($site_name));
        
        // Menu de seleção
        $mode = WP_CLI::prompt(
            'Modo de operação',
            ['development', 'staging', 'production']
        );
        update_option('meu_plugin_mode', $mode);
        
        // Confirmação (Y/n)
        if (WP_CLI::confirm('Habilitar cache?')) {
            update_option('meu_plugin_cache_enabled', true);
            
            // Prompt com validação
            $duration = WP_CLI::prompt(
                'Duração do cache (minutos)',
                '60',
                function($response) {
                    return is_numeric($response) && $response > 0;
                }
            );
            update_option('meu_plugin_cache_duration', absint($duration));
        }
        
        // Email
        $email = WP_CLI::prompt(
            'Email para notificações',
            get_option('admin_email'),
            function($response) {
                return is_email($response);
            }
        );
        update_option('meu_plugin_notification_email', sanitize_email($email));
        
        // Resumo
        WP_CLI::log('');
        WP_CLI::success('Configuração concluída!');
        WP_CLI::log('');
        WP_CLI::log('%G=== Resumo ===%n');
        WP_CLI::log('API Key: ' . str_repeat('*', 20));
        WP_CLI::log('Site: ' . $site_name);
        WP_CLI::log('Modo: ' . $mode);
        WP_CLI::log('Cache: ' . (get_option('meu_plugin_cache_enabled') ? 'Habilitado' : 'Desabilitado'));
        WP_CLI::log('Email: ' . $email);
        WP_CLI::log('');
    }
}

WP_CLI::add_command('meu-plugin setup', 'Meu_Plugin_Interactive_CLI_Command');
```

---

## Comandos de Database

### CRUD Completo

```php
<?php
/**
 * Comando para gerenciar dados customizados
 */
class Meu_Plugin_Data_CLI_Command {
    
    /**
     * Listar registros
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Formato da saída (table, json, csv)
     * ---
     * default: table
     * ---
     *
     * [--limit=<limit>]
     * : Quantidade de registros
     * ---
     * default: 20
     * ---
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin data list
     *     wp meu-plugin data list --format=json --limit=50
     *
     * @when after_wp_load
     */
    public function list($args, $assoc_args) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'meu_plugin_data';
        $limit = isset($assoc_args['limit']) ? (int) $assoc_args['limit'] : 20;
        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';
        
        $results = $wpdb->get_results(
            "SELECT id, name, value, created_at FROM {$table} LIMIT {$limit}"
        );
        
        if (empty($results)) {
            WP_CLI::error('Nenhum registro encontrado');
        }
        
        if ($format === 'json') {
            WP_CLI::line(json_encode($results, JSON_PRETTY_PRINT));
        } else if ($format === 'csv') {
            echo "id,name,value,created_at\n";
            foreach ($results as $row) {
                echo "{$row->id},{$row->name},{$row->value},{$row->created_at}\n";
            }
        } else {
            // Table format
            WP_CLI::table(
                ['ID', 'Name', 'Value', 'Created'],
                array_map(function($row) {
                    return [
                        $row->id,
                        $row->name,
                        $row->value,
                        $row->created_at,
                    ];
                }, $results)
            );
        }
    }
    
    /**
     * Criar registro
     *
     * ## OPTIONS
     *
     * <name>
     * : Nome do registro
     *
     * <value>
     * : Valor do registro
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin data create "Chave 1" "Valor 1"
     *
     * @when after_wp_load
     */
    public function create($args, $assoc_args) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'meu_plugin_data';
        $name = $args[0];
        $value = $args[1];
        
        $result = $wpdb->insert(
            $table,
            [
                'name' => $name,
                'value' => $value,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s']
        );
        
        if ($result) {
            WP_CLI::success("Registro criado com ID: {$wpdb->insert_id}");
        } else {
            WP_CLI::error('Falha ao criar registro');
        }
    }
    
    /**
     * Atualizar registro
     *
     * ## OPTIONS
     *
     * <id>
     * : ID do registro
     *
     * <name>
     * : Novo nome
     *
     * <value>
     * : Novo valor
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin data update 1 "Novo Nome" "Novo Valor"
     *
     * @when after_wp_load
     */
    public function update($args, $assoc_args) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'meu_plugin_data';
        $id = $args[0];
        $name = $args[1];
        $value = $args[2];
        
        $result = $wpdb->update(
            $table,
            ['name' => $name, 'value' => $value],
            ['id' => $id],
            ['%s', '%s'],
            ['%d']
        );
        
        if ($result !== false) {
            WP_CLI::success("Registro {$id} atualizado!");
        } else {
            WP_CLI::error('Falha ao atualizar registro');
        }
    }
    
    /**
     * Deletar registro
     *
     * ## OPTIONS
     *
     * <id>
     * : ID do registro
     *
     * [--force]
     * : Não pedir confirmação
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin data delete 1
     *     wp meu-plugin data delete 1 --force
     *
     * @when after_wp_load
     */
    public function delete($args, $assoc_args) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'meu_plugin_data';
        $id = $args[0];
        
        // Confirmar
        if (!isset($assoc_args['force'])) {
            WP_CLI::confirm("Deletar registro {$id}?");
        }
        
        $result = $wpdb->delete(
            $table,
            ['id' => $id],
            ['%d']
        );
        
        if ($result) {
            WP_CLI::success("Registro {$id} deletado!");
        } else {
            WP_CLI::error('Falha ao deletar registro');
        }
    }
}

WP_CLI::add_command('meu-plugin data', 'Meu_Plugin_Data_CLI_Command');
```

---

## Scaffolding com WP-CLI

### Gerar Estrutura de Código

```php
<?php
/**
 * Comandos de Scaffolding
 */
class Meu_Plugin_Scaffold_CLI_Command {
    
    /**
     * Gerar novo módulo
     *
     * ## OPTIONS
     *
     * <name>
     * : Nome do módulo (ex: Payment)
     *
     * [--type=<type>]
     * : Tipo do módulo (class, service, controller, model)
     * ---
     * default: class
     * options:
     *   - class
     *   - service
     *   - controller
     *   - model
     * ---
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin scaffold module Payment
     *     wp meu-plugin scaffold module Payment --type=service
     *
     * @when after_wp_load
     */
    public function module($args, $assoc_args) {
        $name = $args[0];
        $type = isset($assoc_args['type']) ? $assoc_args['type'] : 'class';
        
        $class_name = $this->sanitize_class_name($name);
        
        switch ($type) {
            case 'service':
                $this->generate_service($class_name);
                break;
            case 'controller':
                $this->generate_controller($class_name);
                break;
            case 'model':
                $this->generate_model($class_name);
                break;
            default:
                $this->generate_class($class_name);
        }
    }
    
    /**
     * Gerar classe simples
     */
    private function generate_class($name) {
        $template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Class
 */

namespace MeuPlugin;

class {CLASS_NAME} {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize
    }
    
    /**
     * Initialize hooks
     */
    public function init() {
        add_action('init', [$this, 'setup']);
    }
    
    /**
     * Setup
     */
    public function setup() {
        // Setup code here
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        
        $file = MEU_PLUGIN_PATH . 'src/' . $name . '.php';
        
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        
        if (file_exists($file)) {
            WP_CLI::error("Arquivo já existe: {$file}");
        }
        
        file_put_contents($file, $template);
        WP_CLI::success("Classe criada: {$file}");
    }
    
    /**
     * Gerar Service
     */
    private function generate_service($name) {
        $template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Service
 */

namespace MeuPlugin\Services;

class {CLASS_NAME} {
    
    /**
     * Execute operation
     */
    public function execute() {
        // Service logic here
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        $file = MEU_PLUGIN_PATH . 'src/Services/' . $name . '.php';
        
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        
        file_put_contents($file, $template);
        WP_CLI::success("Service criado: {$file}");
    }
    
    /**
     * Gerar Controller
     */
    private function generate_controller($name) {
        $template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Controller
 */

namespace MeuPlugin\Controllers;

class {CLASS_NAME} {
    
    /**
     * Handle request
     */
    public function handle() {
        // Controller logic here
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        $file = MEU_PLUGIN_PATH . 'src/Controllers/' . $name . '.php';
        
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        
        file_put_contents($file, $template);
        WP_CLI::success("Controller criado: {$file}");
    }
    
    /**
     * Gerar Model
     */
    private function generate_model($name) {
        $table_name = strtolower($this->to_snake_case($name));
        
        $template = <<<'PHP'
<?php
/**
 * {CLASS_NAME} Model
 */

namespace MeuPlugin\Models;

class {CLASS_NAME} {
    
    private $table = '{TABLE_NAME}';
    
    /**
     * Get all
     */
    public function all() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . $this->table);
    }
    
    /**
     * Get by ID
     */
    public function get($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . $wpdb->prefix . $this->table . " WHERE id = %d",
            $id
        ));
    }
}

PHP;
        
        $template = str_replace(['{CLASS_NAME}', '{TABLE_NAME}'], [$name, $table_name], $template);
        $file = MEU_PLUGIN_PATH . 'src/Models/' . $name . '.php';
        
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        
        file_put_contents($file, $template);
        WP_CLI::success("Model criado: {$file}");
    }
    
    /**
     * Helper: Sanitizar nome da classe
     */
    private function sanitize_class_name($name) {
        return str_replace(['-', '_', ' '], '', ucwords($name, '-_ '));
    }
    
    /**
     * Helper: Converter para snake_case
     */
    private function to_snake_case($name) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
}

WP_CLI::add_command('meu-plugin scaffold', 'Meu_Plugin_Scaffold_CLI_Command');
```

---

## Boas Práticas

### Checklist de Comandos de Qualidade

```markdown
# Checklist WP-CLI Commands

## Estrutura
- [ ] Usar namespace próprio para o comando
- [ ] Herdar de `WP_CLI_Command` (opcional mas recomendado)
- [ ] Usar `@when after_wp_load` nas callbacks
- [ ] Documentar com comentários PHPDoc

## Parâmetros
- [ ] Usar `<param>` para obrigatórios
- [ ] Usar `[--option]` para opcionais
- [ ] Definir padrões com `default:`
- [ ] Usar `options:` para valores permitidos

## Output
- [ ] Usar `WP_CLI::success()` para sucesso
- [ ] Usar `WP_CLI::warning()` para aviso
- [ ] Usar `WP_CLI::error()` para erro
- [ ] Usar `WP_CLI::log()` para informação
- [ ] Usar `WP_CLI::table()` para tabelas

## Segurança
- [ ] Sempre sanitizar entrada do usuário
- [ ] Usar `$wpdb->prepare()` para queries
- [ ] Verificar capabilities/permissões
- [ ] Pedir confirmação antes de deletar

## Exemplos
- [ ] Incluir `## EXAMPLES` na documentação
- [ ] Mostrar uso com e sem opções
- [ ] Exemplos realistas e úteis

## Performance
- [ ] Usar progress bars para operações longas
- [ ] Executar queries em lotes para grandes volumes
- [ ] Usar índices no banco de dados
- [ ] Considerar usar transações
```

### Estrutura de Projeto

```
meu-plugin/
├── includes/
│   ├── class-plugin.php
│   ├── class-cli.php                 # Comando principal
│   └── cli/
│       ├── class-db-command.php       # Subcomando: db
│       ├── class-data-command.php     # Subcomando: data
│       ├── class-setup-command.php    # Subcomando: setup
│       └── class-scaffold-command.php # Subcomando: scaffold
├── src/
│   ├── Models/
│   ├── Services/
│   └── Controllers/
├── tests/
└── meu-plugin.php
```

### Registrar Comandos no Plugin

```php
<?php
// meu-plugin.php

// Registrar comandos WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    require_once MEU_PLUGIN_PATH . 'includes/class-cli.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-db-command.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-data-command.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-setup-command.php';
    require_once MEU_PLUGIN_PATH . 'includes/cli/class-scaffold-command.php';
    
    WP_CLI::add_command('meu-plugin', 'Meu_Plugin_CLI_Command');
    WP_CLI::add_command('meu-plugin db', 'Meu_Plugin_DB_CLI_Command');
    WP_CLI::add_command('meu-plugin data', 'Meu_Plugin_Data_CLI_Command');
    WP_CLI::add_command('meu-plugin setup', 'Meu_Plugin_Setup_CLI_Command');
    WP_CLI::add_command('meu-plugin scaffold', 'Meu_Plugin_Scaffold_CLI_Command');
}
```

---

## Resumo da Fase 7

### ✅ Tópicos Abordados

1. **WP-CLI Fundamentos** - Instalação, conceitos, uso básico
2. **Comandos Básicos** - Core, plugins, temas, posts, usuários, database
3. **Criar Comandos Customizados** - Estrutura, parâmetros, output
4. **Subcomandos** - Estrutura hierárquica (db, data, setup, scaffold)
5. **Interatividade** - Prompts, confirmações, menus
6. **CRUD via CLI** - Listar, criar, atualizar, deletar registros
7. **Scaffolding** - Gerar classes, services, controllers, models
8. **Boas Práticas** - Checklist, segurança, performance

### 🎯 Competências Adquiridas

✅ Automação completa de tarefas WordPress  
✅ Criar ferramentas poderosas para sua equipe  
✅ Deploy e DevOps com WP-CLI  
✅ Scaffolding para geração de código  
✅ Integração com scripts de automação  
✅ CRUD desde a linha de comando  

### 🚀 Próximas Fases

- **Fase 8**: Performance e Caching Avançado
- **Fase 9**: Testing, Debugging e Deploy
- **Fase 10**: Multisite e Internacionalização (i18n/l10n)
- **Fase 11**: Segurança Avançada e Boas Práticas Finais

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress
