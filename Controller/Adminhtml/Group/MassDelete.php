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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Group
 */
class MassDelete extends Group
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $brandCollection = $this->_groupBrandFactory->create()->getCollection();
        $collections = $this->_filter->getCollection($brandCollection);
        $totals = 0;
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            /**
             * @var \Magenest\ShopbyBrand\Model\Brand $item
             */
            foreach ($collections as $item) {
                $countBrands = $this->brandFactory->create()->getCollection()
                    ->addFieldToFilter('groups', ['finset' => $item->getId()])
                    ->count();

                if ($countBrands) {
                    $this->messageManager->addErrorMessage("Can't delete group " . $item->getName() . ",please check all brands in this group");
                } else {
                    $this->deleteUrlRewrite($item->getId());
                    $item->delete();
                    $totals++;
                }

            }
            if ($totals) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));

            }
            return $resultRedirect->setPath('*/*/');

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the group(s).'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $id
     */
    public function deleteUrlRewrite($id)
    {
        $this->_urlRewrite->create()->getCollection()
            ->addFieldToFilter('entity_type', 'group')
            ->addFieldToFilter('entity_id', $id)
            ->walk('delete');
    }
}
