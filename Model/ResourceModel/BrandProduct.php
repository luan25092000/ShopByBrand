<?php

namespace Magenest\ShopByBrand\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use function print_r;

/**
 * Class BrandProduct
 * @package Magenest\ShopByBrand\Model\ResourceModel
 */
class BrandProduct extends AbstractDb
{

    public function _construct()
    {
        $this->_init('magenest_brand_product', 'product_id');
    }

}
