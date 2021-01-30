<?php

namespace Magenest\ShopByBrand\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use function print_r;

/**
 * Class BrandStore
 * @package Magenest\ShopByBrand\Model\ResourceModel
 */
class BrandStore extends AbstractDb
{

    public function _construct()
    {
        $this->_init('magenest_brand_store', 'store_id');
    }

}
