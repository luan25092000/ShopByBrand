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
 * Class Save
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class Save extends Brand
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $brandResource = $this->_brandResource;
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $selectedProduct = $this->_json->unserialize($data['brand_products']);
        $data['summary'] = count($this->_json->unserialize($this->_json->serialize($selectedProduct)));
        $data['name'] = trim($data['brand_name']);
        $data['page_title'] = trim($data['page_title']);
        $data['url_key'] = trim($data['url_key']) . ".html";
        $logo = $this->getRequest()->getFiles('logo');
        $logoBrandDetail = $this->getRequest()->getFiles('logo_white');
        $banner = $this->getRequest()->getFiles('banner');

        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
        */

        if ($data) {
            /**
             * @var \Magenest\ShopByBrand\Model\Brand $model
             */
            $model = $this->_brandFactory->create();

            //get all brand name
            $listBrand = $this->_brandCollection->create();
            $nameData = strtolower(trim($data['brand_name']));
            foreach ($listBrand as $brand) {
                $namedb = strtolower(trim($brand->getName()));
                $brandNames[$namedb] = $brand->getId();
            }

            //check duplicate title
            if (isset($data['brand_id'])) {
                $brand = $brandResource->load($model, $data['brand_id']);
                if (strtolower($model->getName()) != $nameData) {
                    if (isset($brandNames[$nameData])) {
                        $this->messageManager->addErrorMessage("Duplicate brand title");
                        return $resultRedirect->setPath('*/*/edit', ['brand_id' => $data['brand_id']]);
                    }
                }
                $item = $this->_brandCollection->create()->addUrlFilter($data['brand_id'], trim($data['url_key']))->getData();
            } else {
                if (isset($brandNames[$nameData])) {
                    $this->messageManager->addErrorMessage('This title is exist');
                    return $resultRedirect->setPath('*/*/new');
                }
                $item = $this->_brandCollection->create()->addUrlFilter(null, trim($data['url_key']))->getData();
            }

            if ($item) {
                if (isset($data['brand_id'])) {
                    $this->messageManager->addErrorMessage("Duple url_key ");
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $data['brand_id']]);
                }

                $this->messageManager->addErrorMessage("This url_key is exist");
                return $resultRedirect->setPath('*/*/index');
            }

            //all product old save
            $arrProdcutId = [];
            if (!empty($data['brand_id'])) {
                $arrProdcutId = $brandResource->getListIdProductIn($data['brand_id']);
            }

            if (!empty($data['brand_id'])) {
                $brandResource->load($model, $data['brand_id']);
                if ($data['brand_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong brand.'));
                }
            }

            if (isset($logo) && $logo['name'] != '') {
                try {
                    $uploader = $this->helper->getUploadImage();
                    $data['logo'] = $uploader->uploadFileAndGetName('logo', $uploader->getBaseImageDir(), $data);
                    if ($data['logo'] == 'error') {
                        $data['logo'] = '';
                        $this->messageManager->addErrorMessage("Your photos couldn't be uploaded. Photos should be saved as JPG, PNG, GIF, JPEG ");
                    }
                } catch (LocalizedException $e) {
                    throw new LocalizedException(__('Wrong Upload Logo.'));
                }
            } else {
                if (!empty($data['logo']['delete']) && !empty($data['logo']['value'])) {
                    $uploader = $this->helper->getUploadImage();
                    $uploader->deleteFile($data['logo']['value']);
                    $data['logo'] = "";
                } elseif (isset($data['brand_id'])) {
                    $data['logo'] = $model->getData('logo');
                } else {
                    $data['logo'] = "";
                }
            }

            if (isset($logoBrandDetail) && $logoBrandDetail['name'] != '') {
                try {
                    $uploader = $this->helper->getUploadImage();
                    $data['logo_brand_detail'] = $uploader->uploadFileAndGetName('logo_white', $uploader->getBaseImageDir(), $data);
                    if ($data['logo_brand_detail'] == 'error') {
                        $data['logo_brand_detail'] = '';
                        $this->messageManager->addErrorMessage("Your photos couldn't be uploaded. Photos should be saved as JPG, PNG, GIF, JPEG ");
                    }
                } catch (LocalizedException $e) {
                    throw new LocalizedException(__('Wrong Upload Logo.'));
                }
            }

            if (isset($banner) && $banner['name'] != '') {
                try {
                    $uploader = $this->helper->getUploadImage();
                    $data['banner'] = $uploader->uploadFileAndGetName('banner', $uploader->getBaseImageDir(), $data);
                    if ($data['banner'] == 'error') {
                        $data['banner'] = '';
                        $this->messageManager->addErrorMessage("Your photos couldn't be uploaded. Photos should be saved as JPG, PNG, GIF, JPEG");
                    }
                } catch (LocalizedException $e) {
                    throw new LocalizedException(__('Wrong Upload Banner.'));
                }
            } else {
                if (!empty($data['banner']['delete']) && !empty($data['banner']['value'])) {
                    $uploader = $this->helper->getUploadImage();
                    $uploader->deleteFile($data['banner']['value']);
                    $data['banner'] = "";
                } elseif (isset($data['brand_id'])) {
                    $data['banner'] = $model->getData('banner');
                } else {
                    $data['banner'] = "";
                }
            }

            if (isset($data['brand_products']) && is_string($data['brand_products'])
            ) {
                $products = $this->_json->unserialize($data['brand_products']);
                $model->setPostedProducts($products);
                $brandCategories = [];

                foreach ($products as $productId => $position) {
                    $product = $this->_productFactory->create()->load($productId);
                    $cats = $product->getCategoryIds();
                    $brandCategories += $cats;
                }

                if (empty($brandCategories)) {
                    $brandCategories[] = $this->getCategoryDefault();
                }

                $data['categories'] = $this->_json->serialize(array_unique($brandCategories));
            }

            $listGroup = '';
            if (isset($data['groups'])) {
                for ($i = 0; $i < count($data['groups']); $i++) {
                    if ($i == 0) {
                        $listGroup = (int)$data['groups'][$i];
                    } else {
                        $listGroup = $listGroup . ',' . $data['groups'][$i];
                    }
                }
                $data['groups'] = $listGroup;
            } else {
                $data['groups'] = '';
            }

            $model->setData($data);
            $this->_session->setPageData($model->getData());

            try {
                $brandResource->save($model);

                if (isset($data['brand_products']) && is_string($data['brand_products'])) {
                    $products = $this->_json->unserialize($data['brand_products']);
                    $productNew = array_diff_key($products, array_flip($arrProdcutId));
                    $arrProdcutId = array_diff_key(array_flip($arrProdcutId), $products);

                    $productIds = array_keys($productNew);
                    $arrProdcutId = array_keys($arrProdcutId);

                    if ($productIds) {
                        if (isset($data['brand_id']) && $data['brand_id'] != '') {
                            $this->productAction->updateAttributes($productIds, ['brand_id' => $data['brand_id']], 0);
                        } else {
                            $tmpBrand = $this->_brandCollection->create()->getLastItem();
                            $tmpBrandId = $tmpBrand->getBrandId();
                            $this->productAction->updateAttributes($productIds, ['brand_id' => $tmpBrandId], 0);
                        }
                    }

                    if ($arrProdcutId) {
                        $this->productAction->updateAttributes($arrProdcutId, ['brand_id' => null], 0);
                    }
                }

                $this->addUrlRewrite($data, $model->getId());
                $this->messageManager->addSuccessMessage(__('Brand has been saved.'));

                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
            }
        }
    }

    /**
     * @param $data
     * @param $id
     */
    public function addUrlRewrite($data, $id)
    {
        $model = $this->_urlRewrite->create();
        $urlRewriteResource = $this->_urlRewriteFactory->create();
        $rewritePage = $this->_scoreConfig->getValue('shopbybrand/page/url');
        if (!empty($data['brand_id'])) {
            $collection = $this->_urlRewriteCollectionFactory->create()
                ->addFieldToFilter('entity_type', 'brand')
                ->addFieldToFilter('entity_id', $id);
            foreach ($collection as $modelRewrite) {
                $modelRewrite->delete();
            }

            $page = [];
            $page['url_rewrite_id'] = null;
            $page['entity_type'] = 'brand';
            $page['entity_id'] = $id;
            $page['request_path'] = $rewritePage . '/' . trim($data['url_key']);
            $page['target_path'] = 'shopbybrand/brand/view/brand_id/' . $id . '/';
            $model = $this->_urlRewrite->create();
            if ($data['store_ids'][0] == 0) {
                $allIds = $this->getAllStoreId();

                foreach ($allIds as $id) {
                    $page['store_id'] = $id;
                    $model->setData($page);
                    $urlRewriteResource->save($model);
                }
            } else {
                foreach ($data['store_ids'] as $id) {
                    $page['store_id'] = $id;
                    $model->setData($page);
                    $urlRewriteResource->save($model);
                }
            }
        } else {
            $page = [];
            $page['url_rewrite_id'] = null;
            $page['entity_type'] = 'brand';
            $page['entity_id'] = $id;
            $page['request_path'] = $rewritePage . '/' . trim($data['url_key']);
            $page['target_path'] = 'shopbybrand/brand/view/brand_id/' . $id . '/';

            if ($data['store_ids'][0] == 0) {
                $allIds = $this->getAllStoreId();

                foreach ($allIds as $id) {
                    $page['store_id'] = $id;
                    $model->setData($page);
                    $urlRewriteResource->save($model);
                }
            } else {
                foreach ($data['store_ids'] as $id) {
                    $page['store_id'] = $id;
                    $model->setData($page);
                    $urlRewriteResource->save($model);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getAllStoreId()
    {
        $allStoreId = [];

        $allStore = $this->_storeManagement->getStores($withDefault = false);

        foreach ($allStore as $store) {
            $allStoreId[] = $store->getStoreId();
        }
        return $allStoreId;
    }
}
