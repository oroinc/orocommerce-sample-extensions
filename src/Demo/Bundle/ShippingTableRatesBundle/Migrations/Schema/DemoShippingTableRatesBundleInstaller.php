<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DemoShippingTableRatesBundleInstaller implements Installation
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
        $this->createDemoShippingTableRatesTransportLabelTable($schema);
        $this->addDemoShippingTableRatesTransportLabelForeignKeys($schema);
        $this->createDemoShippingTableRateTable($schema);
        $this->addDemoShippingTableRateForeignKeys($schema);
    }

    /**
     * Create `demo_ship_tbl_rates_trans_lbl` table
     *
     * @param Schema $schema
     */
    private function createDemoShippingTableRatesTransportLabelTable(Schema $schema)
    {
        $table = $schema->createTable('demo_ship_tbl_rates_trans_lbl');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->addUniqueIndex(['localized_value_id']);
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addIndex(['transport_id']);
    }

    /**
     * Add `demo_ship_tbl_rates_trans_lbl` foreign keys.
     *
     * @param Schema $schema
     */
    private function addDemoShippingTableRatesTransportLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('demo_ship_tbl_rates_trans_lbl');
        if (!$table->hasForeignKey('transport_id')) {
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_integration_transport'),
                ['transport_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
        }

        if (!$table->hasForeignKey('localized_value_id')) {
            $table->addForeignKeyConstraint(
                $schema->getTable('oro_fallback_localization_val'),
                ['localized_value_id'],
                ['id'],
                ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
        }
    }

    /**
     * Create `demo_shipping_table_rate` table
     *
     * @param Schema $schema
     */
    private function createDemoShippingTableRateTable(Schema $schema)
    {
        $table = $schema->createTable('demo_shipping_table_rate');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('price', 'money', ['precision' => 0, 'comment' => '(DC2Type:money)']);
        $table->addColumn('handling_fee', 'money', ['precision' => 0, 'comment' => '(DC2Type:money)']);
        $table->addColumn('currency', 'string', ['length' => 3]);
        $table->addColumn('country_code', 'string', ['length' => 2]);
        $table->addColumn('region_code', 'string', ['length' => 16, 'notnull' => false]);
        $table->addColumn('postal_code', 'string', ['length' => 255, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['currency']);
        $table->addIndex(['postal_code']);
    }

    /**
     * Add `demo_shipping_table_rate` foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDemoShippingTableRateForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('demo_shipping_table_rate');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_dictionary_region'),
            ['region_code'],
            ['combined_code'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_dictionary_country'),
            ['country_code'],
            ['iso2_code'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }
}
