# DemoShippingTableRatesBundle

DemoShippingTableRatesBundle provides table rates shipping methods to the OroCommerce applications. The table rates may contain a handling fee (per order) and the shipping price either per order or per one purchased item, set, etc.

## Overview

The table rates allows you to define multiple shipping rates based on location, price, weight, or item count in one shipping method.

## Usage

**TBD**

| Price | Handling Fee | Currency | Country ISO2 code | Region Combined code | Postal Code |
| ----- | ------------ | -------- | ----------------- | -------------------- | ----------- |
| 10    | 0            | USD      | DE                |                      | 0           |
| 10    | 15           | USD      | DE                | DE_HB                | 0           |
| 20    | 0            | USD      | DE                |                      | 1           |
| 20    | 15           | USD      | DE                | DE_HB                | 1           |
| 8     | 0            | EUR      | DE                |                      | 0           |
| 8     | 12           | EUR      | DE                | DE_HB                | 0           |
| 16    | 0            | EUR      | DE                |                      | 1           |
| 16    | 12           | EUR      | DE                | DE_HB                | 1           |


```bash
php app/console --env=prod oro:import:file /path/to/the/file.csv --jobName=entity_import_from_csv --processor=demo_shipping_table_rates.add_or_replace --email=your_email@example.com
```

## Expand logic with own fields and rules (Example)

**TBD**

| Price | Handling Fee | Currency | Country ISO2 code | Region Combined code | Postal Code | Weight |
| ----- | ------------ | -------- | ----------------- | -------------------- | ----------- | ------ |
| 10    | 0            | USD      | DE                |                      |             | 10     |
| 10    | 15           | USD      | DE                | DE_HB                |             | 10     |
| 20    | 0            | USD      | DE                |                      |             | 20     |
| 20    | 15           | USD      | DE                | DE_HB                |             | 20     |
| 8     | 0            | EUR      | DE                |                      |             | 30     |
| 8     | 12           | EUR      | DE                | DE_HB                |             | 30     |
| 16    | 0            | EUR      | DE                |                      |             | 40     |
| 16    | 12           | EUR      | DE                | DE_HB                |             | 40     |

### Migration

**TBD**

```php
// src/Acme/Bundle/DemoBundle/Migrations/Schema/AcmeDemoBundleInstaller.php

<?php

namespace Acme\Bundle\DemoBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AcmeDemoBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->addAdditionalColumnsToShippingTableRateTable($schema);
    }

    /**
     * Add additional columns to `demo_shipping_table_rate` table
     *
     * @param Schema $schema
     */
    private function addAdditionalColumnsToShippingTableRateTable(Schema $schema)
    {
        $table = $schema->getTable('demo_shipping_table_rate');
        $table->addColumn('weight', 'string', [
            'length' => 255,
            'notnull' => false,
            OroOptions::KEY => [
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'is_extend' => true,
                ]
            ],
        ]);
        $table->addIndex(['weight']);
    }
}
```

```yml
# src/Acme/Bundle/DemoBundle/Resources/translations/messages.en.yml

demo:
    shippingtablerates:
        shippingtablerate:
            weight.label: Weight
```

### Own price rules

```yml
services:
    acme_demo.provider.price:
        class: 'Acme\Bundle\DemoBundle\Provider\ShippingTableRatesPriceProvider'
        decorates: demo_shipping_table_rates.provider.price
        public: false
        arguments:
             - '@oro_entity.doctrine_helper'
```

```php
<?php
// src/Acme/Bundle/DemoBundle/Provider/ShippingTableRatesPriceProvider.php

namespace Acme\Bundle\DemoBundle\Provider;

use Demo\Bundle\ShippingTableRatesBundle\Provider\ShippingTableRatesPriceProviderInterface;

class ShippingTableRatesPriceProvider implements ShippingTableRatesPriceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(ShippingContextInterface $context)
    {
        // put here your logic
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlingFee(ShippingContextInterface $context)
    {
        // put here your logic
    }
}
```

## Dependencies

* `Oro\Bundle\AddressBundle`
* `Oro\Bundle\CurrencyBundle`
* `Oro\Bundle\EntityBundle`
* `Oro\Bundle\EntityConfigBundle`
* `Oro\Bundle\IntegrationBundle`
* `Oro\Bundle\LocaleBundle`
* `Oro\Bundle\MigrationBundle`
* `Oro\Bundle\ShippingBundle`
