<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 01/06/2016
 * Time: 09:34
 */
namespace Magenest\ShopByBrand\Model;

/**
 * Class Listbrand
 *
 * @package Magenest\ShopByBrand\Model
 */
class ListBrand extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_brandCollection;
    public function __construct(
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection
    ) {
        $this->_brandCollection = $brandCollection;
    }

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $brands    = $this->_brandCollection->create()
            ->addFieldToFilter('status', '1');
        $listBrand = [];
        $duplicatedBrandCheck = [];
        foreach ($brands as $brand) {
            $value = $brand->getId();
            $brandName = $brand->getName();
            if (isset($duplicatedBrandCheck[$brandName])) {
                $duplicatedBrandCheck[$brandName]++;
                $brandName = $brandName . '-' . $duplicatedBrandCheck[$brandName];
            } else {
                $duplicatedBrandCheck[$brandName] = 1;
            }
            $listBrand[$brand->getId()] = [
                'label' => $brandName,
                'value' => $value,
            ];
        }

        return $listBrand;
    }

    /**
     * Get model option as array
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = $this->getAvailableTemplate();
        if ($withEmpty) {
            array_unshift(
                $options,
                [
                    'value' => null,
                    'label' => __('-- Please Select --'),
                ]
            );
        }
        return $options;
    }
}
