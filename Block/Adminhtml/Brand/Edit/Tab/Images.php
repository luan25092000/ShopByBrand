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
namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as FormGeneric;

/**
 * Class Images
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab
 */
class Images extends FormGeneric
{

    /**
     * @return FormGeneric
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('shopbybrand');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('images_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Images')]);

        $fieldset->addType('logo', \Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper\Logo::class);
        $fieldset->addType('logo_brand_detail', \Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper\LogoWhite::class);
        $fieldset->addType('banner', \Magenest\ShopByBrand\Block\Adminhtml\Brand\Helper\Banner::class);

        $fieldset->addField(
            'logo',
            'logo',
            [
                'name'     => 'logo',
                'label'    => __('Logo in Brand Page'),
                'title'    => __('Logo in Brand Page'),
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, gif, png (Optimal icon min size is 100 x 100 px)',
            ]
        );

        $fieldset->addField(
            'logo_brand_detail',
            'logo_brand_detail',
            [
                'name'     => 'logo_brand_detail',
                'label'    => __('Logo in Brand Detail'),
                'title'    => __('Logo in Brand Page'),
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, gif, png (Optimal icon min size is 100 x 100 px)',
            ]
        );

        $fieldset->addField(
            'banner',
            'banner',
            [
                'name'     => 'banner',
                'label'    => __('Banner'),
                'title'    => __('Banner'),
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, gif, png (Optimal size is 1360 x 318)',
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
