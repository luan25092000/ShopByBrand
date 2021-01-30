<?php
namespace Magenest\ShopByBrand\Setup;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;
use Magento\Store\Model\Store;
use Magenest\ShopByBrand\Model\Config\Router;

/**
 * Class InstallSchema
 *
 * @package Magenest\ShopByBrand\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;

    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $_eavSetup;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewrite
     */
    protected $_urlRewrite;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;


    /**
     * @param \Magento\Eav\Setup\EavSetup          $eavSetup
     * @param \Magento\Eav\Model\Entity\Type       $entityType
     * @param \Magento\Eav\Model\Entity\Attribute  $catalogAttribute
     * @param \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetup,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute,
        \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite,
        Store $store
    ) {
        $this->_eavSetup         = $eavSetup;
        $this->_entityTypeModel  = $entityType;
        $this->_catalogAttribute = $catalogAttribute;
        $this->_urlRewrite       = $urlRewrite;
        $this->store             = $store;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $entityTypeModel       = $this->_entityTypeModel;
        $catalogAttributeModel = $this->_catalogAttribute;

        $setup->startSetup();

        /*
            * Create table 'magenest_brand_group'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('magenest_brand_group'))->addColumn(
            'group_id',
            Table::TYPE_INTEGER,
            11,
            array(
             'identity' => true,
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
            ),
            'Group ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Group Name'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Group Url Key'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            array(
             'nullable' => false,
             'default'  => '1',
            ),
            'Status'
        )->setComment('Brand Group');
        $setup->getConnection()->createTable($table);

        /*
            * Create table 'magenest_shop_brand'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('magenest_shop_brand'))->addColumn(
            'brand_id',
            Table::TYPE_INTEGER,
            null,
            array(
             'identity' => true,
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
            ),
            'Brand ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Brand Name'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Brand Url Key'
        )->addColumn(
            'slogan',
            Table::TYPE_TEXT,
            255,
            array('nullable' => true),
            'Slogan'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            array('nullable' => true),
            'Sort Order'
        )->addColumn(
            'summary',
            Table::TYPE_INTEGER,
            10,
            array(
                'unsigned' => true,
                'nullable' => false,
            ),
            'Summary'
        )->addColumn(
            'order_total',
            Table::TYPE_DECIMAL,
            '12,4',
            array(
                'unsigned' => true,
                'nullable' => false,
                'default'  => '0',
            ),
            'Order Total'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            array('nullable' => false),
            'Brand Description'
        )->addColumn(
            'logo',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Brand Logo'
        )->addColumn(
            'banner',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Brand Banner'
        )->addColumn(
            'page_title',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Brand Page Title'
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '64k',
            array('nullable' => false),
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            '64k',
            array('nullable' => false),
            'Meta Description'
        )->addColumn(
            'categories',
            Table::TYPE_TEXT,
            '255',
            array('nullable' => false),
            'Brand Categories'
        )->addColumn(
            'groups',
            Table::TYPE_TEXT,
            '255',
            array('nullable' => false),
            'Groups'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            array(
             'nullable' => false,
             'default'  => Table::TIMESTAMP_INIT,
            ),
            'Brand Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            array(
             'nullable' => false,
             'default'  => Table::TIMESTAMP_INIT_UPDATE,
            ),
            'Brand Modification Time'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            array(
             'nullable' => false,
             'default'  => '1',
            ),
            'Status'
        )->addColumn(
            'featured',
            Table::TYPE_SMALLINT,
            null,
            array(
                'nullable' => false,
                'default'  => '2',
            ),
            'Featured'
        )->addColumn(
            'show_in_sidebar',
            Table::TYPE_SMALLINT,
            null,
            array(
             'nullable' => false,
             'default'  => '1',
            ),
            'Show in Sidebar'
        )->setComment('Brand Information');
        $setup->getConnection()->createTable($table);

        /*
            * Create table 'magenest_brand_product'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('magenest_brand_product'))->addColumn(
            'brand_id',
            Table::TYPE_INTEGER,
            null,
            array(
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
             'default'  => '0',
            ),
            'Brand ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            array(
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
             'default'  => '0',
            ),
            'Product ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            array(
             'nullable' => false,
             'default'  => '0',
            ),
            'Position'
        )->addColumn(
            'featured_product',
            Table::TYPE_SMALLINT,
            null,
            array(
                'nullable' => false,
                'default'  => '0',
            ),
            'Featured Product'
        )->addIndex(
            $setup->getIdxName('magenest_brand_product', array('product_id')),
            array('product_id')
        )->addForeignKey(
            $setup->getFkName(
                'magenest_brand_product',
                'brand_id',
                'magenest_shop_brand',
                'brand_id'
            ),
            'brand_id',
            $setup->getTable('magenest_shop_brand'),
            'brand_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('magenest_brand_product', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Brand Product To Brand Linkage Table');
        $setup->getConnection()->createTable($table);

        /*
         * Create table 'magenest_ShopByBrand_store'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('magenest_brand_store'))->addColumn(
            'brand_id',
            Table::TYPE_INTEGER,
            null,
            array(
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
            ),
            'Brand Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            array(
             'unsigned' => true,
             'nullable' => false,
             'primary'  => true,
            ),
            'Store Id'
        )->addIndex(
            $setup->getIdxName('magenest_brand_store', array('store_id')),
            array('store_id')
        )->addForeignKey(
            $setup->getFkName('magenest_brand_store', 'brand_id', 'magenest_shop_brand', 'brand_id'),
            'brand_id',
            $setup->getTable('magenest_shop_brand'),
            'brand_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('magenest_brand_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment('Shop By Brand To Store Relations');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
