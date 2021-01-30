<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 09/12/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class ChangeColumn implements SchemaPatchInterface
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
        $this->moduleDataSetup->getConnection()->changeColumn(
            $this->moduleDataSetup->getTable('magenest_shop_brand'),
            'logo_white',
            'logo_brand_detail',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'    => 255,
                'nullable' => false,
                'comment' => 'Brand Detail Logo'
            ]
        );
        $this->moduleDataSetup->endSetup();
    }
}
