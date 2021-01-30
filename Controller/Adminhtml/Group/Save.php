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
use Magenest\ShopByBrand\Model\Config\Router;

/**
 * Class Save
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Group
 */
class Save extends Group
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $error = false;

        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /**
             * @var \Magenest\ShopByBrand\Model\Group $model
             */
            $model = $this->_groupBrandFactory->create();

            $listGroup = $this->_groupBrandFactory->create()->getCollection();
            $nameData = strtolower(trim($data['name']));

            if ($nameData == 'all') {
                $this->messageManager->addErrorMessage('Cannot create group name with "All"');
                if (isset($data['group_id'])) {
                    return $resultRedirect->setPath('*/*/edit', array('group_id' => $data['group_id']));
                } else {
                    return $resultRedirect->setPath('*/*/new');
                }
            }
            foreach ($listGroup as $group) {
                $namedb = strtolower(trim($group->getName()));
                $groupNames[$namedb] = $group->getId();
            }

            //check duplicate title
            if (isset($data['group_id'])) {
                $group = $this->_groupBrandFactory->create()->load($data['group_id']);
                if (strtolower(trim($group->getName())) != $nameData) {
                    if (isset($groupNames[$nameData])) {
                        $this->messageManager->addErrorMessage("Duplicate brand title");
                        return $resultRedirect->setPath('*/*/edit', array('group_id' => $data['group_id']));
                    }
                }
            } else {
                if (isset($groupNames[$nameData])) {
                    $this->messageManager->addErrorMessage('This title is exist');
                    return $resultRedirect->setPath('*/*/new');
                }
            }

            if (!empty($data['group_id'])) {
                $model->load($data['group_id']);
                if ($data['group_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong brand.'));
                }
            }

            if ($error == true) {
                return $resultRedirect->setPath('*/*/edit', array('group_id' => $model->getId(), '_current' => true));
            }

            $model->setData($data);
            $this->_session->setPageData($model->getData());

            try {
                $model->save();

                $this->messageManager->addSuccessMessage(__('Group has been saved.'));
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', array('group_id' => $model->getId(), '_current' => true));
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the brand.'));
                $this->_session->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', array('group_id' => $this->getRequest()->getParam('group_id')));
            }
        }
    }


    /**
     * Get all store id
     *
     * @return array
     */
    public function getAllStoreId()
    {
        $allStoreId = array();

        $allStore = $this->_storeManage->getStores($withDefault = false);

        foreach ($allStore as $store) {
            $allStoreId[] = $store->getStoreId();
        }

        return $allStoreId;
    }

    /**
     * @param $data
     * @param $id
     */
    public function addUrlRewrite($data, $id)
    {
        $allStoreId = $this->getAllStoreId();

        $model = $this->_urlRewrite->create();
        $router = Router::ROUTER_GROUP;
        if (!empty($data['group_id'])) {
            $collection = $model->getCollection()
                ->addFieldToFilter('entity_type', 'group')
                ->addFieldToFilter('entity_id', $id);
            foreach ($collection as $model) {
                $model->delete();
            }

            $page = array();
            $page['url_rewrite_id'] = null;
            $page['entity_type'] = 'group';
            $page['entity_id'] = $id;
            $page['request_path'] = $router . '/' . $data['url_key'];
            $page['target_path'] = 'shopbybrand/group/view/group_id/' . $id;

            $model = $this->_urlRewrite->create();

            foreach ($allStoreId as $id) {
                $page['store_id'] = $id;
                $model->setData($page);
                $model->save();
            }
        } else {
            $page = array();
            $page['url_rewrite_id'] = null;
            $page['entity_type'] = 'group';
            $page['entity_id'] = $id;
            $page['request_path'] = $router . '/' . $data['url_key'];
            $page['target_path'] = 'shopbybrand/group/view/group_id/' . $id;

            foreach ($allStoreId as $id) {
                $page['store_id'] = $id;
                $model->setData($page);
                $model->save();
            }
        }
    }
}
