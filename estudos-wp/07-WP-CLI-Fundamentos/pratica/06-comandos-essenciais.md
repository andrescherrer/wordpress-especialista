# Comandos WP-CLI essenciais

Referência rápida dos comandos nativos. Execute na **raiz do WordPress** (onde está o `wp-config.php`).

---

## Instalação e info

```bash
wp --version
wp --info
```

---

## Core

```bash
wp core download
wp core download --version=6.0
wp core install --url="http://exemplo.com" --title="Meu Site" --admin_user="admin" --admin_password="senha" --admin_email="admin@exemplo.com"
wp core update
wp core version
wp config create --dbname=wordpress --dbuser=root --dbpass=
```

---

## Plugins

```bash
wp plugin list
wp plugin install hello-dolly
wp plugin install akismet --activate
wp plugin activate hello-dolly
wp plugin deactivate hello-dolly
wp plugin update akismet
wp plugin update --all
wp plugin delete hello-dolly
wp plugin search woocommerce
```

---

## Temas

```bash
wp theme list
wp theme install twentytwentytwo
wp theme activate twentytwentytwo
wp theme update --all
wp theme delete twentytwentythree
```

---

## Posts

```bash
wp post list
wp post list --post_type=post --posts_per_page=5
wp post create --post_title="Meu Post" --post_content="Conteúdo" --post_status=publish
wp post update 123 --post_title="Novo Título"
wp post delete 123
wp post delete 123 --force
wp post get 123
```

---

## Usuários

```bash
wp user list
wp user create joao joao@exemplo.com --role=subscriber --user_pass=senha123
wp user update 2 --user_email=novo@exemplo.com
wp user delete 2
wp user delete 2 --reassign=1
wp user get 2
wp role list
```

---

## Database

```bash
wp db export
wp db export backup.sql
wp db import backup.sql
wp db query "SELECT * FROM wp_posts LIMIT 5"
wp db optimize
wp db repair
wp db reset --yes
wp db check
wp db tables
```

---

## Cache e transients

```bash
wp cache flush
wp transient delete --all
wp transient delete meu_transient
wp transient get meu_transient
wp transient set meu_transient "valor" 3600
```

---

## Search-replace

```bash
wp search-replace 'http://old.com' 'https://new.com'
wp search-replace 'old' 'new' --all-tables
wp search-replace 'old' 'new' --dry-run
```

---

**Dica:** use `wp <comando> --help` para ver opções de qualquer comando.
