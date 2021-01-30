<?php
/**
 * Created by PhpStorm.
 * User: ninhvu
 * Date: 19/01/2018
 * Time: 09:27
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand;

use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Backend\Block\Template;

/**
 * Class Validate
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand
 */
class Validate extends Template
{
    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $brandFactory;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand
     */
    protected $_brandResource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;
    /**
     * @var mixed
     */
    protected $id;
    /**
     * @var null
     */
    protected $_brandModel;
    /**
     * Validate constructor.
     * @param Template\Context $context
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magenest\ShopByBrand\Model\BrandFactory $brandFactory,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->brandFactory = $brandFactory;
        $this->_brandResource = $brandResource;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->_brandModel = null;
    }
    public function getBrandById()
    {
        $id = $this->getRequest()->getParam('brand_id');
        if ($this->_brandModel == null || $this->_brandModel->getId() != $this->getRequest()->getParam('brand_id')) {
            $brand = $this->brandFactory->create();
            $this->_brandResource->load($brand, $id);
            $this->_brandModel = $brand;
        }
        return $this->_brandModel;
    }
    public function getBanner()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getBrandById()->getBanner() == '') {
            return null;
        }

        return $mediaUrl . Router::ROUTER_MEDIA . $this->getBrandById()->getBanner();
    }

    public function getLogo()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getBrandById()->getLogo() == '') {
            return null;
        }

        return $mediaUrl . Router::ROUTER_MEDIA . $this->getBrandById()->getLogo();
    }

    public function getLogoBrandDetail()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getBrandById()->getLogoBrandDetail() == '') {
            return null;
        }

        return $mediaUrl . Router::ROUTER_MEDIA . $this->getBrandById()->getLogoBrandDetail();
    }
}
