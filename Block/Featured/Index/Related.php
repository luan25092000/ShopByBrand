<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 12/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Block\Featured\Index;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\BrandFactory;
use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
class Related extends Template implements  \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var BrandFactory
     */
    protected $_brandFactory;
    /**
     * @var Brand
     */
    protected $_brandResource;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var Json
     */
    protected $_json;
    /**
     * @var UrlInterface
     */
    protected $_urlInterface;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    protected $_helper;
    /**
     * Related constructor.
     * @param Template\Context $context
     * @param BrandFactory $brandFactory
     * @param Brand $brandResource
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param Json $json
     * @param UrlInterface $urlInterface
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BrandFactory $brandFactory,
        Brand $brandResource,
        Registry $registry,
        LoggerInterface $logger,
        Json $json,
        UrlInterface $urlInterface,
        StoreManagerInterface $storeManager,
        Helper $helper,
        array $data = []
    ) {
        $this->_brandResource = $brandResource;
        $this->_brandFactory = $brandFactory;
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->_json = $json;
        $this->_urlInterface = $urlInterface;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getRelatedBrand()
    {
        $brandModel = $this->_brandFactory->create();
        $brandId = $this->getRequest()->getParam('brand_id');
        try {
            $this->_brandResource->load($brandModel, $brandId);
            return $this->_json->unserialize($brandModel->getRelatedBrand());
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }
    public function getBaseUrl()
    {
        return $this->_urlInterface->getBaseUrl();
    }
    public function getImageUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getBrandModelById($id)
    {
        $brandModel = $this->_brandFactory->create();
        $this->_brandResource->load($brandModel, $id);
        return $brandModel;
    }
    public function getUrlKey()
    {
        return $this->_helper->getUrlRewrite();
    }
    public function getCurrentRelatedBrand()
    {
        $id = $this->getRequest()->getParam('brand_id');
        $brandModel = $this->_brandFactory->create();
        $this->_brandResource->load($brandModel, $id);
        return $brandModel;
    }
    public function getIdentities()
    {
        return $this->getCurrentRelatedBrand()->getIdentities();
    }

    /**
     * Get Path url image
     *
     * @param $image
     * @return string
     */
    public function getPathUrlImage($image)
    {
        $result = $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        if ($image && $this->_helper->isImageExists(Router::ROUTER_MEDIA . $image)) {
            $result = $this->getImageUrl() . Router::ROUTER_MEDIA . $image;
        }
        return $result;
    }
}
