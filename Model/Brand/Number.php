<?php
/**
 * Created by PhpStorm.
 * User: ninhvu
 * Date: 09/02/2018
 * Time: 13:19
 */

namespace Magenest\ShopByBrand\Model\Brand;

class Number implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollection;

    /**
     * Number constructor.
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection
     */
    public function __construct(
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection
    ) {
        $this->_brandCollection = $brandCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $brandCollection = $this->_brandCollection->create()
            ->addFieldToFilter('status', 1);

        $arr = [];

        $arr[] = ['value' => 0, 'label' => 0];

        for ($i = 1; $i <= $brandCollection->count(); $i++) {
            $arr[] = ['value' => $i, 'label' => $i];
        }

        return $arr;
    }
}
