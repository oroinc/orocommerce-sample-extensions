This repository contains various small examples of customizing OroCommerce application.

Each example is created as a separate bundle in [src/Demo/Bundle](src/Demo/Bundle) and
may cover one or multiple use cases.

**Please note that while the code of the example have been checked to work as intended at the time of writing,
there is no guarantee that these examples will work for any particular version of OroCommerce!!!**


Examples:

* [DemoBostonBundle](src/Demo/Bundle/BostonBundle):
    - direct shopping list to order converter (skips the storefront checkout flow entirely)
    - displaying some custom product attrubutes in the storefront product listing
* [DemoPricingBundle](src/Demo/Bundle/PricingBundle):
    - displaying how to integrate external service as a source of prices
    - displaying how to implement lazy loading of prices and price hints for product list, product grid

