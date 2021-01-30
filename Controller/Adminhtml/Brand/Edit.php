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

/**
 * Class Edit
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class Edit extends Brand
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id    = $this->getRequest()->getParam('brand_id');
        $model = $this->_brandFactory->create();
        $brandResource = $this->_brandResource;
        if ($id) {
            $brandResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                /*
                    * \Magento\Backend\Model\View\Result\Redirect $resultRedirect
                 */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $url = $model->getUrlKey();
        if (strpos($url, ".html") !== false) {
            $url = substr($url, 0, strlen($url)-5);
        }

        $model->setUrlKey($url);

        $this->_coreRegistry->register('shopbybrand', $model);
        $this->_coreRegistry->register('relatedbrand', $model);
        $this->_coreRegistry->register('category', 1);
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(!$model->getId() ? __('New Brand') : __('Edit \'%1\' Brand', $model->getName()));

        return $resultPage;
    }
}
