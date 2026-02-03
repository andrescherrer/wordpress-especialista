# Padrões de código (PHPCS, WordPress-Core)

Configurar **PHP_CodeSniffer** com ruleset **WordPress-Core** (ou WordPress); rodar no plugin; corrigir warnings; integração em **CI**.

---

## Instalação

```bash
composer require --dev squizlabs/php_codesniffer
composer require --dev phpcsstandards/phpcsutils
composer require --dev wp-coding-standards/wpcs
```

Configurar o **installed_paths** para incluir WordPress:

```bash
./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
```

---

## Ruleset WordPress

Criar **phpcs.xml** (ou phpcs.xml.dist) na raiz do plugin:

```xml
<?xml version="1.0"?>
<ruleset name="Meu Plugin">
  <description>WordPress Coding Standards</description>
  <rule ref="WordPress-Core">
    <exclude name="WordPress.Files.FileName"/>
  </rule>
  <file>.</file>
  <arg name="extensions" value="php"/>
  <arg value="sp"/>
</ruleset>
```

- **WordPress-Core:** regras do core; pode usar **WordPress** (mais permissivo) ou **WordPress-Extra**.
- **exclude:** desativar regras específicas se necessário (ex.: nome de arquivo).

---

## Rodar no plugin

```bash
./vendor/bin/phpcs
```

Ou em um diretório específico:

```bash
./vendor/bin/phpcs wp-content/plugins/meu-plugin
```

Corrigir os **warnings** e **errors** reportados (indentação, Yoda conditions, escape, etc.).

---

## Integração em CI

- No **GitHub Actions** (ou outro CI): instalar dependências, rodar `phpcs` e falhar o job se houver erros.
- Exemplo (trecho): `run: ./vendor/bin/phpcs --report=checkstyle` e publicar o resultado ou falhar com código de saída não zero.

---

## Recursos

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- [WPCS – PHP_CodeSniffer](https://github.com/WordPress/WordPress-Coding-Standards)
- Ver **25-code-review-checklist.md** para o que os revisores olham.
