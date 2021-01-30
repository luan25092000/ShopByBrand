<?php
/**
 * Created by PhpStorm.
 * User: thien
 * Date: 28/08/2017
 * Time: 13:26
 */

namespace Magenest\ShopByBrand\Controller\Adminhtml\Group;

use Magenest\ShopByBrand\Controller\Adminhtml\Group;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class Delete extends Group
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('group_id');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_groupBrandFactory->create();
            $model->load($id);
            if ($id != $model->getId()) {
                $this->messageManager->addErrorMessage("Can't found Group brand");
                return $resultRedirect->setPath('*/*/edit', array('group_id' => $this->getRequest()->getParam('brand_id')));
            }

            $this->_session->setPageData($model->getData());
            try {
                $countBrands = $this->brandFactory->create()->getCollection()
                    ->addFieldToFilter('groups', ['finset' => $id])
                    ->count();

                if ($countBrands) {
                    $this->messageManager->addErrorMessage("Can't delete this group, please check all brands in this group");
                    return $resultRedirect->setPath('*/*/edit', array('group_id' => $model->getId(), '_current' => true));
                } else {
                    $this->deleteUrlRewrite($id);
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('The Group has been deleted.'));
                    $this->_session->setPageData(false);
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('group_id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setPageData($id);
                return $resultRedirect->setPath('*/*/edit', array('group_id' => $this->getRequest()->getParam('brand_id')));
            }
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
