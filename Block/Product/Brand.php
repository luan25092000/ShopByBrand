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

namespace Magenest\ShopByBrand\Block\Product;

use Magento\Framework\View\Element\Template\Context;
use Magenest\ShopByBrand\Helper\Brand as BrandHelper;
use Magento\Framework\Registry;

/**
 * Class Brand
 * @package Magenest\ShopByBrand\Block\Product
 */
class Brand extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magenest\ShopByBrand\Helper\Brand
     */
    protected $helper;
    /**
     * @var Registry
     */
    protected $_coreRegistry;


    /**
     * Brand constructor.
     * @param Context $context
     * @param BrandHelper $brandHelper
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        BrandHelper $brandHelper,
        Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->helper = $brandHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getModeDisplay()
    {
        return $this->helper->isShowInProductDetail();
    }

    /**
     * @return bool
     */
    public function isShowBrand()
    {
        $brandhelper = $this->helper->setProduct($this->getProduct());
        return $brandhelper->isShowBrand();
    }

    /**
     * @return array
     */
    public function getBrand()
    {
        return $this->helper->getBrand();
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }
}
