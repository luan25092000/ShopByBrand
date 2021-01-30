<?php
/**
 * Created by PhpStorm.
 * User: ninhvu
 * Date: 09/02/2018
 * Time: 13:19
 */

namespace Magenest\ShopByBrand\Model\Brand;

use Magenest\ShopByBrand\Model\Brand;
use Magenest\ShopByBrand\Model\Status as BrandStatus;
use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $data =[
            [
                'value' => Brand::STATUS_ENABLE,
                'label' => __("Enable"),
            ],
            [
                'value' => Brand::STATUS_DISABLE,
                'label' => __("Disable"),
            ],
        ];
        return $data;
    }

    public function getOptionArray()
    {
        return [
            BrandStatus::STATUS_ENABLED => __("Enable"),
            BrandStatus::STATUS_DISABLED => __("Disable")
        ];
    }
}
