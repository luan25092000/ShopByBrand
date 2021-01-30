<?php

namespace Magenest\ShopByBrand\Observer\Brand;

use Magenest\ShopByBrand\Model\ResourceModel\Brand;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magenest\ShopByBrand\Helper\Brand as BrandHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\ShopByBrand\Model\Config\Router;
use function print_r;
use const true;

/**
 * Class AddUrl
 *
 * @package Magenest\ShopByBrand\Observer\Brand
 */
class AddUrl implements ObserverInterface
{
    /**
     * @var \Magenest\ShopByBrand\Model\Brand
     */
    protected $_brand;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_urlRewrite;
    /**
     * @var Router
     */
    protected $_router;
    /**
     * @var \Magenest\ShopByBrand\Model\GroupFactory
     */
    protected $groupFactory;
    /**
     * @var \Magenest\ShopByBrand\Helper\Brand
     */
    protected $_brandHelper;

    protected $_storeManagement;
    /**
     * @var Brand
     */
    protected $_resourceBrand;
    /**
     * @param \Magenest\ShopByBrand\Model\Brand    $brand
     * @param \Psr\Log\LoggerInterface             $logger
     * @param ManagerInterface                     $messageManager
     * @param ScopeConfigInterface                 $scopeConfig
     * @param \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite
     */
    public function __construct(
        \Magenest\ShopByBrand\Model\Brand $brand,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $resourceModel,
        \Magenest\ShopByBrand\Model\GroupFactory $groupFactory,
        Router $router,
        \Psr\Log\LoggerInterface $logger,
        ManagerInterface $messageManager,
        BrandHelper $brandHelper,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $_urlRewrite
    ) {
        $this->_brand         = $brand;
        $this->_storeManagement     = $storeManagement;
        $this->_logger        = $logger;
        $this->messageManager = $messageManager;
        $this->_scopeConfig   = $scopeConfig;
        $this->_urlRewrite    =$_urlRewrite;
        $this->groupFactory   = $groupFactory;
        $this->_router        = $router;
        $this->_brandHelper   = $brandHelper;
        $this->_resourceBrand = $resourceModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Store\Model\Store $storeModel
         */
        $storeModel = $observer->getEvent()->getData('store');
        $model  = $this->_urlRewrite->create();

        $page = array(
            'url_rewrite_id' => null,
            'entity_type'    => 'brand',
            'entity_id'      => '0',
            'request_path'   => $this->_router->getRouterBrand(),
            'target_path'    => 'shopbybrand/brand/index/',
            'store_id'       => $storeModel->getId()
        );
        $model->setData($page);
        $model->save();

        /*get collection group brand*/
        $groupFactory = $this->groupFactory->create();
        $collection = $groupFactory->getCollection()->getData();
        $router = Router::ROUTER_GROUP;
        foreach ($collection as $data) {
            $page = array();
            $page['url_rewrite_id'] = null;
            $page['entity_type']    = 'group';
            $page['entity_id']      = $data['group_id'];
            $page['request_path']   = $router.'/'.$data['url_key'];
            $page['target_path']    = 'shopbybrand/group/view/group_id/'.$data['group_id'];
            $page['store_id']       = $storeModel->getId();
            $model->setData($page);
            $model->save();
        }

        /*Get collection brand show all store view*/
        $collectionBrand=$this->_brandHelper->getBrandAllStoreView();
        $this->_logger->debug('list brand all');
        $this->_logger->log(100, print_r($collectionBrand, true));
        foreach ($collectionBrand as $data) {
            $this->addUrlRewrite($data, $data['brand_id']);
            $this->_resourceBrand->updateForeignKeyBrandStore($data['brand_id']);
        }
    }
    /**
     * @param $data
     * @param $id
     */
    public function addUrlRewrite($data, $id)
    {
        $model  = $this->_urlRewrite->create();
        $rewritePage = $this->_scopeConfig->getValue('shopbybrand/page/url');
        if (!empty($data['brand_id'])) {
            $collection = $model->getCollection()
                ->addFieldToFilter('entity_type', 'brand')
                ->addFieldToFilter('entity_id', $id);
            foreach ($collection as $model) {
                $model->delete();
            }

            $this->_logger->debug('chay delete');
            $page = array();
            $page['url_rewrite_id'] = null;
            $page['entity_type']    = 'brand';
            $page['entity_id']      = $id;
            $page['request_path']   = $rewritePage.'/'.$data['url_key'];
            $page['target_path']    = 'shopbybrand/brand/view/brand_id/'.$id;
            $model  = $this->_urlRewrite->create();
            /*Add url for all id*/
            $allIds = $this->getAllStoreId();
            foreach ($allIds as $id) {
                $page['store_id']       = $id;
                $model->setData($page);
                $model->save();
            }
        }
    }

    /**
     * Get all store id
     *
     * @return array
     */
    public function getAllStoreId()
    {
        $allStoreId = array();

        $allStore = $this->_storeManagement->getStores($withDefault = false);

        foreach ($allStore as $store) {
            $allStoreId[] = $store->getStoreId();
        }

        return $allStoreId;
    }
}
