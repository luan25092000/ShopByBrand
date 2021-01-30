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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class MassDelete extends Brand
{
    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $brandCollection = $this->_brandCollection->create();
        $collections = $this->_filter->getCollection($brandCollection);
        $totals = 0;
        try {
            foreach ($collections as $item) {
                /**
                 * @var \Magenest\ShopbyBrand\Model\Brand $item
                 */
                $this->deleteUrlRewrite($item->getId());
                $item->delete();
                $totals++;
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the brand(s).'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $id
     */
    public function deleteUrlRewrite($id)
    {
        $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('entity_type', 'brand')
            ->addFieldToFilter('entity_id', $id)
            ->walk('delete');

    }
}
