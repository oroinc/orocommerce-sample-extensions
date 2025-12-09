# OroCommerce Sample Extensions and Customizations

This repository contains various small examples of customizing OroCommerce application.

Some examples are created as separate bundles in [src/Demo/Bundle](src/Demo/Bundle) and
may cover one or multiple use cases. Other examples are created as **bundle-less** customizations
in [bundle-less](bundle-less).

**Please note that while the code of the examples has been checked to work as intended at the time of writing,
there is no guarantee that these examples will work for any particular version of OroCommerce!!!**

## Bundle-less customizations:

* [custom-body-class](bundle-less/custom-body-class-5.1) (5.1):
    - adds a custom class to the body tag of a certain back-office page
    - shows how to use page components, override templates and define custom CSS
* [custom-body-class](bundle-less/custom-body-class-6.1) (6.1):
    - adds a custom class to the body tag of a certain back-office page
    - shows how to use page components, override templates and define custom CSS
    - JavaScript is written using modern ESM syntax (ES6+), that will be required starting from OroCommerce 7.0

## Bundles:

* [DemoBostonBundle](src/Demo/Bundle/BostonBundle):
    - direct shopping list to order converter (skips the storefront checkout flow entirely)
    - displaying some custom product attributes in the storefront product listing
* [DemoPricingBundle](src/Demo/Bundle/PricingBundle):
    - displaying how to integrate external service as a source of prices
    - displaying how to implement lazy loading of prices and price hints for a product list (product grid)
* [DemoShoppingListButtonsBundle](src/Demo/Bundle/ShoppingListButtonsBundle):
    - conditionally hide the "Delete" operation button from the storefront shopping list view page
    - conditionally hide the "Create Order" checkout workflow start button
