# Prática – Como usar

1. **Plugin:** use um plugin existente ou crie um. Controllers e helpers são registrados em **rest_api_init**.
2. **Copie** os arquivos ou trechos para o plugin (ex.: `includes/REST/`).
3. **Controller completo (01):** instancie a classe no plugin para registrar as rotas.
4. **Resposta/Validator/Erros (02–04):** inclua os arquivos e use as classes nos seus controllers.

**Arquivos 08–11:** schema (08), paginação e headers (09), retry/fallback (10), checklist REST avançado (11).

**Arquivos 12–14 (Auth):** fluxo OAuth2 (12), segurança de tokens – refresh e revogação (13), tabela JWT vs Application Password vs OAuth2 (14).

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).

**Testes:** ver exemplos em [05-testes-api.md](05-testes-api.md) (curl e PHPUnit).
