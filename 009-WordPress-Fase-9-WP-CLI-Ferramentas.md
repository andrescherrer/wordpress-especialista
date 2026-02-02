# 🎯 FASE 9: WP-CLI e Ferramentas de Desenvolvimento

**Versão:** 1.0  
**Data:** Janeiro 2026  
**Nível:** Especialista em PHP  
**Objetivo:** Dominar WP-CLI, automação e ferramentas profissionais de desenvolvimento

---

**Navegação:** [Índice](./000-WordPress-Indice-Topicos.md) | [← Fase 8](./008-WordPress-Fase-8-Performance-Cache-Otimizacao.md) | [Fase 10 →](./010-WordPress-Fase-10-Testes-Debug-Implantacao.md)

---

## 📑 Índice

1. [Fundamentos do WP-CLI](#fundamentos-do-wp-cli)
2. [Comandos Básicos Essenciais](#comandos-básicos-essenciais)
3. [Criar Comandos WP-CLI Customizados](#criar-comandos-wp-cli-customizados)
4. [Subcomandos e Hierarquia](#subcomandos-e-hierarquia)
5. [Comandos com Interatividade](#comandos-com-interatividade)
6. [Comandos com Testes](#comandos-com-testes)
7. [Scaffolding com WP-CLI](#scaffolding-com-wp-cli)
8. [Migrations e Database](#migrations-e-database)
9. [Debugging Tools](#debugging-tools)
10. [Scripts de Automação](#scripts-de-automação)
11. [CI/CD com GitHub Actions](#cicd-com-github-actions)
12. [Boas Práticas](#boas-práticas)

---

## 🎯 Objetivos de Aprendizado

Ao final desta fase, você será capaz de:

1. ✅ Usar comandos WP-CLI avançados para operações complexas
2. ✅ Criar comandos WP-CLI customizados com testes e validação
3. ✅ Construir scripts de automação usando WP-CLI
4. ✅ Integrar WP-CLI com pipelines CI/CD (GitHub Actions)
5. ✅ Usar WP-CLI para migrações de banco de dados e manipulação de dados
6. ✅ Implementar ferramentas de debugging e tratamento de erros no WP-CLI
7. ✅ Criar ferramentas de scaffolding para desenvolvimento rápido
8. ✅ Aplicar workflows de desenvolvimento profissional com WP-CLI

## 📝 Autoavaliação

Teste seu entendimento:

- [ ] Como você testa comandos WP-CLI programaticamente?
- [ ] Qual é a melhor forma de tratar migrações de banco de dados com WP-CLI?
- [ ] Como você integra comandos WP-CLI em workflows do GitHub Actions?
- [ ] Quais são as considerações de segurança ao expor comandos WP-CLI?
- [ ] Como você cria bibliotecas reutilizáveis de comandos WP-CLI?
- [ ] Qual é a diferença entre comandos WP-CLI e hooks do WordPress?
- [ ] Como você trata comandos WP-CLI de longa duração sem timeout?
- [ ] Quais são as melhores práticas para documentação de comandos WP-CLI?

## 🛠️ Projeto Prático

**Construir:** Suite de Automação de Desenvolvimento

Crie uma suite abrangente de desenvolvimento baseada em WP-CLI que:
- Gere scaffolding de plugin/tema
- Execute migrações de banco de dados
- Execute testes automatizados
- Realize verificações de qualidade de código
- Integre com pipelines CI/CD
- Inclua ferramentas de debugging e profiling

**Tempo estimado:** 10-12 horas  
**Dificuldade:** Avançado

---

## ❌ Equívocos Comuns

### Equívoco 1: "Comandos WP-CLI não podem ser testados"
**Realidade:** Comandos WP-CLI podem e devem ser testados usando PHPUnit, assim como qualquer outro código PHP.

**Por que é importante:** Testar comandos WP-CLI garante confiabilidade e previne regressões.

**Como lembrar:** Comandos WP-CLI = código PHP = podem ser testados com PHPUnit.

### Equívoco 2: "WP-CLI é apenas para tarefas pontuais"
**Realidade:** WP-CLI se destaca em automação, integração CI/CD e tarefas repetitivas. É uma ferramenta poderosa de automação.

**Por que é importante:** Entender capacidades de automação do WP-CLI desbloqueia workflows poderosos.

**Como lembrar:** WP-CLI = ferramenta de automação, não apenas comandos manuais.

### Equívoco 3: "Comandos WP-CLI sempre precisam do WordPress carregado"
**Realidade:** Alguns comandos WP-CLI podem funcionar sem bootstrap completo do WordPress, melhorando performance para tarefas específicas.

**Por que é importante:** Entender quando o WordPress precisa ser carregado ajuda a otimizar performance de comandos.

**Como lembrar:** Alguns comandos = WordPress não necessário. Maioria dos comandos = WordPress requerido.

---

## Fundamentos do WP-CLI

### O que é WP-CLI?

**WP-CLI** é a interface de linha de comando oficial do WordPress. Permite gerenciar WordPress sem usar navegador.

**Características principais:**
- Gerenciar WordPress via terminal
- Automatizar tarefas repetitivas
- Criar comandos customizados
- Essencial para DevOps e CI/CD
- Scripts de migração e setup

### Instalação

```bash
# Download
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Tornar executável
chmod +x wp-cli.phar

# Mover para path global
sudo mv wp-cli.phar /usr/local/bin/wp

# Verificar instalação
wp --info

# Atualizar
wp cli update
```

### Informações do Sistema

```bash
# Ver versão e informações
wp --info

# Output típico:
# OS:  Linux 5.10.0
# Shell:  /bin/bash
# PHP binary:  /usr/bin/php7.4
# PHP version:  7.4.3
# php.ini used:  /etc/php/7.4/cli/php.ini
# WP-CLI root dir:  phar://wp-cli.phar
# WP-CLI version:  2.6.0
```

---

## Comandos Básicos Essenciais

### Core WordPress

```bash
# Baixar WordPress
wp core download                           # Versão mais recente
wp core download --version=5.8             # Versão específica
wp core download --version=5.8 --locale=pt_BR

# Criar arquivo de configuração
wp config create \
    --dbname=wordpress \
    --dbuser=root \
    --dbpass=senha \
    --dbhost=localhost \
    --dbprefix=wp_

# Instalar WordPress
wp core install \
    --url=http://localhost \
    --title="Meu Site" \
    --admin_user=admin \
    --admin_password=senha123 \
    --admin_email=admin@exemplo.com

# Verificar versão
wp core version                            # Apenas versão
wp core version --extra                    # Com informações extras

# Atualizar WordPress
wp core check-update                       # Verificar disponibilidade
wp core update                             # Atualizar para latest
wp core update --minor                     # Apenas minor updates
wp core update-db                          # Atualizar banco após update

# Reinstalar core (sem afetar dados)
wp core download --force

# Modo de manutenção
wp maintenance-mode activate
wp maintenance-mode deactivate
wp maintenance-mode status

# Verificar integridade
wp core verify-checksums                   # Verificar core files
wp core verify-checksums --all             # Com temas e plugins
```

### Plugins

```bash
# Listar plugins
wp plugin list                             # Todos
wp plugin list --status=active             # Apenas ativos
wp plugin list --format=json               # Formato JSON
wp plugin list --format=csv                # Formato CSV

# Instalar plugins
wp plugin install wordpress-seo            # Do repositório oficial
wp plugin install https://exemplo.com/plugin.zip
wp plugin install ./meu-plugin.zip --activate

# Ativar e desativar
wp plugin activate wordpress-seo           # Ativar específico
wp plugin activate --all                   # Ativar todos
wp plugin deactivate wordpress-seo
wp plugin deactivate --all
wp plugin toggle wordpress-seo             # Alternar status

# Atualizar
wp plugin update --all                     # Todos
wp plugin update wordpress-seo             # Específico
wp plugin update --all --dry-run            # Simular (sem aplicar)

# Verificar vulnerabilidades
wp plugin verify-checksums --all

# Desinstalar (remove dados)
wp plugin uninstall wordpress-seo --deactivate

# Buscar no repositório
wp plugin search seo --per-page=5 --format=table
```

### Temas

```bash
# Listar temas
wp theme list                              # Todos
wp theme list --status=active              # Apenas ativo

# Instalar temas
wp theme install twentytwentytwo           # Do repositório
wp theme install ./tema-local.zip --activate

# Ativar
wp theme activate twentytwentytwo

# Deletar
wp theme delete twentytwentytwo

# Atualizar
wp theme update --all
```

### Posts e Conteúdo

```bash
# Listar posts
wp post list                               # Todos
wp post list --post_type=page             # Apenas pages
wp post list --post_type=produto          # CPT customizado
wp post list --posts_per_page=5           # Limitar
wp post list --format=json                # Formato JSON

# Criar posts
wp post create \
    --post_type=post \
    --post_title="Novo Post" \
    --post_content="Conteúdo do post" \
    --post_status=publish

# Atualizar posts
wp post update 123 --post_title="Novo Título"
wp post update 123 --post_content="Novo conteúdo" --post_status=draft

# Deletar posts
wp post delete 123                         # Enviar para lixo
wp post delete 123 --force                 # Deletar permanentemente

# Meta dados
wp post meta add 123 meta_key meta_value
wp post meta get 123 meta_key
wp post meta update 123 meta_key novo_valor
wp post meta delete 123 meta_key
```

### Usuários

```bash
# Listar usuários
wp user list                               # Todos
wp user list --role=administrator         # Apenas admins
wp user list --format=json

# Criar usuários
wp user create novo_user email@teste.com   # Com senha aleatória
wp user create novo_user email@teste.com --user_pass=senha123
wp user create novo_user email@teste.com --role=editor

# Atualizar usuários
wp user update 1 --user_email=novo@email.com
wp user update 1 --role=administrator

# Deletar usuários
wp user delete 1                           # Com reassign padrão
wp user delete 1 --reassign=2              # Reatribuir posts para ID 2

# Gerenciar roles
wp user set-role 1 administrator
wp user remove-role 1

# Senha
wp user update 1 --user_pass=novaSenha123
wp user list --format=json | grep user_login
```

### Banco de Dados

```bash
# Informações
wp db check                                # Verificar integridade
wp db size                                 # Tamanho total
wp db size --tables                        # Por tabela
wp db tables                               # Listar tabelas

# Backup e restauração
wp db export backup.sql                    # Exportar dump
wp db export - | gzip > backup.sql.gz      # Com compressão
wp db import backup.sql                    # Importar
zcat backup.sql.gz | wp db import -

# Queries diretas
wp db query "SELECT COUNT(*) FROM wp_posts"
wp db query "SHOW PROCESSLIST"
wp db query "SELECT * FROM wp_posts LIMIT 5"

# Otimização
wp db optimize                             # Otimizar tabelas
wp db repair                               # Reparar tabelas

# Search and Replace (migrações)
wp search-replace 'http://antigo.com' 'https://novo.com' --dry-run
wp search-replace 'http://antigo.com' 'https://novo.com' --all-tables
wp search-replace '/caminho/antigo' '/caminho/novo' --all-tables
```

### Cache e Transients

```bash
# Limpar cache de objetos
wp cache flush

# Gerenciar transients
wp transient list                          # Listar todos
wp transient get my_transient              # Obter valor
wp transient set my_transient "valor"      # Definir
wp transient set my_transient "valor" 3600 # Com timeout
wp transient delete my_transient           # Deletar
wp transient delete --all                  # Deletar todos

# Opções
wp option list                             # Listar todas
wp option get siteurl                      # Obter valor
wp option set siteurl "http://novo.com"
wp option delete siteurl
```

---

## Criar Comandos WP-CLI Customizados

### Estrutura Básica de um Comando

```php
<?php
/**
 * Comandos WP-CLI para o Plugin
 * 
 * Arquivo: includes/class-cli-commands.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Comandos principais do plugin
 */
class Meu_Plugin_CLI_Command {
    
    /**
     * Limpar dados antigos
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin cleanup
     *     wp meu-plugin cleanup --days=60
     *
     * ## OPTIONS
     *
     * [--days=<days>]
     * : Número de dias para manter (padrão: 30)
     *
     * [--dry-run]
     * : Simular sem deletar realmente
     *
     * @when after_wp_load
     */
    public function cleanup($args, $assoc_args) {
        $days = isset($assoc_args['days']) ? absint($assoc_args['days']) : 30;
        $dry_run = isset($assoc_args['dry-run']);
        
        global $wpdb;
        
        $date = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}meu_plugin_logs WHERE created_at < %s",
            $date
        );
        
        $count = $wpdb->get_var($query);
        
        if ($count === 0) {
            WP_CLI::log("Nenhum dado para limpar (anterior a {$days} dias)");
            return;
        }
        
        if (!$dry_run) {
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}meu_plugin_logs WHERE created_at < %s",
                $date
            ));
        }
        
        $mode = $dry_run ? '(DRY RUN) ' : '';
        WP_CLI::success("{$mode}{$count} registros deletados!");
    }
    
    /**
     * Processar fila de jobs
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin process-queue
     *     wp meu-plugin process-queue --limit=50
     *
     * ## OPTIONS
     *
     * [--limit=<limit>]
     * : Número máximo de jobs (padrão: 20)
     *
     * [--force]
     * : Forçar reprocessamento de falhas
     *
     * @when after_wp_load
     */
    public function process_queue($args, $assoc_args) {
        $limit = isset($assoc_args['limit']) ? absint($assoc_args['limit']) : 20;
        $force = isset($assoc_args['force']);
        
        global $wpdb;
        
        $where = $force ? '' : 'AND status != "failed"';
        
        $query = $wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}meu_plugin_queue 
             WHERE status = 'pending' {$where}
             LIMIT %d",
            $limit
        );
        
        $jobs = $wpdb->get_col($query);
        
        if (empty($jobs)) {
            WP_CLI::log('Nenhum job pendente');
            return;
        }
        
        $progress = WP_CLI\Utils\make_progress_bar('Processando', count($jobs));
        
        foreach ($jobs as $job_id) {
            try {
                $this->process_job($job_id);
                $progress->tick();
            } catch (Exception $e) {
                WP_CLI::warning("Job {$job_id} falhou: " . $e->getMessage());
            }
        }
        
        $progress->finish();
        WP_CLI::success('Processamento concluído!');
    }
    
    /**
     * Processar um job individual
     */
    private function process_job($job_id) {
        global $wpdb;
        
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}meu_plugin_queue WHERE id = %d",
            $job_id
        ));
        
        if (!$job) {
            throw new Exception('Job não encontrado');
        }
        
        $data = json_decode($job->data, true);
        
        switch ($job->type) {
            case 'send_email':
                $this->send_email_job($data);
                break;
            case 'sync_api':
                $this->sync_api_job($data);
                break;
            default:
                throw new Exception("Tipo de job desconhecido: {$job->type}");
        }
        
        // Marcar como concluído
        $wpdb->update(
            $wpdb->prefix . 'meu_plugin_queue',
            [
                'status' => 'completed',
                'completed_at' => current_time('mysql'),
            ],
            ['id' => $job_id],
            ['%s', '%s'],
            ['%d']
        );
    }
    
    /**
     * Enviar email
     */
    private function send_email_job($data) {
        wp_mail(
            $data['to'],
            $data['subject'],
            $data['message']
        );
    }
    
    /**
     * Sincronizar com API
     */
    private function sync_api_job($data) {
        // Implementar sincronização
    }
    
    /**
     * Verificar integridade do plugin
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin check-integrity
     *
     * @when after_wp_load
     */
    public function check_integrity($args, $assoc_args) {
        WP_CLI::log('Verificando integridade...');
        
        $issues = [];
        
        // Verificar tabelas
        global $wpdb;
        $tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}meu_plugin_%'");
        
        if (empty($tables)) {
            $issues[] = 'Nenhuma tabela encontrada!';
        }
        
        // Verificar opções
        $required_options = ['meu_plugin_version', 'meu_plugin_settings'];
        foreach ($required_options as $option) {
            if (!get_option($option)) {
                $issues[] = "Opção ausente: {$option}";
            }
        }
        
        // Verificar arquivos críticos
        $critical_files = [
            'includes/class-core.php',
            'admin/class-admin.php',
        ];
        
        $plugin_dir = dirname(MEU_PLUGIN_FILE);
        
        foreach ($critical_files as $file) {
            if (!file_exists($plugin_dir . '/' . $file)) {
                $issues[] = "Arquivo ausente: {$file}";
            }
        }
        
        if (empty($issues)) {
            WP_CLI::success('✓ Plugin íntegro!');
        } else {
            foreach ($issues as $issue) {
                WP_CLI::warning('✗ ' . $issue);
            }
        }
    }
    
    /**
     * Reparar dados do plugin
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin repair
     *
     * @when after_wp_load
     */
    public function repair($args, $assoc_args) {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'meu_plugin_data',
            $wpdb->prefix . 'meu_plugin_logs',
        ];
        
        $progress = WP_CLI\Utils\make_progress_bar('Reparando', count($tables));
        
        foreach ($tables as $table) {
            $wpdb->query("REPAIR TABLE {$table}");
            $progress->tick();
        }
        
        $progress->finish();
        WP_CLI::success('Tabelas reparadas!');
    }
}

// Registrar comando
WP_CLI::add_command('meu-plugin', 'Meu_Plugin_CLI_Command');
```

---

## Subcomandos e Hierarquia

```php
<?php
/**
 * Subcomandos WP-CLI
 */

/**
 * Comandos de Banco de Dados
 */
class Meu_Plugin_DB_CLI_Command {
    
    /**
     * Inicializar banco de dados
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin db init
     *
     * @when after_wp_load
     */
    public function init($args, $assoc_args) {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}meu_plugin_data (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            uuid VARCHAR(36) NOT NULL UNIQUE,
            data LONGTEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_uuid (uuid),
            INDEX idx_created_at (created_at)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        
        WP_CLI::success('Banco de dados inicializado!');
    }
    
    /**
     * Resetar banco de dados
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin db reset
     *     wp meu-plugin db reset --confirm
     *
     * @when after_wp_load
     */
    public function reset($args, $assoc_args) {
        if (!isset($assoc_args['confirm'])) {
            WP_CLI::error('Operação cancelada. Use --confirm para confirmar');
        }
        
        global $wpdb;
        
        $tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}meu_plugin_%'");
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE {$table}");
        }
        
        // Reinicializar
        $this->init($args, $assoc_args);
    }
    
    /**
     * Exportar dados
     *
     * ## OPTIONS
     *
     * [--output=<file>]
     * : Arquivo de saída (padrão: export.json)
     *
     * [--format=<format>]
     * : Formato (json, csv)
     *
     * @when after_wp_load
     */
    public function export($args, $assoc_args) {
        global $wpdb;
        
        $output = isset($assoc_args['output']) ? $assoc_args['output'] : 'export.json';
        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'json';
        
        $data = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}meu_plugin_data",
            ARRAY_A
        );
        
        if ($format === 'json') {
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            $content = $this->array_to_csv($data);
        }
        
        file_put_contents($output, $content);
        
        WP_CLI::success("Dados exportados para {$output}");
    }
    
    /**
     * Converter array para CSV
     */
    private function array_to_csv($data) {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://memory', 'w');
        
        // Headers
        fputcsv($output, array_keys((array)$data[0]));
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, (array)$row);
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        
        return $content;
    }
}

// Registrar subcomando
WP_CLI::add_command('meu-plugin db', 'Meu_Plugin_DB_CLI_Command');

// Uso:
// wp meu-plugin db init
// wp meu-plugin db reset --confirm
// wp meu-plugin db export --output=dados.json
// wp meu-plugin db export --format=csv
```

---

## Comandos com Interatividade

```php
<?php
/**
 * Comandos interativos WP-CLI
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
        WP_CLI::log('╔═══════════════════════════════════════════╗');
        WP_CLI::log('║   Configuração do Meu Plugin             ║');
        WP_CLI::log('╚═══════════════════════════════════════════╝');
        WP_CLI::log('');
        
        // API Key (com validação)
        $api_key = '';
        while (empty($api_key)) {
            $api_key = WP_CLI\Utils\prompt('API Key');
            if (strlen($api_key) < 20) {
                WP_CLI::warning('API Key deve ter no mínimo 20 caracteres');
                $api_key = '';
            }
        }
        update_option('meu_plugin_api_key', sanitize_text_field($api_key));
        
        // Modo de operação
        $modes = ['development', 'staging', 'production'];
        WP_CLI::log('');
        WP_CLI::log('Selecione o modo de operação:');
        foreach ($modes as $i => $mode) {
            WP_CLI::log("  " . ($i + 1) . ") $mode");
        }
        $mode_choice = WP_CLI\Utils\prompt('Escolha (1-3)', '1');
        $mode_idx = absint($mode_choice) - 1;
        
        if ($mode_idx < 0 || $mode_idx >= count($modes)) {
            WP_CLI::error('Opção inválida!');
        }
        
        update_option('meu_plugin_mode', $modes[$mode_idx]);
        
        // Cache
        WP_CLI::log('');
        $enable_cache = WP_CLI\Utils\prompt('Habilitar cache? (s/n)', 's');
        $cache_enabled = strtolower($enable_cache[0]) === 's';
        update_option('meu_plugin_cache_enabled', $cache_enabled ? '1' : '0');
        
        // Duração do cache
        if ($cache_enabled) {
            $cache_duration = WP_CLI\Utils\prompt('Duração do cache (minutos)', '60');
            update_option('meu_plugin_cache_duration', absint($cache_duration));
        }
        
        // Email
        $email = WP_CLI\Utils\prompt(
            'Email para notificações',
            get_option('admin_email')
        );
        update_option('meu_plugin_notification_email', sanitize_email($email));
        
        // Resumo
        WP_CLI::log('');
        WP_CLI::log('═══════════════════════════════════════════');
        WP_CLI::log('Resumo da Configuração:');
        WP_CLI::log('═══════════════════════════════════════════');
        WP_CLI::log('API Key: ' . str_repeat('*', strlen($api_key)));
        WP_CLI::log('Modo: ' . $modes[$mode_idx]);
        WP_CLI::log('Cache: ' . ($cache_enabled ? 'Habilitado' : 'Desabilitado'));
        if ($cache_enabled) {
            WP_CLI::log('Duração: ' . get_option('meu_plugin_cache_duration') . ' minutos');
        }
        WP_CLI::log('Email: ' . get_option('meu_plugin_notification_email'));
        WP_CLI::log('═══════════════════════════════════════════');
        WP_CLI::log('');
        
        WP_CLI::success('✓ Configuração concluída com sucesso!');
    }
    
    /**
     * Wizard de importação de dados
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin import
     *
     * @when after_wp_load
     */
    public function import($args, $assoc_args) {
        WP_CLI::log('');
        WP_CLI::log('╔═══════════════════════════════════════════╗');
        WP_CLI::log('║   Wizard de Importação de Dados          ║');
        WP_CLI::log('╚═══════════════════════════════════════════╝');
        WP_CLI::log('');
        
        // Tipo de arquivo
        WP_CLI::log('Selecione o tipo de arquivo:');
        WP_CLI::log('  1) CSV');
        WP_CLI::log('  2) JSON');
        WP_CLI::log('  3) XML');
        $type_choice = WP_CLI\Utils\prompt('Escolha (1-3)', '1');
        
        $types = ['csv', 'json', 'xml'];
        $file_type = $types[absint($type_choice) - 1] ?? 'csv';
        
        // Caminho do arquivo
        $file_path = WP_CLI\Utils\prompt('Caminho do arquivo');
        
        if (!file_exists($file_path)) {
            WP_CLI::error('Arquivo não encontrado!');
        }
        
        // Validar extensão
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        if ($ext !== $file_type) {
            WP_CLI::warning("Aviso: Extensão do arquivo ({$ext}) não corresponde ao tipo ({$file_type})");
        }
        
        // Confirmar
        WP_CLI::log('');
        WP_CLI::log('Resumo:');
        WP_CLI::log("  Tipo: " . strtoupper($file_type));
        WP_CLI::log("  Arquivo: {$file_path}");
        WP_CLI::log("  Tamanho: " . size_format(filesize($file_path)));
        WP_CLI::log('');
        
        $confirm = WP_CLI\Utils\prompt('Continuar? (s/n)', 's');
        
        if (strtolower($confirm[0]) !== 's') {
            WP_CLI::log('Importação cancelada');
            return;
        }
        
        // Processar arquivo
        $this->process_import($file_path, $file_type);
    }
    
    /**
     * Processar importação
     */
    private function process_import($file_path, $file_type) {
        switch ($file_type) {
            case 'csv':
                $this->import_csv($file_path);
                break;
            case 'json':
                $this->import_json($file_path);
                break;
            case 'xml':
                $this->import_xml($file_path);
                break;
        }
    }
    
    /**
     * Importar CSV
     */
    private function import_csv($file_path) {
        $file = fopen($file_path, 'r');
        
        if (!$file) {
            WP_CLI::error('Erro ao abrir arquivo');
        }
        
        $headers = fgetcsv($file);
        $count = 0;
        
        $progress = WP_CLI\Utils\make_progress_bar('Importando', $this->count_csv_lines($file_path) - 1);
        
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);
            
            // Inserir dados
            $this->insert_data($data);
            
            $count++;
            $progress->tick();
        }
        
        $progress->finish();
        fclose($file);
        
        WP_CLI::success("✓ {$count} registros importados com sucesso!");
    }
    
    /**
     * Importar JSON
     */
    private function import_json($file_path) {
        $content = file_get_contents($file_path);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            WP_CLI::error('JSON inválido: ' . json_last_error_msg());
        }
        
        $count = 0;
        $progress = WP_CLI\Utils\make_progress_bar('Importando', count($data));
        
        foreach ($data as $record) {
            $this->insert_data($record);
            $count++;
            $progress->tick();
        }
        
        $progress->finish();
        WP_CLI::success("✓ {$count} registros importados com sucesso!");
    }
    
    /**
     * Importar XML
     */
    private function import_xml($file_path) {
        $xml = simplexml_load_file($file_path);
        
        if (!$xml) {
            WP_CLI::error('XML inválido');
        }
        
        $count = 0;
        $progress = WP_CLI\Utils\make_progress_bar('Importando', count($xml->record));
        
        foreach ($xml->record as $record) {
            $data = json_decode(json_encode($record), true);
            $this->insert_data($data);
            $count++;
            $progress->tick();
        }
        
        $progress->finish();
        WP_CLI::success("✓ {$count} registros importados com sucesso!");
    }
    
    /**
     * Inserir dados no banco
     */
    private function insert_data($data) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'meu_plugin_data',
            [
                'uuid' => wp_generate_uuid4(),
                'data' => json_encode($data),
            ],
            ['%s', '%s']
        );
    }
    
    /**
     * Contar linhas do CSV
     */
    private function count_csv_lines($file_path) {
        $count = 0;
        $file = fopen($file_path, 'r');
        while (fgets($file)) {
            $count++;
        }
        fclose($file);
        return $count;
    }
}

WP_CLI::add_command('meu-plugin setup', ['Meu_Plugin_Interactive_CLI_Command', 'setup']);
WP_CLI::add_command('meu-plugin import', ['Meu_Plugin_Interactive_CLI_Command', 'import']);
```

---

## Comandos com Testes

```php
<?php
/**
 * Comandos de teste com WP-CLI
 */

class Meu_Plugin_Test_CLI_Command {
    
    /**
     * Executar testes
     *
     * ## OPTIONS
     *
     * [--group=<group>]
     * : Grupo de testes específico
     *
     * [--filter=<filter>]
     * : Filtrar testes por padrão
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin test
     *     wp meu-plugin test --group=api
     *     wp meu-plugin test --filter=payment
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $group = isset($assoc_args['group']) ? $assoc_args['group'] : null;
        $filter = isset($assoc_args['filter']) ? $assoc_args['filter'] : null;
        
        require_once dirname(__FILE__) . '/../tests/bootstrap.php';
        
        $tests = $this->find_tests($group, $filter);
        
        if (empty($tests)) {
            WP_CLI::warning('Nenhum teste encontrado');
            return;
        }
        
        $passed = 0;
        $failed = 0;
        $errors = [];
        
        $progress = WP_CLI\Utils\make_progress_bar('Executando testes', count($tests));
        
        foreach ($tests as $test) {
            try {
                $result = $test['callback']();
                
                if ($result === true || $result === null) {
                    $passed++;
                } else {
                    $failed++;
                    $errors[] = $test['name'] . ': ' . $result;
                }
            } catch (Exception $e) {
                $failed++;
                $errors[] = $test['name'] . ': ' . $e->getMessage();
            }
            
            $progress->tick();
        }
        
        $progress->finish();
        
        // Resumo
        WP_CLI::log('');
        WP_CLI::log('═══════════════════════════════════════════');
        WP_CLI::log("Testes Concluídos: {$passed} ✓ / {$failed} ✗");
        WP_CLI::log('═══════════════════════════════════════════');
        
        if (!empty($errors)) {
            WP_CLI::log('');
            WP_CLI::log('Erros:');
            foreach ($errors as $error) {
                WP_CLI::error('  • ' . $error, false);
            }
        }
        
        if ($failed === 0) {
            WP_CLI::success('✓ Todos os testes passaram!');
        } else {
            WP_CLI::error('✗ Alguns testes falharam');
        }
    }
    
    /**
     * Encontrar testes
     */
    private function find_tests($group = null, $filter = null) {
        $test_dir = dirname(__FILE__) . '/../tests';
        
        if (!is_dir($test_dir)) {
            return [];
        }
        
        $tests = [];
        $files = glob($test_dir . '/test-*.php');
        
        foreach ($files as $file) {
            require_once $file;
            
            $class_name = str_replace(
                ['test-', '.php'],
                ['Test_', ''],
                basename($file)
            );
            $class_name = 'Meu_Plugin_' . str_replace('-', '_', $class_name);
            
            if (!class_exists($class_name)) {
                continue;
            }
            
            $reflection = new ReflectionClass($class_name);
            
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (strpos($method->getName(), 'test_') !== 0) {
                    continue;
                }
                
                $test_name = str_replace('test_', '', $method->getName());
                
                // Filtrar por grupo
                if ($group && strpos($test_name, $group) === false) {
                    continue;
                }
                
                // Filtrar por padrão
                if ($filter && strpos($test_name, $filter) === false) {
                    continue;
                }
                
                $tests[] = [
                    'name' => $class_name . '::' . $method->getName(),
                    'callback' => function() use ($class_name, $method) {
                        $instance = new $class_name();
                        return $instance->{$method->getName()}();
                    }
                ];
            }
        }
        
        return $tests;
    }
}

WP_CLI::add_command('meu-plugin test', 'Meu_Plugin_Test_CLI_Command');
```

---

## Scaffolding com WP-CLI

```php
<?php
/**
 * Comandos de scaffolding (geração de código)
 */

class Meu_Plugin_Scaffold_CLI_Command {
    
    /**
     * Gerar novo módulo
     *
     * ## OPTIONS
     *
     * <name>
     * : Nome do módulo
     *
     * [--type=<type>]
     * : Tipo do módulo (class, service, controller)
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
            default:
                $this->generate_class($class_name);
        }
        
        WP_CLI::success("Módulo {$class_name} criado com sucesso!");
    }
    
    /**
     * Gerar classe
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
        $this->init();
    }
    
    /**
     * Inicializar
     */
    public function init() {
        // Implementar
    }
    
    /**
     * Método exemplo
     */
    public function example() {
        // Implementar
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        $file_path = $this->get_file_path($name, 'includes');
        
        file_put_contents($file_path, $template);
    }
    
    /**
     * Gerar service
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
     * Singleton instance
     */
    private static $instance;
    
    /**
     * Get instance
     */
    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor (private para singleton)
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Inicializar
     */
    private function init() {
        // Implementar
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        $file_path = $this->get_file_path($name, 'includes/services');
        
        file_put_contents($file_path, $template);
    }
    
    /**
     * Gerar controller
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
    public function handle($request) {
        // Implementar
        return [
            'status' => 'success',
            'data' => [],
        ];
    }
}

PHP;
        
        $template = str_replace('{CLASS_NAME}', $name, $template);
        $file_path = $this->get_file_path($name, 'includes/controllers');
        
        file_put_contents($file_path, $template);
    }
    
    /**
     * Obter caminho do arquivo
     */
    private function get_file_path($name, $dir) {
        $base = dirname(dirname(__FILE__));
        $dir_path = "{$base}/{$dir}";
        
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        
        return "{$dir_path}/class-{$this->name_to_filename($name)}.php";
    }
    
    /**
     * Converter nome para nome de arquivo
     */
    private function name_to_filename($name) {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
    }
    
    /**
     * Sanitizar nome da classe
     */
    private function sanitize_class_name($name) {
        return str_replace(['-', '_'], '', ucwords($name, '-_'));
    }
}

WP_CLI::add_command('meu-plugin scaffold module', ['Meu_Plugin_Scaffold_CLI_Command', 'module']);
```

---

## Migrations e Database

```php
<?php
/**
 * Sistema de migrations para WP-CLI
 */

class Meu_Plugin_Migration_CLI_Command {
    
    /**
     * Executar migrações pendentes
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin migrate
     *     wp meu-plugin migrate --step=5
     *
     * ## OPTIONS
     *
     * [--step=<step>]
     * : Número de migrações a executar
     *
     * @when after_wp_load
     */
    public function migrate($args, $assoc_args) {
        $step = isset($assoc_args['step']) ? absint($assoc_args['step']) : 999;
        
        $migrations = $this->get_pending_migrations();
        
        if (empty($migrations)) {
            WP_CLI::log('Nenhuma migração pendente');
            return;
        }
        
        $migrations = array_slice($migrations, 0, $step);
        
        $progress = WP_CLI\Utils\make_progress_bar('Migrando', count($migrations));
        
        foreach ($migrations as $migration) {
            $this->run_migration($migration);
            $progress->tick();
        }
        
        $progress->finish();
        WP_CLI::success('✓ Migrações executadas com sucesso!');
    }
    
    /**
     * Fazer rollback de migrações
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin rollback
     *
     * @when after_wp_load
     */
    public function rollback($args, $assoc_args) {
        $last_migration = $this->get_last_executed_migration();
        
        if (!$last_migration) {
            WP_CLI::log('Nenhuma migração para fazer rollback');
            return;
        }
        
        $confirm = WP_CLI\Utils\prompt('Fazer rollback de ' . $last_migration . '? (s/n)', 'n');
        
        if (strtolower($confirm[0]) !== 's') {
            return;
        }
        
        $this->run_rollback($last_migration);
        WP_CLI::success('✓ Rollback executado com sucesso!');
    }
    
    /**
     * Listar migrações
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin migrations list
     *
     * @when after_wp_load
     */
    public function list_migrations($args, $assoc_args) {
        $all_migrations = $this->get_all_migrations();
        $executed = $this->get_executed_migrations();
        
        $items = [];
        
        foreach ($all_migrations as $migration) {
            $status = in_array($migration, $executed) ? '✓' : '✗';
            $items[] = [
                'status' => $status,
                'migration' => $migration,
            ];
        }
        
        WP_CLI\Utils\format_items('table', $items, ['status', 'migration']);
    }
    
    /**
     * Obter migrações pendentes
     */
    private function get_pending_migrations() {
        $all = $this->get_all_migrations();
        $executed = $this->get_executed_migrations();
        
        return array_diff($all, $executed);
    }
    
    /**
     * Obter todas as migrações
     */
    private function get_all_migrations() {
        $migration_dir = dirname(__FILE__) . '/../migrations';
        
        if (!is_dir($migration_dir)) {
            return [];
        }
        
        $files = glob($migration_dir . '/*.php');
        $migrations = [];
        
        foreach ($files as $file) {
            $migrations[] = basename($file, '.php');
        }
        
        sort($migrations);
        return $migrations;
    }
    
    /**
     * Obter migrações executadas
     */
    private function get_executed_migrations() {
        $option = get_option('meu_plugin_migrations', []);
        return is_array($option) ? $option : [];
    }
    
    /**
     * Executar migração
     */
    private function run_migration($migration) {
        $file = dirname(__FILE__) . "/../migrations/{$migration}.php";
        
        if (!file_exists($file)) {
            throw new Exception("Arquivo de migração não encontrado: {$migration}");
        }
        
        require_once $file;
        
        $class = 'Meu_Plugin_Migration_' . str_replace(['-', '/'], '_', $migration);
        
        if (!class_exists($class)) {
            throw new Exception("Classe de migração não encontrada: {$class}");
        }
        
        $instance = new $class();
        $instance->up();
        
        // Registrar execução
        $executed = $this->get_executed_migrations();
        $executed[] = $migration;
        update_option('meu_plugin_migrations', $executed);
    }
    
    /**
     * Fazer rollback
     */
    private function run_rollback($migration) {
        $file = dirname(__FILE__) . "/../migrations/{$migration}.php";
        require_once $file;
        
        $class = 'Meu_Plugin_Migration_' . str_replace(['-', '/'], '_', $migration);
        $instance = new $class();
        $instance->down();
        
        // Remover de registrados
        $executed = $this->get_executed_migrations();
        $key = array_search($migration, $executed);
        if ($key !== false) {
            unset($executed[$key]);
            update_option('meu_plugin_migrations', array_values($executed));
        }
    }
    
    /**
     * Obter última migração executada
     */
    private function get_last_executed_migration() {
        $executed = $this->get_executed_migrations();
        return end($executed);
    }
}

WP_CLI::add_command('meu-plugin migrate', ['Meu_Plugin_Migration_CLI_Command', 'migrate']);
WP_CLI::add_command('meu-plugin rollback', ['Meu_Plugin_Migration_CLI_Command', 'rollback']);
WP_CLI::add_command('meu-plugin migrations', ['Meu_Plugin_Migration_CLI_Command', 'list_migrations']);
```

---

## Debugging Tools

```php
<?php
/**
 * Ferramentas de debug integradas no WP-CLI
 */

class Meu_Plugin_Debug_CLI_Command {
    
    /**
     * Gerar relatório de debug
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin debug report
     *     wp meu-plugin debug report --output=debug.log
     *
     * ## OPTIONS
     *
     * [--output=<file>]
     * : Arquivo para salvar relatório
     *
     * @when after_wp_load
     */
    public function report($args, $assoc_args) {
        $output = isset($assoc_args['output']) ? $assoc_args['output'] : null;
        
        $report = $this->generate_debug_report();
        
        if ($output) {
            file_put_contents($output, $report);
            WP_CLI::success("Relatório salvo em {$output}");
        } else {
            WP_CLI::log($report);
        }
    }
    
    /**
     * Verificar performance
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin debug performance
     *
     * @when after_wp_load
     */
    public function performance($args, $assoc_args) {
        WP_CLI::log('Analisando performance...');
        
        $start = microtime(true);
        
        // Simular operações
        $this->test_database_performance();
        $this->test_cache_performance();
        $this->test_hooks_performance();
        
        $elapsed = microtime(true) - $start;
        
        WP_CLI::log('');
        WP_CLI::log('Tempo total: ' . number_format($elapsed, 4) . 's');
    }
    
    /**
     * Limpar dados de debug
     *
     * ## EXAMPLES
     *
     *     wp meu-plugin debug clear
     *
     * @when after_wp_load
     */
    public function clear($args, $assoc_args) {
        global $wpdb;
        
        $wpdb->query("DELETE FROM {$wpdb->prefix}meu_plugin_debug_logs");
        WP_CLI::success('Logs de debug limpos!');
    }
    
    /**
     * Gerar relatório
     */
    private function generate_debug_report() {
        $report = "═══════════════════════════════════════════\n";
        $report .= "Relatório de Debug - Meu Plugin\n";
        $report .= "═══════════════════════════════════════════\n\n";
        
        $report .= "WordPress:\n";
        $report .= "  Versão: " . get_bloginfo('version') . "\n";
        $report .= "  URL: " . home_url() . "\n";
        $report .= "  Admin Email: " . get_bloginfo('admin_email') . "\n\n";
        
        $report .= "PHP:\n";
        $report .= "  Versão: " . PHP_VERSION . "\n";
        $report .= "  Memory Limit: " . ini_get('memory_limit') . "\n";
        $report .= "  Max Execution Time: " . ini_get('max_execution_time') . "s\n";
        $report .= "  Extensions: " . implode(', ', array_slice(get_loaded_extensions(), 0, 5)) . "...\n\n";
        
        $report .= "Plugins Ativos:\n";
        foreach (get_option('active_plugins') as $plugin) {
            $report .= "  • " . $plugin . "\n";
        }
        $report .= "\n";
        
        $report .= "Tema Ativo: " . get_option('template') . "\n\n";
        
        $report .= "Opções do Plugin:\n";
        $report .= "  Version: " . get_option('meu_plugin_version') . "\n";
        $report .= "  Mode: " . get_option('meu_plugin_mode') . "\n";
        $report .= "  Cache Enabled: " . (get_option('meu_plugin_cache_enabled') ? 'Sim' : 'Não') . "\n";
        $report .= "\n";
        
        return $report;
    }
    
    /**
     * Testar performance do banco
     */
    private function test_database_performance() {
        global $wpdb;
        
        $start = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $wpdb->get_results("SELECT * FROM {$wpdb->posts} LIMIT 1");
        }
        
        $elapsed = microtime(true) - $start;
        WP_CLI::log(sprintf("Database: %.4f segundos para 100 queries", $elapsed));
    }
    
    /**
     * Testar performance de cache
     */
    private function test_cache_performance() {
        $start = microtime(true);
        
        for ($i = 0; $i < 1000; $i++) {
            wp_cache_set("test_{$i}", "value_{$i}", 'debug', 3600);
            wp_cache_get("test_{$i}", 'debug');
        }
        
        $elapsed = microtime(true) - $start;
        WP_CLI::log(sprintf("Cache: %.4f segundos para 1000 operações", $elapsed));
    }
    
    /**
     * Testar performance de hooks
     */
    private function test_hooks_performance() {
        $start = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            do_action('meu_plugin_test_hook', $i);
        }
        
        $elapsed = microtime(true) - $start;
        WP_CLI::log(sprintf("Hooks: %.4f segundos para 100 execuções", $elapsed));
    }
}

WP_CLI::add_command('meu-plugin debug', 'Meu_Plugin_Debug_CLI_Command');
```

---

## Scripts de Automação

```bash
#!/bin/bash
# deploy.sh - Script de deploy automatizado

set -e  # Exit on error

echo "╔════════════════════════════════════╗"
echo "║   Deploy Automatizado do Plugin   ║"
echo "╚════════════════════════════════════╝"
echo ""

# Variáveis
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$PLUGIN_DIR/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# 1. Criar backup
echo "📦 Criando backup..."
mkdir -p "$BACKUP_DIR"
wp db export "$BACKUP_DIR/backup_$TIMESTAMP.sql"
cp -r "$PLUGIN_DIR" "$BACKUP_DIR/plugin_$TIMESTAMP"
echo "✓ Backup criado em $BACKUP_DIR/backup_$TIMESTAMP.sql"

# 2. Executar testes
echo ""
echo "🧪 Executando testes..."
wp meu-plugin test || {
    echo "✗ Testes falharam!"
    exit 1
}
echo "✓ Testes passaram"

# 3. Executar migrações
echo ""
echo "🔄 Executando migrações..."
wp meu-plugin migrate
echo "✓ Migrações executadas"

# 4. Limpar cache
echo ""
echo "🧹 Limpando cache..."
wp cache flush
wp transient delete --all
echo "✓ Cache limpo"

# 5. Verificar integridade
echo ""
echo "✅ Verificando integridade..."
wp meu-plugin check-integrity

echo ""
echo "╔════════════════════════════════════╗"
echo "║   Deploy Concluído com Sucesso!   ║"
echo "╚════════════════════════════════════╝"
```

---

## CI/CD com GitHub Actions

```yaml
# .github/workflows/deploy.yml

name: Deploy Plugin

on:
  push:
    branches: [ main, staging ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        extensions: mysql, mbstring
    
    - name: Install WP-CLI
      run: |
        curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
        chmod +x wp-cli.phar
        sudo mv wp-cli.phar /usr/local/bin/wp
    
    - name: Install WordPress
      run: |
        wp core download --path=/tmp/wordpress
        cd /tmp/wordpress
        wp config create --dbname=test --dbuser=root --dbpass=root --dbhost=127.0.0.1
        wp core install --url=http://localhost --title=Test --admin_user=admin --admin_email=admin@test.com
    
    - name: Install Plugin
      run: |
        cp -r . /tmp/wordpress/wp-content/plugins/meu-plugin
        cd /tmp/wordpress
        wp plugin activate meu-plugin
    
    - name: Run Tests
      run: |
        cd /tmp/wordpress
        wp meu-plugin test
    
    - name: Check Integrity
      run: |
        cd /tmp/wordpress
        wp meu-plugin check-integrity

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to Server
      run: |
        mkdir -p ~/.ssh
        echo "${{ secrets.DEPLOY_KEY }}" > ~/.ssh/id_rsa
        chmod 600 ~/.ssh/id_rsa
        ssh-keyscan -H ${{ secrets.SERVER_HOST }} >> ~/.ssh/known_hosts
        rsync -avz --delete ./ ${{ secrets.SERVER_USER }}@${{ secrets.SERVER_HOST }}:${{ secrets.PLUGIN_PATH }}/
```

---

## Boas Práticas

### 1. Documentação de Comandos

Sempre documente seus comandos WP-CLI:

```php
/**
 * Descrição do comando
 *
 * ## DESCRIPTION
 *
 * Descrição detalhada do que o comando faz
 *
 * ## EXAMPLES
 *
 *     wp meu-plugin comando arg
 *     wp meu-plugin comando arg --option=value
 *
 * ## OPTIONS
 *
 * <arg>
 * : Descrição do argumento
 *
 * [--option=<value>]
 * : Descrição da opção
 *
 * [--flag]
 * : Descrição da flag booleana
 *
 * @when after_wp_load
 */
public function comando($args, $assoc_args) {
    // Implementar
}
```

### 2. Tratamento de Erros

```php
// Validar argumentos
if (empty($args) || empty($args[0])) {
    WP_CLI::error('Argumento obrigatório ausente');
}

// Tratamento de exceções
try {
    $this->process_data();
} catch (Exception $e) {
    WP_CLI::error('Erro ao processar: ' . $e->getMessage());
}

// Avisos e logs
WP_CLI::warning('Aviso: algo pode estar errado');
WP_CLI::log('Informação: operação em progresso');
WP_CLI::success('Sucesso: operação completada');
```

### 3. Progresso e Output

```php
// Barra de progresso
$progress = WP_CLI\Utils\make_progress_bar('Processando', count($items));
foreach ($items as $item) {
    // Processar
    $progress->tick();
}
$progress->finish();

// Tabelas
WP_CLI\Utils\format_items('table', $data, ['col1', 'col2']);

// JSON
WP_CLI\Utils\format_items('json', $data);
```

### 4. Performance

```php
// Desabilitar garbage collection desnecessário
wp_suspend_cache_invalidation(true);

// Batch processing
$batch_size = 100;
foreach (array_chunk($large_array, $batch_size) as $batch) {
    $this->process_batch($batch);
}

// Cleanup
wp_cache_flush();
wp_suspend_cache_invalidation(false);
```

### 5. Segurança

```php
// Validar e sanitizar entrada
$value = isset($assoc_args['option']) ? sanitize_text_field($assoc_args['option']) : '';

// Verificar nonce se necessário
check_admin_referer('action_nonce');

// Preparar queries
$wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE post_title = %s", $title);
```

---

## Resumo da Fase 9

Ao dominar a **Fase 9**, você entenderá:

✅ **WP-CLI Fundamentos** - Instalação, configuração, comandos básicos  
✅ **Comandos Customizados** - Criar sua própria CLI  
✅ **Subcomandos** - Organizar hierarquicamente  
✅ **Interatividade** - Prompts e menus interativos  
✅ **Testes** - Executar testes via WP-CLI  
✅ **Scaffolding** - Gerar código automaticamente  
✅ **Migrations** - Sistema de versionamento de banco de dados  
✅ **Debugging** - Ferramentas de debug integradas  
✅ **Automação** - Scripts de deploy e CI/CD  
✅ **GitHub Actions** - Pipeline automatizado  

---

**Versão:** 1.0  
**Última atualização:** Janeiro 2026  
**Autor:** André | Especialista em PHP e WordPress  
**Comunidade:** Compartilhe esse conhecimento! 🚀
