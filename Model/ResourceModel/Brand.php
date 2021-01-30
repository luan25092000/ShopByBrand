<?php

namespace Magenest\ShopByBrand\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Brand
 *
 * @package Magenest\ShopByBrand\Model\ResourceModel
 */
class Brand extends AbstractDb
{
    /**
     * @var
     */
    protected $_brandProductTable;

    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_product;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManage;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var Json
     */
    protected $_json;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagement
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param Json $json
     * @param null $connectionName
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Framework\App\ResourceConnection $resource,
        Json $json,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_product = $productFactory;
        $this->_logger = $logger;
        $this->_storeManage = $storeManagement;
        $this->_resource = $resource;
        $this->_json = $json;
    }

    public function _construct()
    {
        $this->_init('magenest_shop_brand', 'brand_id');
    }

    /**
     * Update Order Brand
     *
     * @param  $productId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateOrderBrand($productId)
    {
        $mainTable = $this->getMainTable();

        $productTable = $this->getTable('magenest_brand_product');

        $adapter = $this->_getConnection('read');

        $select = $adapter->select()->from(
            ['main_table' => $mainTable],
            '*'
        )
            ->join(['product_table' => $productTable], 'main_table.brand_id = product_table.brand_id')
            ->where('product_table.product_id=' . $productId);

        $row = $adapter->fetchAssoc($select);

        return $row;
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $brand
     * @return $this
     */
    protected function _afterDelete(AbstractModel $brand)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('magenest_brand_store'),
            ['brand_id=?' => $brand->getId()]
        );
        $connection->delete(
            $this->getTable('magenest_brand_product'),
            ['brand_id=?' => $brand->getId()]
        );

        return parent::_afterDelete($brand);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     * @throws \Exception
     */
    protected function _afterSave(AbstractModel $object)
    {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        if (empty($newIds)) {
            $newIds = (array)$object->getStoreId();
        }

        if (!empty($newIds)) {
            $this->_updateForeignKey($object, $newIds, $oldIds, $this->getTable('magenest_brand_store'), 'store_id');
            $this->_saveBrandProducts($object);
        }

        $productTable = $this->getTable('magenest_brand_product');
        $adapter = $this->_getConnection('read');
        $select = $adapter->select()->from(
            ['product_table' => $productTable],
            '*'
        )->where('product_table.brand_id=' . $object->getId());

        $summary = $adapter->query($select)->rowCount();
        $adapter->update($this->getTable('magenest_shop_brand'), ['summary' => $summary], ['brand_id = ?' => $object->getId()]);

        return parent::_afterSave($object);
    }

    /**
     * @param $brandId
     * @param $productId
     */
    public function _saveProductBrand($brandId, $productId)
    {
        $connection = $this->getConnection();
        $data = [];
        $data[] = [
            'brand_id' => (int)$brandId,
            'product_id' => (int)$productId,
            'position' => 0,
        ];

        $connection->insertMultiple($this->getBrandProductTable(), $data);
    }

    /**
     * @param $productId
     */
    public function _deleteProductBrand($productId)
    {
        $connection = $this->getConnection();
        $conditions = [
            'product_id = ' . $productId,
        ];

        $connection->delete($this->getBrandProductTable(), $conditions);
    }

    /**
     * Save category products relation
     *
     * @param $category
     * @return $this
     * @throws \Exception
     */
    protected function _saveBrandProducts($category)
    {
        $id = $category->getId();
        $connection = $this->getConnection();
        $products = json_decode($category->getBrandProducts(), true);
        $featuredProduct = $category->getFeaturedProduct();

        if ($products === null) {
            return $this;
        }

        $oldProducts = $category->getProductsPosition();

        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        $update = array_intersect_key($products, $oldProducts);

        /**
         * Delete products from category
         */
        if (!empty($delete)) {
            $cond = ['product_id IN(?)' => array_keys($delete), 'brand_id=?' => $id];
            $connection->delete($this->getBrandProductTable(), $cond);
        }

        /**
         * Add products to category
         */
        if (!empty($insert)) {
            foreach ($insert as $productId => $position) {
                if ($featuredProduct) {
                    $status = 0;

                    foreach ($featuredProduct as $identityId => $proId) {
                        if ($productId == $proId) {
                            $status = 1;
                        }
                    }
                    $data = [
                        'brand_id' => (int)$id,
                        'product_id' => (int)$productId,
                        'position' => (int)$position,
                        'featured_product' => (int)$status,
                    ];
                } else {
                    $data = [
                        'brand_id' => (int)$id,
                        'product_id' => (int)$productId,
                        'position' => (int)$position,
                        'featured_product' => 0,
                    ];
                }
                $connection->insert($this->getBrandProductTable(), $data);
            }
        }

        /**
         * Update product positions in category
         */
        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                if ($featuredProduct) {
                    $status = 0;

                    foreach ($featuredProduct as $identityId => $proId) {
                        if ($productId == $proId) {
                            $status = 1;
                        }
                    }

                    $where = ['brand_id = ?' => (int)$id, 'product_id = ?' => (int)$productId];
                    $bind = ['position' => (int)$position,'featured_product' => (int)$status];
                    $connection->update($this->getBrandProductTable(), $bind, $where);
                } else {
                    $where = ['brand_id = ?' => (int)$id, 'product_id = ?' => (int)$productId];
                    $bind = ['position' => (int)$position,'featured_product' => 0];
                    $connection->update($this->getBrandProductTable(), $bind, $where);
                }
            }
        }

        return $this;
    }

    /**
     * Get Website ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_lookupIds($id, $this->getTable('magenest_brand_store'), 'store_id');
    }

    /**
     * Get ids to which specified item is assigned
     *
     * @param int $id
     * @param string $tableName
     * @param string $field
     * @return array
     */
    protected function _lookupIds($id, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'brand_id = ?',
            (int)$id
        );

        return $adapter->fetchCol($select);
    }

    /**
     * @param $tableName
     * @param $oldProducts
     * @return array
     */
    protected function _lookupIdAllProduct($tableName, $oldProducts)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable($tableName));
        return $this->validateProduct($oldProducts, $adapter->fetchAll($select));
    }

    /**
     * @param $newProduct
     * @param $allProduct
     * @return array
     */
    protected function validateProduct($newProduct, $allProduct)
    {
        $result = [];
        foreach ($newProduct as $productId => $position) {
            foreach ($allProduct as $productOld) {
                if ($productId == $productOld['product_id']) {
                    $check = $productOld;
                    array_push($result, $check);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Update post connections
     *
     * @param AbstractModel $object
     * @param array $newRelatedIds
     * @param array $oldRelatedIds
     * @param $tableName
     * @param $field
     */
    protected function _updateForeignKey(
        AbstractModel $object,
        array $newRelatedIds,
        array $oldRelatedIds,
        $tableName,
        $field
    ) {
        $table = $this->getTable($tableName);
        $insert = array_diff($newRelatedIds, $oldRelatedIds);

        // All store view
        if (!$newRelatedIds[0]) {
            $insert = $this->getAllStore();
            $where = ['brand_id = ?' => (int)$object->getId(), $field . ' IN (?)' => $oldRelatedIds];
            $this->getConnection()->delete($table, $where);
        }

        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = ['brand_id = ?' => (int)$object->getId(), $field . ' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'brand_id' => (int)$object->getId(),
                    $field => (int)$storeId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * @param $idBrand
     */
    public function updateForeignKeyBrandStore($idBrand)
    {
        $oldIds = $this->lookupStoreIds($idBrand);
        $newIds = [];
        $this->updateBrandStore($idBrand, $newIds, $oldIds, $this->getTable('magenest_brand_store'), 'store_id');
    }

    /**
     * @param $idBrand
     * @param array $newRelatedIds
     * @param array $oldRelatedIds
     * @param $tableName
     * @param $field
     */
    protected function updateBrandStore($idBrand, array $newRelatedIds, array $oldRelatedIds, $tableName, $field)
    {
        $table = $this->getTable($tableName);
        $insert = $this->getAllStore();
        if ($oldRelatedIds) {
            $where = ['brand_id = ?' => $idBrand, $field . ' IN (?)' => $oldRelatedIds];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'brand_id' => $idBrand,
                    $field => (int)$storeId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Get all id store
     *
     * @return array
     */
    public function getAllStore()
    {
        $allStoreId = [];
        $allStore = $this->_storeManage->getStores($withDefault = false);
        foreach ($allStore as $store) {
            $allStoreId[] = $store->getStoreId();
        }

        return $allStoreId;
    }

    /**
     * Get positions of associated to category products
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return array
     */
    public function getProductsPosition($category)
    {
        $select = $this->getConnection()->select()->from(
            $this->getBrandProductTable(),
            ['product_id', 'position']
        )->where(
            'brand_id = :brand_id'
        );
        $bind = ['brand_id' => (int)$category->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Category product table name getter
     *
     * @return string
     */
    public function getBrandProductTable()
    {
        if (!$this->_brandProductTable) {
            $this->_brandProductTable = $this->getTable('magenest_brand_product');
        }

        return $this->_brandProductTable;
    }

    /**
     * Get all product id not in brand
     *
     * @param  $id
     * @return array
     */
    public function getListIdProductOut($id)
    {
        $select = $this->getConnection()->select()->from(
            $this->getBrandProductTable(),
            ['product_id']
        )->where(
            'brand_id <>' . $id
        )->group('product_id');

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get all product id
     *
     * @return array
     */
    public function getListIdProduct()
    {
        $select = $this->getConnection()->select()->from(
            $this->getBrandProductTable(),
            ['product_id']
        )->group('product_id');

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param $id
     * @return array
     */
    public function getListIdProductIn($id)
    {
        $select = $this->getConnection()->select()->from(
            $this->getBrandProductTable(),
            ['product_id']
        )->where(
            'brand_id =' . $id
        )->group('product_id');

        return $this->getConnection()->fetchCol($select);
    }

    public function setSummary($id)
    {
        $where = ['brand_id = ?' => (int)$id];
        $shopBrand = $this->getTable('magenest_brand_product');
        $adapter = $this->_getConnection('read');
        $select = $adapter->select()->from(
            ['shop_brand_table' => $shopBrand],
            '*'
        )->where('shop_brand_table.brand_id=' . $id);

        $summary = $adapter->query($select)->rowCount();
        $adapter->update($this->getTable('magenest_shop_brand'), ['summary' => $summary], $where);
    }

    public function insertMultipleBrand($table, $data)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $connection->getTableName($table);
        foreach ($data as $item) {
            $connection->insertMultiple($tableName, $item);
        }
    }

    public function insertMultipleStore($table, $data)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $connection->getTableName($table);
        foreach ($data as $item) {
            foreach ($item as $index => $value) {
                if ($index=="store_id") {
                    if (count($value)>1) {
                        $item[$index] = $this->_json->serialize($value);
                    } else {
                        $item[$index] = $item[$index][0];
                    }
                }
            }
            $connection->insertMultiple($tableName, $item);
        }
    }

    public function insertMultipleProduct($table, $data)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $connection->getTableName($table);
        foreach ($data as $item) {
            $connection->insertMultiple($tableName, $item);
        }
    }

    /**
     * Get all brand name
     *
     * @return array
     * @throws \Exception
     */
    public function getAllBrandName()
    {
        try {
            $select = $this->getConnection()->select()
                ->from($this->getMainTable(), ['name']);
            return $this->getConnection()->fetchCol($select);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
