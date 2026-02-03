# Política de privacidade e consentimento

Como **declarar** coleta de dados; **checkbox de consentimento** em formulários; **registro de consentimento** (data/finalidade); modelo de texto para política.

---

## Declarar coleta de dados

- O plugin deve descrever na documentação (e no readme) **quais dados** coleta, **para quê** e **por quanto tempo**.
- Oferecer um **trecho de texto** para o dono do site colar na **página de Política de Privacidade** (WordPress: Configurações > Privacidade).

Exemplo de trecho:

> **Plugin Estudos WP:** Este site utiliza o plugin Estudos WP, que pode armazenar preferências do usuário (ex.: último acesso) em dados técnicos. Esses dados são exportáveis e removíveis mediante solicitação (Ferramentas > Exportar/Apagar dados pessoais).

---

## Checkbox de consentimento

- Em **formulários** que coletam dados pessoais (newsletter, cadastro), incluir **checkbox** explícito: “Li e aceito a política de privacidade” (link para a página).
- Não pré-marcar o checkbox; o envio só deve ser aceito se o usuário marcar.
- **Registrar** o consentimento quando exigido por lei: user_meta com data, finalidade e versão da política (ex.: `consentimento_newsletter` = `2024-01-15;newsletter;v1`).

---

## Modelo de texto para política

- Quem coleta (site/plugin).
- Quais dados (nome, email, IP, etc.).
- Finalidade (envio de newsletter, melhoria do serviço, etc.).
- Base legal (consentimento, legítimo interesse).
- Prazo de retenção.
- Direitos (acesso, retificação, exclusão, portabilidade) e como exercer (email, Ferramentas do WP).
- Compartilhamento com terceiros (se houver).
- Contato do encarregado (se aplicável).

Ver **05-hooks-core-privacidade.md** e **06-checklist-privacidade.md**; [WordPress Privacy Handbook](https://developer.wordpress.org/plugins/privacy/).
