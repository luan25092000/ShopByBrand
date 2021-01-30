<?php
namespace Magenest\ShopByBrand\Model\Config;

class Visibility implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                'label' => __('Catalog'),
            ],
            [
                'value' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                'label' => __('Search'),
            ],
            [
                'value' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                'label' => __('Catalog, Search'),
            ]
        ];
    }
}
