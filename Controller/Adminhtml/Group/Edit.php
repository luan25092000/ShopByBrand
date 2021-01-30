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
 * Class Edit
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Group
 */
class Edit extends Group
{
    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id    = $this->getRequest()->getParam('group_id');
        $model = $this->_groupBrandFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                /**
                 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
                 */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('groups', $model);

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Group') : __('New Group'));

        $resultPage->addContent($resultPage->getLayout()->createBlock(\Magenest\ShopByBrand\Block\Adminhtml\Group\Edit::class));
        return $resultPage;

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ShopByBrand::brand');
    }
}
