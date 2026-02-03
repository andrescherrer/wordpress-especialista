# Contribuir com o core (passo a passo)

Criar conta em **WordPress.org**; acessar **Trac**; encontrar ticket; criar branch SVN; fazer alteração; gerar **patch**; anexar no ticket; referência ao Core Contributor Handbook.

---

## 1. Conta WordPress.org

- Criar conta em [wordpress.org](https://wordpress.org/support/register.php).
- Usar o mesmo login para **Trac** (bugs e patches do core).

---

## 2. Trac

- Acessar [core.trac.wordpress.org](https://core.trac.wordpress.org/).
- **Query** ou **Report** para encontrar tickets (bugs, melhorias, “good first bug”).
- Escolher um ticket; comentar que vai trabalhar nele (evitar duplicar esforço).

---

## 3. Ambiente SVN

- Instalar **Subversion** (SVN).
- Checkout do repositório do core:  
  `svn co https://develop.svn.wordpress.org/trunk/ wp-core`
- Criar **branch** (cópia) para o seu ticket, se o guia do core indicar; ou trabalhar em uma cópia do trunk e gerar diff.

---

## 4. Fazer a alteração

- Editar os arquivos necessários (PHP, JS, etc.) seguindo os [padrões de código do WordPress](https://developer.wordpress.org/coding-standards/).
- Testar localmente (PHPUnit quando aplicável).

---

## 5. Gerar o patch

- **Diff** contra o trunk (ou o branch do ticket):  
  `svn diff > 12345.patch` (12345 = número do ticket).
- Ou usar **git-svn** e `git diff`/`git format-patch` conforme o fluxo recomendado pelo core.

---

## 6. Anexar no ticket

- No Trac, abrir o ticket e anexar o arquivo **.patch**.
- Comentar explicando o que foi alterado e como testar.
- Aguardar feedback dos revisores.

---

## Recursos

- [Core Contributor Handbook](https://make.wordpress.org/core/handbook/)
- [Contributing to WordPress Core](https://make.wordpress.org/core/handbook/contribute/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)

Ver **23-publicar-plugin-org.md**, **24-phpcs-wordpress-core.md**, **25-code-review-checklist.md**, **26-comunidade-slack-make.md**.
