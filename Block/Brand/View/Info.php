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

namespace Magenest\ShopByBrand\Block\Brand\View;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\BrandFactory;
use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Model\Template\Filter;

/**
 * Class Info
 * @package Magenest\ShopByBrand\Block\Brand\View
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var
     */
    protected $_storeManager;

    /**
     * @var BrandFactory
     */
    protected $_brand;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Filter
     */
    protected $widgetFilter;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    /**
     * @var BrandResource
     */
    protected $_brandResource;
    /**
     * Info constructor.
     * @param Context $context
     * @param BrandFactory $brand
     * @param Helper $helper
     * @param Filter $widgetFilter
     * @param FilterProvider $_filterProvider
     * @param BrandResource $brandResource
     * @param array $data
     */
    public function __construct(
        Context $context,
        BrandFactory $brand,
        Helper $helper,
        Filter $widgetFilter,
        FilterProvider $_filterProvider,
        BrandResource $brandResource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_brand = $brand;
        $this->helper = $helper;
        $this->widgetFilter = $widgetFilter;
        $this->_filterProvider = $_filterProvider;
        $this->_brandResource = $brandResource;
    }

    /**
     * @return array
     */
    public function getBrandInfo()
    {
        $model = $this->_brand->create();
        $id = $this->getRequest()->getParam('brand_id');
        $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $this->_brandResource->load($model, $id);
        $brand = $model->getData();

        $imageDefault = $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        $data = [
            'name' => isset($brand['name']) ? $brand['name'] : '',
            'url_key' => isset($brand['url_key']) ? $brand['url_key'] : '',
            'banner' => $brand['banner'] && $this->helper->isImageExists(Router::ROUTER_MEDIA . $brand['banner'])
                ? $baseUrl . Router::ROUTER_MEDIA . $brand['banner']
                : $imageDefault,
            'logo' => $brand['logo'] && $this->helper->isImageExists(Router::ROUTER_MEDIA . $brand['logo'])
                ? $baseUrl . Router::ROUTER_MEDIA . $brand['logo']
                : $imageDefault,
            'logo_brand_detail' => $brand['logo_brand_detail'] && $this->helper->isImageExists(Router::ROUTER_MEDIA . $brand['logo_brand_detail'])
                ? $baseUrl . Router::ROUTER_MEDIA . $brand['logo_brand_detail']
                : $imageDefault,
            'description' => isset($brand['description']) ? $brand['description'] : '',
            'alt' => isset($brand['name']) ? $brand['name'] : '',
            'type' => '1',
            'page_title' => isset($brand['page_title']) ? $brand['page_title'] : '',
            'meta_keywords' => isset($brand['meta_keywords']) ? $brand['meta_keywords'] : '',
            'meta_description' => isset($brand['meta_description']) ? $brand['meta_description'] : ''
        ];
        return $data;
    }

    /**
     * @param $name
     * @return string
     */
    public function getUrlImage($name)
    {
        return $this->getViewFileUrl('Magenest_ShopByBrand::images/' . $name);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getBrandRewrite()
    {
        return $this->helper->getBrandUrl();
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    public function _prepareLayout()
    {
        $brand = $this->getBrandInfo();

        $page_title = $brand['page_title'];
        $meta_keywords = $brand['meta_keywords'];
        $meta_description = $brand['meta_description'];

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

    public function getDescription($description)
    {
        return $this->_filterProvider->getPageFilter()->filter(
            $description
        );
    }
}
