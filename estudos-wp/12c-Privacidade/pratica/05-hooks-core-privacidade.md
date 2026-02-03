# Hooks do Core (privacidade)

**wp_privacy_personal_data_exporters** e **wp_privacy_personal_data_erasers**; como registrar **exportador** e **apagador** no plugin.

---

## Exportadores – wp_privacy_personal_data_exporters

Registrar um **array** de exportadores; cada um tem:

- **exporter_friendly_name:** nome exibido na tela “Exportar dados pessoais”.
- **callback:** função que recebe `$email_address` e `$page`; retorna `['data' => [...], 'done' => true/false]`.

Cada item em `data` deve ter:

- **group_id**, **group_label:** agrupamento na exportação.
- **item_id:** identificador do item.
- **data:** array de `['name' => '...', 'value' => '...']`.

O core chama o callback por página (paginação); quando `done` for true, não chama mais.

Exemplo: ver **02-exportacao-dados-usuario.php**.

---

## Apagadores – wp_privacy_personal_data_erasers

Registrar um **array** de apagadores; cada um tem:

- **eraser_friendly_name:** nome exibido na tela “Apagar dados pessoais”.
- **callback:** função que recebe `$email_address` e `$page`; retorna:

  - **items_removed:** quantidade de itens apagados.
  - **items_retained:** itens que não foram apagados (ex.: por obrigação legal).
  - **messages:** mensagens para o usuário (ex.: “Dados X foram removidos.”).
  - **done:** true quando não há mais nada a apagar.

O core chama o callback por página; o plugin deve apagar ou anonimizar os dados do usuário (user_meta, post_meta, etc.).

Exemplo: ver **03-exclusao-dados-usuario.php**.

---

## Ferramentas do WordPress

- **Ferramentas > Exportar dados pessoais:** o administrador informa o email; o core chama todos os exportadores registrados e gera um arquivo para download.
- **Ferramentas > Apagar dados pessoais:** o administrador informa o email; o core chama todos os apagadores registrados.

Ver [WordPress Privacy Handbook](https://developer.wordpress.org/plugins/privacy/).
