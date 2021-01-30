<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 19/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Model\Config;


class SortOrderBrandList implements \Magento\Framework\Option\ArrayInterface
{
    const NUMBER_FIRST = "Number first";
    const CHARACTER_FIRST = "Character first";

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __(self::NUMBER_FIRST)
            ],
            [
                'value' => '1',
                'label' => __(self::CHARACTER_FIRST)
            ]
        ];
    }
}
