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

namespace Magenest\ShopByBrand\Controller\Brand;

use Magenest\ShopByBrand\Helper\Brand as Helper;

/**
 * Class View
 * @package Magenest\ShopByBrand\Controller\Brand
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $brand;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var Helper
     */
    protected $_helper;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand
     */
    protected $_brandResource;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brand
     * @param Helper $_helper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\ShopByBrand\Model\BrandFactory $brand,
        Helper $_helper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource
    ) {
        $this->layerResolver = $layerResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->brand = $brand;
        $this->_helper = $_helper;
        $this->_brandResource = $brandResource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->layerResolver->create('customlayer');
        $model = $this->brand->create();
        $brandResource = $this->_brandResource;

        $id = $this->getRequest()->getParam('brand_id');
        if (!$id) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath("*/*/index");
        }

        $brandResource->load($model, $id);
        if ($model->getStatus() != 1) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage('No brand is not activated!');
            return $resultRedirect->setPath("*/*/index");
        }

        $resultPage = $this->resultPageFactory->create();
        if ($model->getPageTitle()) {
            $resultPage->getConfig()->getTitle()->set($model->getPageTitle());
        }

        return $resultPage;
    }
}
