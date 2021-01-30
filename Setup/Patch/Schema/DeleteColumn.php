<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 06/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class DeleteColumn implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * DeleteColumn constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }
    public function getAliases()
    {
        return [];
    }
    public static function getDependencies()
    {
        return [];
    }
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->moduleDataSetup->getConnection()->dropColumn(
            $this->moduleDataSetup->getTable('magenest_shop_brand'),
            'sort_order',
            $schemaName = null
        );
        $this->moduleDataSetup->endSetup();
    }
}
