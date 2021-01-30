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
 * Class NewAction
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class NewAction extends Brand
{
    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        return $this->_forward('edit');
    }
}
