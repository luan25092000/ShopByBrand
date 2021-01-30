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

/**
 * Class Delete
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class Delete extends Brand
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $brandResource = $this->_brandResource;
        $id = $this->getRequest()->getParam('brand_id');
        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_brandFactory->create();
            $brandResource->load($model, $id);
            if ($id != $model->getId()) {
                throw new LocalizedException(__('Wrong brand.'));
            }

            $this->_session->setPageData($model->getData());
            try {
                $this->deleteUrlRewrite($id);
                $brandResource->delete($model);
                $this->messageManager->addSuccessMessage(__('The Brand has been deleted.'));
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->_session->setPageData($id);
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
            }
        }

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
