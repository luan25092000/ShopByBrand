<?php

namespace Magenest\ShopByBrand\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $catalogAttribute;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $eavAttribute;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute,
        Store $store,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->catalogAttribute = $catalogAttribute;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->store = $store;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->resourceConnection   = $resourceConnection;
        $this->eavAttribute   = $eavAttribute;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        //EAV product
        if (version_compare($context->getVersion(), '101.2.1') < 0) {
            $eavSetup = $this->eavSetupFactory->create(array('setup' => $setup));

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'brand_related'
            );

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'brand_id'
            );

            $data = array(
                'group' => 'Brand Product',
                'type' => 'varchar',
                'input' => 'select',
                'default' => 0,
                'label' => 'Product Brand',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'source' => 'Magenest\ShopByBrand\Model\ListBrand',
                'visible' => 1,
                'required' => 0,
                'is_user_defined' => 0,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'sort_order' => 100,
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 1,
                'is_visible_in_advanced_search' => 1,
                'is_comparable' => 0,
                'is_filterable' => 1,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 1,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 0,
            );
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'brand_id',
                $data
            );
            $brandIds = $this->catalogAttribute->loadByCode('catalog_product', 'brand_id');
            $brandIds->addData($data)->save();


            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'manufacturer'
            );

            $dataMenu = array(
                'group' => 'Brand Product',
                'type' => 'varchar',
                'input' => 'select',
                'default' => 0,
                'label' => 'Manufacturer',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'source' => 'Magenest\ShopByBrand\Model\ListBrand',
                'visible' => 1,
                'required' => 0,
                'is_user_defined' => 1,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'sort_order' => 100,
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 0,
                'is_visible_in_advanced_search' => 0,
                'is_comparable' => 0,
                'is_filterable' => 0,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 1,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 0,
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'manufacturer',
                $dataMenu
            );
            $menufacturer = $this->catalogAttribute->loadByCode('catalog_product', 'manufacturer');
            $menufacturer->addData($dataMenu)->save();

            $categorySetup = $this->categorySetupFactory->create(array('setup' => $setup));
        }

        if (version_compare($context->getVersion(), '101.2.6') < 0) {
            $setup->startSetup();
            $categorySetup = $this->categorySetupFactory->create(array('setup' => $setup));

            $categorySetup->updateAttribute(
                Product::ENTITY,
                'brand_id',
                'is_user_defined',
                0
            );


            $categorySetup->updateAttribute(
                Product::ENTITY,
                'brand_id',
                'source_model',
                \Magenest\ShopByBrand\Model\ListBrand::class
            );

            $categorySetup->updateAttribute(
                Product::ENTITY,
                'brand_id',
                'backend_type',
                'int'
            );

            $setup->endSetup();
        }
    }
}
