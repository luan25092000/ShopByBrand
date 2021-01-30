<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ShopByBrand extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ShopByBrand
 */

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

namespace Magenest\ShopByBrand\Model\Config;

/**
 * Class Product
 *
 * @package Magenest\ShopByBrand\Model\Config
 */
class DisplayMode implements \Magento\Framework\Option\ArrayInterface
{
    const BRAND_NAME_ONLY = "Brand name only";
    const BRAND_NAME_AND_ICON = "Brand name and icon";
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __(self::BRAND_NAME_ONLY),
            ],
            [
                'value' => '1',
                'label' => __(self::BRAND_NAME_AND_ICON),
            ],
        ];
    }
}
