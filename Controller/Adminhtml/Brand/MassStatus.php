<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Blog extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Blog
 */

namespace Magenest\ShopByBrand\Controller\Adminhtml\Brand;

use Magenest\ShopByBrand\Controller\Adminhtml\Brand;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassStatus
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class MassStatus extends Brand
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $brandCollection = $this->_brandCollection->create();
        $collections = $this->_filter->getCollection($brandCollection);
        $status = (int)$this->getRequest()->getParam('status');

        try {
            $collections->setDataToAll('status', $status)->save();
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $collections->count()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*');
    }
}
