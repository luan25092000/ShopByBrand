<?php
/**
 * Created by PhpStorm.
 * User: ninhvu
 * Date: 09/02/2018
 * Time: 13:19
 */

namespace Magenest\ShopByBrand\Model\Brand;

class SortBy implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'asc', 'label' => __('Ascending')],
            ['value' => 'desc', 'label' => __('Descending')]
        ];
    }
}
