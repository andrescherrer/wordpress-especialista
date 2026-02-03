# Publicar plugin no .org

Requisitos (slug, readme.txt, licença GPL, sem código ofensivo); **SVN** do repositório; primeiro commit; tags e atualizações; guidelines de revisão.

---

## Requisitos

| Item | Descrição |
|------|------------|
| **Slug** | Nome único do plugin (sem espaços; usado na URL). |
| **readme.txt** | Formato do WordPress (=== Plugin Name ===, Description, etc.); [readme standard](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-readme/). |
| **Licença** | Código sob **GPL v2 ou posterior** (ou compatível); cabeçalho nos arquivos. |
| **Código** | Sem código ofensivo (backdoors, mineração, spam); sem violação de marcas/patentes. |
| **Funcionalidade** | Plugin deve fazer o que descreve; sem links obrigatórios para sites externos sem justificativa. |

---

## SVN do repositório

- Após aprovação do plugin no WordPress.org, você recebe acesso ao **SVN** do plugin.
- URL típica: `https://plugins.svn.wordpress.org/seu-plugin-slug/`.
- Estrutura: **trunk** (desenvolvimento), **tags/** (releases: 1.0, 1.1, etc.), **assets/** (banners, ícones).

---

## Primeiro commit

- **Checkout** do SVN: `svn co https://plugins.svn.wordpress.org/seu-plugin-slug/ plugin-svn`
- Colocar os arquivos do plugin em **trunk/** (não incluir node_modules, .git, arquivos sensíveis).
- **Adicionar** e **commit**: `svn add trunk/*`, `svn ci -m "Initial commit"`.
- Criar **tag** da primeira versão: `svn cp trunk tags/1.0`, `svn ci -m "Tag 1.0"`.

---

## Atualizações

- Desenvolver em trunk; quando for lançar nova versão, atualizar **Stable tag** e **Version** no readme.txt.
- Copiar trunk para nova tag: `svn cp trunk tags/1.1`, commit.
- O WordPress.org detecta a nova tag e oferece a atualização aos usuários.

---

## Guidelines de revisão

- Segurança (escape, nonce, capability), performance, i18n, a11y, documentação.
- Ver **25-code-review-checklist.md** e [Plugin Handbook – Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/).

---

## Recursos

- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress.org – How to use readme](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-readme/)
- [Make WordPress – Plugins](https://make.wordpress.org/plugins/)
