# Checklist – Integrar com WooCommerce

Verificar versão mínima; não modificar templates sem child theme/override; usar hooks e filtros; testar com dados reais; compatibilidade com blocos.

---

- [ ] **1. Versão mínima**  
  Verificar WooCommerce ativo e versão: `function_exists('WC')` e `version_compare(WC()->version, '7.0', '>=')` (ajustar versão conforme necessidade).

- [ ] **2. Não modificar templates do core**  
  Evitar editar arquivos em `woocommerce/templates/`. Usar **child theme** e copiar o template para o tema (mesma estrutura de pastas) ou usar **hooks/filtros** para alterar comportamento.

- [ ] **3. Usar hooks e filtros**  
  Preferir `woocommerce_checkout_fields`, `woocommerce_order_status_*`, `woocommerce_add_to_cart`, etc., em vez de sobrescrever templates inteiros.

- [ ] **4. Salvar dados no pedido**  
  Usar **order meta** (`$order->update_meta_data`, `get_meta`) ou **order item meta** (`$item->add_meta_data`) para dados custom; exibir no admin e em emails com hooks apropriados.

- [ ] **5. Testar com dados reais**  
  Testar checkout completo (carrinho, checkout, pagamento, email); testar com produto simples e variável; testar com e sem conta de usuário.

- [ ] **6. Compatibilidade com blocos**  
  Se o checkout for por blocos (WooCommerce Blocks), garantir que campos e validações custom funcionem no contexto de blocos (hooks podem diferir).

- [ ] **7. Performance**  
  Evitar queries pesadas em hooks chamados em toda página (ex.: `woocommerce_before_cart`); usar transients ou cache quando listar muitos pedidos em relatórios.

- [ ] **8. Segurança**  
  Validar e sanitizar dados em `woocommerce_checkout_process`; usar capabilities ao exibir relatórios no admin; não expor dados sensíveis na REST sem autenticação.

---

## Recursos

- [WooCommerce Developer Documentation](https://woocommerce.com/documentation/plugins/woocommerce/)
- [WooCommerce REST API](https://woocommerce.github.io/woocommerce-rest-api-docs/)

Documentos desta pasta: **08-woocommerce-hooks.php**, **11-woocommerce-hooks-tabela.md**, **12-woocommerce-checkout-custom.php**, **13-woocommerce-gateway-esqueleto.php**, **14-woocommerce-produto-variavel.php**, **15-woocommerce-relatorios-rest.md**.
