<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\ShopByBrand\Model\ResourceModel\Brand;

use const true;

/**
 * Class Collection
 *
 * @package Magenest\ShopByBrand\Model\ResourceModel\Brand
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'brand_id';

    /**
     *  Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\ShopByBrand\Model\Brand', 'Magenest\ShopByBrand\Model\ResourceModel\Brand');
    }

    /**
     * @param null $id
     * @return Collection
     */
    public function addProductToFilter($id = null)
    {
        if (!$this->getFlag('brand_product_filter')) {
            $conditions = [
                           'brand_product.brand_id = main_table.brand_id',
                           'brand_product.product_id = ' . $id,
                          ];
            $this->getSelect()->join(
                ['brand_product' => $this->getTable('magenest_brand_product')],
                join(' AND ', $conditions)
            );
            $this->setFlag('brand_product_filter', true);
        }

        return $this;
    }

    /**
     * @param null $brandId
     * @return $this
     */
    public function addBrandIdToFilter($brandId = null)
    {
        if (!$this->getFlag('brand_id_filter')) {
            $conditions = [
                           'brand_product.brand_id = main_table.brand_id',
                           'brand_product.brand_id=' . $brandId,
                          ];
            $this->getSelect('product_id')->distinct()->join(
                ['brand_product' => $this->getTable('magenest_brand_product')],
                join(' AND ', $conditions)
            );
            $this->setFlag('brand_id_filter', true);
        }

        return $this;
    }

    public function getProductIdsByBrandId($brandId)
    {
        $conditions = [
            'brand_product.brand_id = main_table.brand_id',
            'brand_product.brand_id=' . $brandId,
        ];
        $sql = $this->getConnection()
            ->select()
            ->from(
                ['main_table' => $this->getMainTable()]
            )
            ->join(
                ['brand_product' => $this->getTable('magenest_brand_product')],
                join(' AND ', $conditions),
                ['product_id' => 'product_id']
            )
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('brand_product.product_id');

        return $this->getConnection()->fetchCol($sql);
    }

    public function addProductFeatured($brandId = null)
    {
        if (!$this->getFlag('brand_id_filter')) {
            $conditions = [
                'brand_product.brand_id = main_table.brand_id',
                'brand_product.brand_id=' . $brandId,
                'brand_product.featured_product = 1',
            ];
            $this->getSelect()->join(
                ['brand_product' => $this->getTable('magenest_brand_product')],
                join(' AND ', $conditions)
            );
            $this->setFlag('brand_id_filter', true);
        }

        return $this;
    }

    /**
     * @param null $brandId
     * @param null $product_id
     * @return $this
     */
    public function addProductBrandToFilter($brandId = null, $product_id = null)
    {
        if (!$this->getFlag('brand_product_id_filter')) {
            $conditions = [
                           'brand_product.brand_id = main_table.brand_id',
                           'brand_product.product_id = ' . $product_id,
                           'brand_product.brand_id=' . $brandId,
                          ];
            $this->getSelect()->join(
                ['brand_product' => $this->getTable('magenest_brand_product')],
                join(' AND ', $conditions)
            );
            $this->setFlag('brand_product_id_filter', true);
        }

        return $this;
    }

    public function addUrlFilter($id, $url)
    {
        if ($id==null) {
            $this->addFieldToFilter('url_key', $url);
            return $this;
        } else {
            $this->addFieldToFilter('brand_id', ['nin'=>$id]);
            $this->addFieldToFilter('url_key', $url);
            return $this;
        }
    }
    /**
     * @param null $store
     * @return $this
     */
    public function addStoreToFilter($store = null)
    {
        if (!$this->getFlag('store_filter')) {
            $conditions = [
                           'brand_store.brand_id = main_table.brand_id',
                           'brand_store.store_id =' . $store,
                          ];
            $this->getSelect()->join(
                ['brand_store' => $this->getTable('magenest_brand_store')],
                join(' AND ', $conditions),
                []
            );
            $this->setFlag('store_filter', true);
        }

        return $this;
    }
    public function addStoreAllToFilter($store = null, $brandId = null)
    {
        if (!$this->getFlag('store_filter')) {
            $conditions = [
                'brand_store.brand_id =' . $brandId,
                'brand_store.store_id =' . $store,
            ];
            $this->getSelect()->join(
                ['brand_store' => $this->getTable('magenest_brand_store')],
                join(' AND ', $conditions),
                []
            );
            $this->setFlag('store_filter', true);
        }

        return $this;
    }
    public function addBrandStore($brand_id)
    {
        if (!$this->getFlag('store_filter')) {
            $conditions = [
                'brand_store.brand_id = main_table.brand_id',
                'brand_store.brand_id =' . $brand_id
            ];
            $this->getSelect()->join(
                ['brand_store' => $this->getTable('magenest_brand_store')],
                join(' AND ', $conditions)
            );
            $this->setFlag('store_filter', true);
        }

        return $this;
    }
}
