<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 26/10/2016
 * Time: 09:00
 */

namespace Magenest\ShopByBrand\Block\Featured\Index;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Listing
 * @package Magenest\ShopByBrand\Block\Featured\Index
 */
class Listing extends Template
{


    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollection;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Listing constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Router $router
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        \Magenest\ShopByBrand\Model\Config\Router $router,
        Helper $helper,
        array $data = []
    ) {
        $this->_brandCollection = $brandCollection;
        $this->router = $router;
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->prepareTemplate();
    }

    /**
     *
     */
    public function prepareTemplate()
    {
        $sidebar = $this->getBrandConfig();
        if (strpos($sidebar, '1') !== false) {
            $this->setTemplate('Magenest_ShopByBrand::featured/index/carousel.phtml');
        }
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllBrand()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_brandCollection->create()
            ->addFieldToFilter('featured', 1)
            ->setOrder('name', 'ASC')
            ->addStoreToFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->getData();
    }

    /**
     * @param $data
     * @return string
     */
    public function getBrandsStyle($data)
    {
        return strtoupper(substr($this->helper->convertVNtoEN($data), 0, 1));
    }

    /**
     * @param $key
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkFirstBrand($key)
    {
        $datas = $this->getAllBrand();
        foreach ($datas as $data) {
            $first = $this->getBrandsStyle($this->helper->convertVNtoEN($data['name']));
            if ($first == $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * @return mixed
     */
    public function getBrandConfig()
    {
        return $this->helper->getFeaturedBrand();
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrandUrl()
    {
        $configUrl = $this->getUrlRewrite();
        return $this->_storeManager->getStore()->getBaseUrl() . $configUrl;
    }

    /**
     * @return mixed
     */
    public function getUrlRewrite()
    {
        $value = $this->_scopeConfig->getValue('shopbybrand/page/url');
        return $value;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->getBaseMediaUrl() . Router::ROUTER_MEDIA . '/';
    }

    /**
     * @param $brand
     * @return string
     */
    public function getImage($brand)
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        if ($brand['logo'] != '' && $this->helper->isImageExists(Router::ROUTER_MEDIA . $brand['logo'])) {
            $brand['logo'] = $baseUrl . Router::ROUTER_MEDIA . $brand['logo'];
        } else {
            $brand['logo'] = $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        }

        return $brand['logo'];
    }

    /**
     * @return mixed
     */
    public function getTitleBrand()
    {
        $value = $this->_scopeConfig->getValue('shopbybrand/brandpage/title_brand');
        return $value;
    }
}
