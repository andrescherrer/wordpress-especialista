# Prática – Como usar

1. **Plugin:** crie um plugin ou use um existente. Todas as rotas devem ser registradas no hook **rest_api_init**.
2. **Copie** os trechos dos arquivos `.php` para o seu plugin (um único arquivo ou includes).
3. **Teste:** GET `https://seusite.com/wp-json/meu-plugin/v1/...` (ajuste namespace/rota) ou use Postman/Insomnia.

**Teoria rápida:** no topo de cada `.php` há um bloco **REFERÊNCIA RÁPIDA**. Tudo em uma página: [../REFERENCIA-RAPIDA.md](../REFERENCIA-RAPIDA.md).

**Permalinks:** a REST API exige estrutura de links diferente de "Simples"; use "Nome do post" ou equivalente.

**Arquivos 06–09:** endpoints GET/POST (06), erros e status HTTP (07), auth Application Password (08), tabela de args (09).

**Arquivos 10–11 (JWT):** endpoint de login que emite JWT (10), permission_callback que valida JWT (11). Requer `composer require firebase/php-jwt`. Comparação JWT vs App Password vs OAuth2: ver 03-REST-API-Avancado/pratica/14-auth-jwt-vs-app-password-vs-oauth2.md.
