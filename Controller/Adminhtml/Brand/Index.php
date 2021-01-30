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
namespace Magenest\ShopByBrand\Controller\Adminhtml\Brand;

use Magenest\ShopByBrand\Controller\Adminhtml\Brand;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class Index extends Brand
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_ShopByBrand::brand');
        $resultPage->addBreadcrumb(__('ShopByBrand'), __('ShopByBrand'));
        $resultPage->addBreadcrumb(__('Manage Brand'), __('Manage Brand'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brands'));
        return $resultPage;
    }
}
