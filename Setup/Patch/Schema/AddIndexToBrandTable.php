<?php
namespace Magenest\ShopByBrand\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AddIndexToBrandTable
 * @package Magenest\ShopByBrand\Setup\Patch\Schema
 */
class AddIndexToBrandTable implements SchemaPatchInterface
{

    /** @var ModuleDataSetupInterface  */
    protected $moduleDataSetup;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * AddIndexToBrandTable constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->logger = $logger;
    }

    /**
     * @return AddIndexToBrandTable|void
     */
    public function apply()
    {
        try {
            $this->moduleDataSetup->startSetup();
            $table = $this->moduleDataSetup->getTable('magenest_shop_brand');
            $this->moduleDataSetup->getConnection()->addIndex(
                $table,
                $this->moduleDataSetup->getConnection()->getIndexName(
                    $table,
                    ['name'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['name'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
            $this->moduleDataSetup->getConnection()->addIndex(
                $table,
                $this->moduleDataSetup->getConnection()->getIndexName(
                    $table,
                    ['url_key'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['url_key'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
            $this->moduleDataSetup->endSetup();
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
