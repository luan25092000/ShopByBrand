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

namespace Magenest\ShopByBrand\Controller\Adminhtml\Group;

use Magenest\ShopByBrand\Controller\Adminhtml\Brand;
use Magenest\ShopByBrand\Controller\Adminhtml\Group;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassStatus
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class MassStatus extends Group
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $brandCollection = $this->_groupBrandFactory->create()->getCollection();
        $collections = $this->_filter->getCollection($brandCollection);
        $status = (int)$this->getRequest()->getParam('status');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $collections->setDataToAll('status', $status)->save();
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $collections->count()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*');
    }
}
