<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ShopByBrand extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ShopByBrand
 */

namespace Magenest\ShopByBrand\Block\Brand\Index;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;

/**
 * Class Listing
 * @package Magenest\ShopByBrand\Block\Brand\Index
 */
class Listing extends \Magento\Framework\View\Element\Template
{
    const BRAND_TOTAL_PRODUCT = 'shopbybrand/brandpage/total_product';
    const TITLE_LIST_BRAND = 'shopbybrand/brandpage/title_list_brand';
    /**
     * @var \Magenest\ShopByBrand\Model\Brand
     */
    protected $brand;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_groupCollection;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand
     */
    protected $_brandResource;
    /**
     * @var CollectionFactory
     */
    protected $_brandCollection;
    /**
     * @var Http
     */
    protected $_request;
    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * Listing constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brand
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource
     * @param CollectionFactory $brandCollection
     * @param Router $router
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory $groupCollection
     * @param Helper $helper
     * @param Http $request
     * @param UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\ShopByBrand\Model\BrandFactory $brand,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        \Magenest\ShopByBrand\Model\Config\Router $router,
        \Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory $groupCollection,
        Helper $helper,
        Http $request,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->brand = $brand;
        $this->router = $router;
        $this->helper = $helper;
        $this->_groupCollection = $groupCollection;
        $this->_brandResource = $brandResource;
        $this->_brandCollection = $brandCollection;
        $this->_request = $request;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    public function _prepareLayout()
    {
        $page_title = $this->helper->getBrandTitle();
        $meta_keywords = $this->helper->getBrandKeyword();
        $meta_description = $this->helper->getBrandDescription();

        if ($page_title) {
            $this->pageConfig->getTitle()->set(__($page_title));
        }

        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }

        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllBrand()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_brandCollection->create()
            ->setOrder('name', 'ASC')
            ->addStoreToFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->getData();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRevAllBrand()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_brandCollection->create()
            ->setOrder('name', 'DESC')
            ->addStoreToFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->getData();
    }
    /**
     * @param $brandId
     * @return mixed
     */
    public function getBrand($brandId)
    {
        return $this->_brandResource->load($this->brand->create(), $brandId);
    }

    /**
     * @param $data
     * @param $key
     * @return bool
     */
    public function getBrandsStyle($data, $key)
    {
        $first = strtoupper(substr($this->helper->convertVNtoEN($data), 0, 1));
        if ($first == $key) {
            return true;
        }
        return false;
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
            $first = strtoupper(substr($this->helper->convertVNtoEN($data['name']), 0, 1));
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
     * @return string
     */
    public function getBaseStaticUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]);
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
        return $this->getBaseUrl() . $this->helper->getUrlRewrite() . '/';
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
    public function getTitleListBrand()
    {
        $value = $this->_scopeConfig->getValue(self::TITLE_LIST_BRAND);
        return $value;
    }

    /**
     * @return mixed
     */
    public function getShowTotalBrandProduct()
    {
        return $this->_scopeConfig->getValue(self::BRAND_TOTAL_PRODUCT);
    }

    /**
     * @return mixed
     */
    public function showBrandList()
    {
        $value = $this->_scopeConfig->getValue('shopbybrand/brandpage/show_list_brand');
        return $value;
    }

    /**
     * @return \Magenest\ShopByBrand\Model\ResourceModel\Group\Collection
     */
    public function getGroupBrandId()
    {
        return $this->_groupCollection->create()->addFieldToFilter('status', 1);
    }

    /**
     * @return mixed
     */
    public function isSortOrderListBrand()
    {
        return $this->_scopeConfig->getValue('shopbybrand/brandpage/sort_order_list_brand');
    }

    /**
     * @return string|null
     */
    public function getRouteFrontName()
    {
        return $this->_request->getRouteName();
    }

    /**
     * @return string
     */
    public function getStaticUrl()
    {
        return $this->_urlInterface->getBaseUrl();
    }

    /**
     * Get all brand use js
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllBrandUseJs()
    {
        $collectionData = $this->getAllBrand();
        $newCollection = [];
        if ($collectionData) {
            $newCollection = $this->helper->changeImagePathCollection($collectionData);
        }
        return $newCollection;
    }
}
