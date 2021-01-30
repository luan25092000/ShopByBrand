<?php

namespace Magenest\ShopByBrand\Helper;

use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand;
use Magento\Framework\App\Helper\Context;

class ConfiguredPrice extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $_brand;
    /**
     * @var Brand
     */
    protected $_brandResource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magenest\ShopByBrand\Helper\Brand
     */
    protected $brandHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $repository;

    /**
     * ConfiguredPrice constructor.
     * @param Context $context
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brand
     * @param Brand $brandResource
     * @param \Magenest\ShopByBrand\Helper\Brand $brandHelper
     * @param \Magento\Framework\View\Asset\Repository $repository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magenest\ShopByBrand\Model\BrandFactory $brand,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource,
        \Magenest\ShopByBrand\Helper\Brand $brandHelper,
        \Magento\Framework\View\Asset\Repository $repository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_brand = $brand;
        $this->_storeManager = $storeManager;
        $this->_brandResource = $brandResource;
        $this->brandHelper = $brandHelper;
        $this->repository = $repository;
        parent::__construct($context);
    }

    public function getBrand($id)
    {
        $model = $this->_brand->create();
        $this->_brandResource->load($model, $id);
        return $model;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $pathImage = $mediaUrl . \Magenest\ShopByBrand\Model\Config\Router::ROUTER_MEDIA;
        return $pathImage;
    }

    /**
     * Get url path image
     *
     * @param $image
     * @return string
     */
    public function getUrlPathImage($image)
    {
        $result = $this->repository->getUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        if ($image && $this->brandHelper->isImageExists(Router::ROUTER_MEDIA . $image)) {
            $result = $this->getMediaUrl() . $image;
        }
        return $result;
    }
}
