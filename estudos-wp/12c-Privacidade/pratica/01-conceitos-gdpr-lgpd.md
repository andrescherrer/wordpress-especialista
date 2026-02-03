# Conceitos GDPR/LGPD no plugin

**Base legal**; **consentimento**; **minimização de dados**; direito de **acesso**, **retificação**, **exclusão**, **portabilidade**.

---

## Conceitos principais

| Conceito | Descrição |
|----------|------------|
| **Base legal** | Tratamento de dados deve ter base legal (consentimento, contrato, legítimo interesse, etc.). |
| **Consentimento** | Quando exigido, deve ser explícito, informado e revogável; registrar data e finalidade. |
| **Minimização** | Coletar só o necessário para a finalidade declarada. |
| **Direito de acesso** | O titular pode solicitar cópia dos dados que o plugin armazena sobre ele. |
| **Direito de retificação** | O titular pode corrigir dados incorretos. |
| **Direito de exclusão** | “Right to be forgotten”: apagar ou anonimizar dados do titular. |
| **Portabilidade** | Entregar dados em formato estruturado (ex.: JSON) quando aplicável. |

---

## No plugin WordPress

- **Exportar dados:** implementar **exportador** e registrar em `wp_privacy_personal_data_exporters`; o core oferece a ferramenta **Ferramentas > Exportar dados pessoais**.
- **Apagar dados:** implementar **apagador** e registrar em `wp_privacy_personal_data_erasers`; ferramenta **Ferramentas > Apagar dados pessoais**.
- **Política de privacidade:** declarar no texto da política o que o plugin coleta e para quê; sugerir trecho para o usuário do site colar na página de política.
- **Consentimento:** em formulários que coletam dados pessoais, checkbox de consentimento (e registro de data/finalidade se necessário).

Ver **02-exportacao-dados-usuario.php**, **03-exclusao-dados-usuario.php**, **04-politica-consentimento.md** e **05-hooks-core-privacidade.md**.
