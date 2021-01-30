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
namespace Magenest\ShopByBrand\Model;

use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory;

/**
 * Class BrandStore
 * @package Magenest\ShopByBrand\Model
 */
class BrandStore extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('Magenest\ShopByBrand\Model\ResourceModel\BrandStore');
        $this->setIdFieldName('store_id');
    }
}
