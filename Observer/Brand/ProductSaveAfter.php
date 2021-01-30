<?php

namespace Magenest\ShopByBrand\Observer\Brand;

use Exception;
use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\BrandFactory;
use Magenest\ShopByBrand\Model\BrandProductFactory;
use Magenest\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Magenest\ShopByBrand\Model\ResourceModel\BrandProduct as BrandProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ProductSaveAfter
 * @package Magenest\ShopByBrand\Observer\Brand
 */
class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var Brand
     */
    protected $helper;
    /**
     * @var BrandProductFactory
     */
    protected $brandProduct;
    /**
     * @var BrandProductResource
     */
    protected $_brandProductResource;
    /**
     * @var BrandFactory
     */
    protected $_brand;
    /**
     * @var BrandResource
     */
    protected $_brandResource;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * ProductSaveAfter constructor.
     * @param Brand $helper
     * @param BrandProductFactory $brandProduct
     * @param BrandProductResource $brandProductResource
     * @param BrandFactory $brand
     * @param BrandResource $brandResource
     * @param \Psr\Log\LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Brand $helper,
        BrandProductFactory $brandProduct,
        BrandProductResource $brandProductResource,
        BrandFactory $brand,
        BrandResource $brandResource,
        \Psr\Log\LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->brandProduct = $brandProduct;
        $this->_brandProductResource = $brandProductResource;
        $this->_brand = $brand;
        $this->_brandResource = $brandResource;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $controller = $observer->getEvent()->getData('controller');
        $product = $observer->getEvent()->getData('product');
        $data = $controller->getRequest()->getPostValue();
        $brandProduct = $this->brandProduct->create();
        $brand = $this->_brand->create();
        $this->_brandProductResource->load($brandProduct, $product->getId());
        $oldBrandId = $brandProduct->getBrandId();
        $newBrandId = isset($data['product']['brand_id']) ? $data['product']['brand_id'] : "";
        if (!empty($newBrandId) && ($oldBrandId != $newBrandId || is_null($oldBrandId))) {
            $this->_brandResource->_deleteProductBrand($product->getId());
            $this->_brandResource->_saveProductBrand($data['product']['brand_id'], $product->getId());
            $this->_brandResource->setSummary($data['product']['brand_id']);
            if ($oldBrandId) {
                $this->_brandResource->setSummary($oldBrandId);
                $this->_brandResource->load($brand, $oldBrandId);
                $this->_brandResource->save($brand);
            }
            $this->_brandResource->load($brand, $newBrandId);
            $this->_brandResource->save($brand);
        }
        if (empty($newBrandId) && !empty($oldBrandId)) {
            $this->_brandResource->_deleteProductBrand($product->getId());
            $this->_brandResource->setSummary($oldBrandId);
        }
    }
}
