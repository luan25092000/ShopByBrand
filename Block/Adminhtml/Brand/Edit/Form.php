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
 * Class Form
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit
 */
class Form extends \Magento\Catalog\Block\Adminhtml\Category\AbstractCategory
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);
    }

    /**
     * @var string
     */
    protected $_template = 'catalog/form.phtml';

    /**
     * @return \Magento\Catalog\Block\Adminhtml\Category\AbstractCategory
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'brand_tabs',
            $this->getLayout()->createBlock(\Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tabs::class, 'brand_tabs')
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBrandTabsHtml()
    {
        return $this->getChildHtml('brand_tabs');
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $products = $this->getBrand()->getProductsPosition();
        if (!empty($products)) {
            return $this->_jsonEncoder->encode($products);
        }
        return '{}';
    }

    /**
     * @return string
     */
    public function getRelatedBrandJson()
    {
        $brands = $this->getRelatedBrand()->getRelatedBrand();
        if (!empty($brands)) {
            return $brands;
        }
        return '{}';
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->_coreRegistry->registry('shopbybrand');
    }

    /**
     * @return mixed
     */
    public function getRelatedBrand()
    {
        return $this->_coreRegistry->registry('relatedbrand');
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return true;
    }

    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        return $this->getUrl('shopbybrand/brand/save', $args);
    }
}
