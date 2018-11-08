# DemoBostonBundle

This bundle contains two examples:
- direct shopping list to order converter (skips the storefront checkout flow entirely)
- displaying some custom product attrubutes in the storefront product listing

## Shopping List to Order Converter

### Checkout Button

- [demo_boston_shopping_list_checkout in actions.yml](Resources/config/oro/actions.yml#L3-L19) provides a new "Checkout" operation for the shopping list view page.
- [oro_shoppinglist_frontend_request_quote in actions.yml](Resources/config/oro/actions.yml#L21-L22) - here we disable "Request Quote" operations that is normally available on the shopping list view.
- All out-of-the-box checkout workflows should be disabled, otherwise their own starting transitions will appear in the "Checkout" button drop-down.

### Custom Controller

The new operation takes the user to a new route defined in [Resources\config\oro\routing.yml](Resources/config/oro/routing.yml) and [Controller\CheckoutController](Controller/CheckoutController.php) simply calls the converter and redirects the user to the view page of the created order.

### Convertion

The conversion happens in [Converter\ShoppingListToOrderConverter](Converter/ShoppingListToOrderConverter.php) ([service](Resources/config/services.yml#L10-L18)).

## Additional Product Attributes in Product Listing

`product_datagrid_row__product_extra_attributes` is a new block added by the layout update [views\layouts\default\imports\oro_product_grid\extra_attributes.yml](Resources/views/layouts/default/imports/oro_product_grid/extra_attributes.yml#L5-L9) to the `product_datagrid_row_product_specification` block. You can find more information and examples of customizing various storefront catalog pages in the [OroProductBundle documentation](https://github.com/oroinc/orocommerce/tree/master/src/Oro/Bundle/ProductBundle/Resources/doc)

This new block [is configured](Resources/views/layouts/default/imports/oro_product_grid/extra_attributes.yml#L3-L4) to use the following template -  [views\layouts\default\imports\oro_product_grid\extra_attributes.html.twig](Resources/views/layouts/default/imports/oro_product_grid/extra_attributes.html.twig). In order for this template to work we also need to pass the values of these extra attributes from the product object to the template, which is done in [EventListener\FrontendProductDatagridListener](EventListener/FrontendProductDatagridListener.php) ([service](Resources/config/services.yml#L2-L8)).

