<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 09/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Controller\Adminhtml\Brand;

use Magenest\ShopByBrand\Controller\Adminhtml\Brand;
class Related extends Brand
{
    public function execute()
    {
        $id    = $this->getRequest()->getParam('brand_id');
        $model = $this->_brandFactory->create();
        $brandResource = $this->_brandResource;
        if ($id) {
            $brandResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('relatedbrand', $model);
        $resultRaw = $this->resultRawFactory->create();
        return  $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\RelatedBrand',
                'brand.tab.related_brand'
            )->toHtml()
        );
    }
}
