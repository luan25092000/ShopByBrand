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
namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit;

/**
 * Class Tabs
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('brand_info_tabs');
        $this->setDestElementId('brand_tab_content');
        $this->setTitle(__('Shop By Brand'));
    }

    /**
     * @return \Magento\Backend\Block\Widget\Tabs|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'main',
            [
             'label'   => __('General'),
            'content' => $this->getLayout()->createBlock(
                \Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\Main::class,
                'brand.tab.main'
            )->toHtml(),
            ]
        );
        $this->addTab(
            'images',
            [
             'label'   => __('Images'),
            'content' => $this->getLayout()->createBlock(
                \Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\Images::class,
                'brand.tab.images'
            )->toHtml(),
            ]
        );
        $this->addTab(
            'products',
            [
             'label'   => __('Products'),
            'content' => $this->getLayout()->createBlock(
                \Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\Products::class,
                'brand.tab.products'
            )->toHtml(),
            ]
        );
        $this->addTab(
            'brand',
            [
             'label'   => __('Brand Page'),
            'content' => $this->getLayout()->createBlock(
                \Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\BrandPage::class,
                'brand.tab.brand'
            )->toHtml(),
            ]
        );
        $this->addTab(
            'related_brand',
            [
                'label'   => __('Related Brand'),
                'content' => $this->getLayout()->createBlock(
                    \Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab\RelatedBrand::class,
                    'brand.tab.related_brand'
                )->toHtml(),
            ]
        );
    }
}
