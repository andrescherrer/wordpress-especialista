# Banco read replica

**Leitura** em réplica; **escrita** no primário; plugin ou constante para definir **$wpdb** para leitura; cuidados (replicação lag).

---

## Conceito

- **Primário:** recebe todas as escritas (INSERT, UPDATE, DELETE).
- **Réplica(s):** cópia do banco (replicação assíncrona); usada apenas para **SELECT**.
- Objetivo: distribuir leituras (queries pesadas, listagens) para a réplica e aliviar o primário.

---

## No WordPress

- O core usa uma única **$wpdb**; por padrão todas as operações vão para o mesmo banco.
- Para usar réplica: **trocar** temporariamente a conexão **$wpdb** (ou usar uma instância separada de wpdb) para a réplica em contextos **somente leitura** (ex.: queries em loops de posts, relatórios).
- Plugins como **HyperDB** ou **LudicrousDB** permitem configurar múltiplos hosts e regras (escrever no primário, ler na réplica).
- Implementação manual: definir constante ou global com host da réplica; em hooks ou em uma camada de dados, abrir conexão com a réplica e executar apenas SELECTs nela.

---

## Cuidados – replicação lag

- A réplica pode estar **atrasada** em relação ao primário (alguns segundos ou mais).
- Se o usuário acabar de fazer uma ação que grava no primário e na próxima request a leitura for na réplica, pode não ver o dado ainda (lag).
- Mitigações: usar réplica só para listagens/relatórios que não dependem do “último segundo”; ou ler do primário após escrita recente (sticky por sessão ou short TTL).

---

## Configuração (exemplo HyperDB)

- HyperDB usa arquivo **db-config.php** com regras por tabela e operação (write → primário, read → réplica).
- Definir hosts do primário e da réplica; o plugin redireciona as queries conforme o tipo.

---

## Recursos

- [HyperDB](https://developer.wordpress.org/advanced-administration/before-install/howto-use-hyperdb/) (WordPress.org); documentação de replicação MySQL/MariaDB.
- Ver **11-fila-externa-sqs-rabbitmq.md**, **13-escalabilidade-checklist.md**; estudos-wp/08 (object cache, CDN).
