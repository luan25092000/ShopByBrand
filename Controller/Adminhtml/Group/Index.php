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
namespace Magenest\ShopByBrand\Controller\Adminhtml\Group;

use Magenest\ShopByBrand\Controller\Adminhtml\Group;

/**
 * Class Index
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Group
 */
class Index extends Group
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_ShopByBrand::group');
        $resultPage->addBreadcrumb(__('ShopByBrand'), __('ShopByBrand'));
        $resultPage->addBreadcrumb(__('Manage Groups'), __('Manage Groups'));
        $resultPage->getConfig()->getTitle()->prepend(__('Brand Groups'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ShopByBrand::brand');
    }
}
