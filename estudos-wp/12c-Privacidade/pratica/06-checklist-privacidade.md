# Checklist – Plugin e privacidade

Coleta só o necessário; política atualizada; exportar/apagar implementado; consentimento quando aplicável; documentação.

---

- [ ] **1. Minimização**  
  O plugin coleta apenas os dados necessários para a finalidade declarada.

- [ ] **2. Política de privacidade**  
  Documentação e trecho para a página de Política de Privacidade do site; descrição clara do que é coletado e para quê.

- [ ] **3. Exportar dados**  
  Exportador registrado em `wp_privacy_personal_data_exporters`; callback coleta user_meta/post_meta/options do plugin e retorna itens no formato esperado; testar em Ferramentas > Exportar dados pessoais.

- [ ] **4. Apagar dados**  
  Apagador registrado em `wp_privacy_personal_data_erasers`; callback apaga ou anonimiza dados do usuário; testar em Ferramentas > Apagar dados pessoais.

- [ ] **5. Consentimento**  
  Formulários que coletam dados pessoais têm checkbox de consentimento (não pré-marcado); registro de data/finalidade quando exigido.

- [ ] **6. Documentação**  
  Readme e documentação do plugin explicam coleta, finalidade e como o titular exerce direitos (exportar/apagar via WP ou contato).

- [ ] **7. Retenção**  
  Definir e documentar prazo de retenção dos dados; apagar ou anonimizar quando não forem mais necessários.

---

## Recursos

- [WordPress Privacy Handbook](https://developer.wordpress.org/plugins/privacy/)
- [GDPR](https://gdpr.eu/), LGPD (lei brasileira)

Documentos desta pasta: **01-conceitos-gdpr-lgpd.md** a **05-hooks-core-privacidade.md**, **07-recursos-privacidade.md**.
