# DemoProductTitleBundle

This bundles contains an example of customizing the title of the product view page.
 
The custom title is constructed from the product name, brand name and product family label separated by dashes. For example, the title of the product view page of product SKU 2JV62 from the OroCommerce sample data set would be "Basic Women’s 4-Pocket Black Scrub Set - ACME - Default".

## Implementation

The bundle [decorates](Resources/config/services.yml#L1-L8) the [default page title provider](https://github.com/oroinc/orocommerce/blob/3.1.4/src/Oro/Bundle/WebCatalogBundle/Resources/config/services.yml#L350-L356) with a custom [ProductTitleDataProvider](Layout/DataProvider/ProductTitleDataProvider.php).
