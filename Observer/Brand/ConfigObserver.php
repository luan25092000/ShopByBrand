<?php

namespace Magenest\ShopByBrand\Observer\Brand;

use Magenest\ShopByBrand\Helper\Brand;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ConfigObserver
 * @package Magenest\ShopByBrand\Observer\Brand
 */
class ConfigObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $productCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scoreConfig;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_urlRewrite;

    /**
     * @var Brand
     */
    protected $_brandHelper;

    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand
     */
    protected $_resourceBrand;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory
     */
    protected $_urlResource;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * ConfigObserver constructor.
     * @param CollectionFactory $productCollection
     * @param Brand $brandHelper
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand $resourceBrand
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $configInterface
     * @param \Magento\Store\Model\StoreFactory $store
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory $urlResource
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollection,
        Brand $brandHelper,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $resourceBrand,
        \Magento\Framework\App\Config\ScopeConfigInterface $configInterface,
        \Magento\Store\Model\StoreFactory $store,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory $urlResource,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    ) {
        $this->productCollection = $productCollection;
        $this->_scoreConfig = $configInterface;
        $this->_store = $store;
        $this->_urlRewrite = $urlRewrite;
        $this->_brandHelper = $brandHelper;
        $this->_resourceBrand = $resourceBrand;
        $this->productAction = $productAction;
        $this->_urlResource = $urlResource;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    /**
     * Url Rewrite
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $allBrand = $this->getAllBrand();
        $collection = $this->_scoreConfig->getValue('shopbybrand/page/url');
        $stores = $this->_store->create()->getCollection()->getData();
        $urlResource = $this->_urlResource->create();
        if ($collection) {
            $params = [
                'entity_type' => 'brand',
                'entity_id' => '0',
                'request_path' => $collection,
                'target_path' => 'shopbybrand/brand/index/'
            ];

            $datas = $this->urlRewrite();

            foreach ($allBrand as $brand) {
                $this->addUrlRewrite($brand, $brand['brand_id']);
            }

            if ($datas) {
                $model = $this->_urlRewrite->create();
                foreach ($datas as $data) {
                    $model->unsetData();
                    $urlResource->load($model, $data['url_rewrite_id']);
                    $urlResource->delete($model);
                }
            }

            $model = $this->_urlRewrite->create();
            foreach ($stores as $item) {
                if ($item['code'] != 'admin') {
                    $params['store_id'] = $item['store_id'];
                    $model->setData($params);
                    $urlResource->save($model);
                }
            }
        }
        $this->updateBrandProducts();
    }

    public function urlRewrite()
    {
        $urls = $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('target_path', 'shopbybrand/brand/index/')
            ->getData();
        return $urls;
    }

    public function getAllBrand()
    {
        return $this->_brandHelper->getAllBrand();
    }

    /**
     * @param $data
     * @param $id
     */
    public function addUrlRewrite($data, $id)
    {
        $arrayBrandStore = $this->_resourceBrand->lookupStoreIds($id);
        $model = $this->_urlRewrite->create();
        $urlResource = $this->_urlResource->create();
        $urlRewriteCollection = $this->_urlRewriteCollectionFactory->create();
        $rewritePage = $this->_scoreConfig->getValue('shopbybrand/page/url');
        if (!empty($data['brand_id'])) {
            $collection = $urlRewriteCollection
                ->addFieldToFilter('entity_type', 'brand')
                ->addFieldToFilter('entity_id', $id);
            foreach ($collection as $modelRewrite) {
                $modelRewrite->delete();
            }

            $page = [];
            $page['url_rewrite_id'] = null;
            $page['entity_type'] = 'brand';
            $page['entity_id'] = $id;
            $page['request_path'] = $rewritePage . '/' . $data['url_key'];
            $page['target_path'] = 'shopbybrand/brand/view/brand_id/' . $id;
            foreach ($arrayBrandStore as $id) {
                $page['store_id'] = $id;
                $model->setData($page);
                $urlResource->save($model);
            }
        }
    }

    public function updateBrandProducts()
    {
        $visiable = ['4'];
        if ($visiable) {
            $productCollection = $this->productCollection->create();
            $productIds = $productCollection->addFieldToFilter('visibility', ['nin' => $visiable])->getAllIds();
            if (count($productIds)) {
                $connection = $this->_resourceBrand->getConnection();
                $table = $productCollection->getTable('magenest_brand_product');
                $connection->delete($table, ['product_id IN (' . implode(',', $productIds) . ')']);
                $this->_brandHelper->getBrandCollection()->save();
                $this->productAction->updateAttributes($productIds, [], 0);
            }
        }
    }
}
