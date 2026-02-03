# Relatórios e REST WooCommerce

Endpoints REST do WooCommerce (**orders**, **products**); relatório simples (total de pedidos, por status); uso em dashboard custom.

---

## Endpoints REST WooCommerce

- Base: **`/wp-json/wc/v3/`** (ou `/wc/v2/` conforme versão).
- Autenticação: **Basic Auth** (Consumer Key + Consumer Secret) ou **OAuth 1.0**.
- Principais recursos:
  - **orders** – listar/criar/atualizar pedidos.
  - **products** – listar/criar/atualizar produtos.
  - **customers** – clientes.
  - **reports** – relatórios (vendas, top sellers, etc.).

Exemplo: `GET /wp-json/wc/v3/orders?status=completed&per_page=10`.

---

## Relatório simples (total de pedidos, por status)

No **PHP** (admin ou endpoint custom):

```php
// Total de pedidos por status
$statuses = ['wc-completed', 'wc-processing', 'wc-pending'];
foreach ($statuses as $status) {
    $orders = wc_get_orders([
        'status' => $status,
        'limit'  => -1,
        'return' => 'ids',
    ]);
    $count = is_array($orders) ? count($orders) : 0;
    // usar $count
}

// Total de vendas (valor)
$orders = wc_get_orders(['status' => 'wc-completed', 'limit' => -1]);
$total = 0;
foreach ($orders as $order) {
    $total += (float) $order->get_total();
}
```

Via **REST** (cliente externo): chamar `GET /wp-json/wc/v3/orders?status=completed&per_page=100` e somar no cliente; ou criar um **endpoint custom** que retorne o agregado.

---

## Dashboard custom

- **Widget no admin:** usar `wc_get_orders()` e exibir totais em um metabox ou página de opções.
- **Relatório em página pública:** apenas para usuários com capability (ex.: `manage_woocommerce`); usar os mesmos dados e exibir em tabela/gráfico.
- **API própria:** registrar rota REST que chama `wc_get_orders()` e retorna JSON (total por status, valor total, etc.); o front (React, Vue) consome essa API.

---

## Recursos

- [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/)
- [WooCommerce Developer Documentation](https://woocommerce.com/documentation/plugins/woocommerce/)

Ver **11-woocommerce-hooks-tabela.md** e **08-woocommerce-hooks.php** para hooks; **16-checklist-woocommerce.md** para integrar com WooCommerce com segurança.
