<?php

namespace Magenest\ShopByBrand\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '101.2.2') < 0) {
            $setup->startSetup();

            $setup->getConnection()->dropColumn($setup->getTable('magenest_brand_group'), 'url_key');

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '101.2.3') < 0) {
            $setup->startSetup();

            $setup->getConnection()->dropColumn($setup->getTable('magenest_shop_brand'), 'show_in_sidebar');

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '101.2.5') < 0) {
            $setup->startSetup();

            $tableName = $setup->getTable('magenest_shop_brand');
            $setup->getConnection()->addColumn(
                $tableName,
                'logo_white',
                array(
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'    => 255,
                'nullable' => false,
                'comment' => 'Brand Detail Logo '
                )
            );

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '101.3.0') < 0) {
            $setup->startSetup();

            $tableName = $setup->getTable('magenest_shop_brand');
            $setup->getConnection()->addColumn(
                $tableName,
                'short_description_hover',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'nullable' => false,
                    'comment' => 'Short Descrition Hover'
                )
            );

            $setup->endSetup();
        }
    }
}
