# Tabela de hooks WooCommerce

Hooks por fase: **carrinho**, **checkout**, **pedido**, **produto**; parâmetros e uso típico.

---

## Carrinho

| Hook | Parâmetros | Uso típico |
|------|------------|------------|
| **woocommerce_add_to_cart** | `$cart_item_key`, `$product_id`, `$quantity`, `$variation_id`, `$variation`, `$cart_item_data` | Log, validação extra, atualizar outro sistema ao adicionar. |
| **woocommerce_cart_item_removed** | `$cart_item_key`, `$cart` | Limpar cache ou meta ao remover item. |
| **woocommerce_before_calculate_totals** | `$cart` | Alterar preços (desconto, taxa) antes do cálculo. |
| **woocommerce_cart_contents_count** | (filtro) | Alterar o número exibido no ícone do carrinho. |

---

## Checkout

| Hook | Parâmetros | Uso típico |
|------|------------|------------|
| **woocommerce_checkout_fields** | `$fields` | Adicionar/remover/reordenar campos do checkout. |
| **woocommerce_checkout_process** | — | Validação custom (campos obrigatórios, regras). |
| **woocommerce_checkout_order_processed** | `$order_id`, `$posted_data`, `$order` | Pós-processamento: notificar, enviar para ERP, invalidar cache. |
| **woocommerce_checkout_create_order** | `$order`, `$data` | Alterar o pedido antes de persistir. |

---

## Pedido (status e vida útil)

| Hook | Parâmetros | Uso típico |
|------|------------|------------|
| **woocommerce_new_order** | `$order_id` | Ação ao criar pedido (log, webhook). |
| **woocommerce_order_status_{status}** | `$order_id` | Ex.: `woocommerce_order_status_completed`, `woocommerce_order_status_processing`. Enviar email, liberar download, atualizar estoque externo. |
| **woocommerce_payment_complete** | `$order_id` | Após pagamento confirmado. |
| **woocommerce_order_status_changed** | `$order_id`, `$old_status`, `$new_status`, `$order` | Qualquer mudança de status. |

---

## Produto

| Hook | Parâmetros | Uso típico |
|------|------------|------------|
| **woocommerce_product_*** | (variados) | Ex.: `woocommerce_product_get_price`, `woocommerce_single_product_summary`. Alterar preço, exibir meta, adicionar conteúdo. |
| **woocommerce_add_cart_item_data** | `$cart_item_data`, `$product_id`, `$variation_id` | Salvar dados custom no item do carrinho (ex.: gravura). |
| **woocommerce_get_item_data** | `$item_data`, `$cart_item` | Exibir no carrinho/checkout os dados adicionados em add_cart_item_data. |

---

## Uso típico

- **Carrinho:** descontos, validações, log.
- **Checkout:** campos custom, validação, integração pós-pedido.
- **Pedido:** notificações, webhooks, mudança de status (completed, processing).
- **Produto:** preço dinâmico, dados por variação, exibição de meta no carrinho/pedido.

Ver **08-woocommerce-hooks.php** para exemplos em PHP; **12-woocommerce-checkout-custom.php** para customizar checkout; **13-woocommerce-gateway-esqueleto.php** para gateway de pagamento.
