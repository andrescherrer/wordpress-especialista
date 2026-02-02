# Prática – Como usar

1. **Plugin:** use um plugin existente ou crie um. Controllers e helpers são registrados em **rest_api_init**.
2. **Copie** os arquivos ou trechos para o plugin (ex.: `includes/REST/`).
3. **Controller completo (01):** instancie a classe no plugin para registrar as rotas.
4. **Resposta/Validator/Erros (02–04):** inclua os arquivos e use as classes nos seus controllers.

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).

**Testes:** ver exemplos em [05-testes-api.md](05-testes-api.md) (curl e PHPUnit).
