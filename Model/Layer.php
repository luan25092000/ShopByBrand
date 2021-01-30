<?php

namespace Magenest\ShopByBrand\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

class Layer extends \Magento\Catalog\Model\Layer
{
    /** @var \Magenest\ShopByBrand\Helper\Brand $_brandHelper */
    protected $_brandHelper;

    /** @var Brand $brand */
    protected $brand;

    /** @var ResourceModel\Brand\CollectionFactory $brandCollection */
    protected $brandCollection;

    //Apart from the default construct argument you need to add your model from which your product collection is fetched.
    /** @var \Magento\Framework\App\RequestInterface $request */
    protected $request;

    /** @var \Magento\CatalogInventory\Helper\Stock $_stockFilter */
    protected $_stockFilter;

    /**
     * Layer constructor.
     *
     * @param \Magenest\ShopByBrand\Helper\Brand $brandHelper
     * @param Brand $brand
     * @param ResourceModel\Brand\CollectionFactory $brandCollection
     * @param \Magento\Catalog\Model\Layer\ContextInterface $context
     * @param \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $catalogProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\CatalogInventory\Helper\Stock $stockFilter
     * @param array $data
     */
    public function __construct(
        \Magenest\ShopByBrand\Helper\Brand $brandHelper,
        \Magenest\ShopByBrand\Model\Brand $brand,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
        $this->_brandHelper = $brandHelper;
        $this->brand = $brand;
        $this->brandCollection = $brandCollection;
        $this->request = $request;
        $this->_stockFilter = $stockFilter;
    }

    public function getProductCollection()
    {
        $filter = $this->request->getParam('brand_id');
        $this->getCurrentBrand();
        if (isset($this->_productCollections['magenest_brand'])) {
            $collection = $this->_productCollections['magenest_brand'];
        } else {
            $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
            $ids = $this->getListId($filter);
            $this->prepareProductCollection($collection);
            $visibility = [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_BOTH
            ];
            $collection = $collection->addAttributeToFilter('entity_id', ['in' => $ids])
                ->addAttributeToFilter(
                    'status',
                    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                )->addFieldToFilter(
                    'visibility',
                    ['in',$visibility]
                );
            $this->_stockFilter->addInStockFilterToCollection($collection);
            $this->_productCollections['magenest_brand'] = $collection;
        }

        return $collection;
    }

    /**
     * @param $id
     * @return array
     */
    public function getListId($id)
    {
        return $this->brandCollection->create()->getProductIdsByBrandId($id);
    }

    public function getCategories()
    {
        $filter = $this->request->getParam('brand_id');
        $categories = $this->brandCollection->create()->addFieldToFilter('brand_id', $filter)->getFirstItem();
        return $categories->getData('categories');
    }

    public function getCurrentBrand()
    {
        $brand = $this->getData('current_brand');
        if ($brand === null) {
            $brand = $this->registry->registry('current_brand');
            if ($brand) {
                $this->setData('current_brand', $brand);
            } else {
                $filter = $this->request->getParam('brand_id');
                $brand = $this->brand->load($filter);
                $this->registry->register('current_brand', $brand);
                $this->setData('current_brand', $brand);
            }
        }

        return $brand;
    }
}
