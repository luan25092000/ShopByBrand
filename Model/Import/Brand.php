<?php
namespace Magenest\ShopByBrand\Model\Import;

use Exception;
use Magenest\ShopByBrand\Helper\ImportDataHelper;
use Magenest\ShopByBrand\Model\ResourceModel\Group as GroupBrandResourceModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\ResourceModel\Iterator as IteratorResourceModel;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Psr\Log\LoggerInterface;

/**
 * Class Brand
 * @package Magenest\ShopByBrand\Model\Import
 */
class Brand extends AbstractEntity
{
    const SHOPBYBRAND_URL = 'shopbybrand/page/url';
    const REWRITE_URL_TARGET_PATH = 'shopbybrand/brand/view/brand_id/';
    const ENTITY_CODE = 'magenest_shopbybrand_import';
    const TABLE = 'magenest_shop_brand';
    const BRAND_STORES_TABLE = 'magenest_brand_store';
    const BRAND_PRODUCT_TABLE = 'magenest_brand_product';
    const URL_REWRITE_TABLE = 'url_rewrite';
    const CATALOG_PRODUCT_ENTITY_TABLE = 'catalog_product_entity';
    const ENTITY_ID_COLUMN = 'brand_id';
    const GROUP_IDS_COLUMN = 'groups';
    const UNIQUE_NAME_COLUMN = 'name';
    const UNIQUE_URL_KEY_COLUMN = 'url_key';
    const STORES_COLUMN = 'store';
    const PRODUCTS_COLUMN = 'products';

    /** @var ImportDataHelper  */
    protected $_importDataHelper;

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        'name'
    ];

    /**
     * @var string[]
     */
    protected $_imageColumns = [
        'logo',
        'logo_brand_detail',
        'banner'
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'store',
        'products',
        'name',
        'url_key',
        'description',
        'logo',
        'banner',
        'page_title',
        'meta_keywords',
        'meta_description',
        'groups',
        'status',
        'featured',
        'logo_brand_detail',
        'short_description_hover',
        'related_brand'
    ];

    /**
     * @var string[]
     */
    protected $urlRewriteColumnNames = [
        'entity_type',
        'entity_id',
        'request_path',
        'target_path',
        'store_id'
    ];

    /** @var ResourceConnection  */
    private $resource;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface  */
    protected $connection;

    /** @var EventManagerInterface|null  */
    protected $_eventManager = null;

    /** @var ScopeConfigInterface  */
    protected $_scoreConfig;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var IteratorResourceModel  */
    protected $_resourceIterator;

    /**
     * @var array
     */
    protected $_stores = [];

    /**
     * @var array
     */
    protected $_products = [];

    /**
     * @var null
     */
    protected $_shopByBrandUrl = null;

    /**
     * @var array
     */
    protected $_summary = [];

    /**
     * @var array
     */
    protected $_brandStoresData = [];

    /**
     * @var array
     */
    protected $_urlRewritesData = [];

    /**
     * @var array
     */
    protected $_productBrandsData = [];

    /**
     * @var File
     */
    protected $_fileSystemIo;

    protected $json;

    /**
     * @var \Magento\ImportExport\Model\History
     */
    protected $historyModel;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;

    /**
     * Brand constructor.
     * @param ImportDataHelper $importDataHelper
     * @param GroupBrandResourceModel $groupBrandResourceModel
     * @param EventManagerInterface $eventManager
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param Data $importData
     * @param \Magento\Eav\Model\Config $config
     * @param ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param IteratorResourceModel $iteratorResource
     * @param File $fileSystemIo
     * @param \Magento\ImportExport\Model\History $historyModel
     * @param \Magento\Catalog\Model\Product\Action $productAction
     */
    public function __construct(
        ImportDataHelper $importDataHelper,
        GroupBrandResourceModel $groupBrandResourceModel,
        EventManagerInterface $eventManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        IteratorResourceModel $iteratorResource,
        File $fileSystemIo,
        \Magento\ImportExport\Model\History $historyModel,
        \Magento\Catalog\Model\Product\Action $productAction
    ) {
        $this->_availableBehaviors = [
            \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND,
        ];
        $this->_importDataHelper = $importDataHelper;
        $this->_eventManager = $eventManager;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->_scoreConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->_resourceIterator = $iteratorResource;
        $this->_fileSystemIo = $fileSystemIo;
        $this->historyModel = $historyModel;
        $this->productAction = $productAction;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'magenest_shopbybrand_import';
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Import data
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function _importData(): bool
    {
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteEntity();
                break;
            case Import::BEHAVIOR_APPEND:
            case Import::BEHAVIOR_REPLACE:
                $this->saveAndReplaceEntity();
                break;
        }
        $this->_eventManager->dispatch('magenest_shopbybrand_import_finish_before', ['adapter' => $this]);
        return true;
    }

    /**
     * Delete entities
     *
     * @return bool
     */
    private function deleteEntity(): bool
    {
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowId = $rowData[static::UNIQUE_NAME_COLUMN];
                    $rows[] = $rowId;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($rows) {
            return $this->deleteEntityFinish(array_unique($rows));
        }
        return false;
    }

    /**
     * Save and replace entities
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveAndReplaceEntity()
    {
        try {
            $behavior = $this->getBehavior();
            $rows = [];
            $count = 0;
            while ($bunch = $this->_dataSourceModel->getNextBunch()) {
                $entityList = [];
                $stores = [];
                $products = [];
                foreach ($bunch as $rowNum => $row) {
                    if (!$this->validateRow($row, $rowNum)) {
                        continue;
                    }

                    if ($this->getErrorAggregator()->hasToBeTerminated()) {
                        $this->getErrorAggregator()->addRowToSkip($rowNum);

                        continue;
                    }
                    $columnValues = [];
                    $rowName = $row[self::UNIQUE_NAME_COLUMN];
                    $rows[] = $rowName;
                    foreach ($this->getAvailableColumns() as $columnKey) {
                        if ($columnKey == self::GROUP_IDS_COLUMN) {
                            $columnValues[$columnKey] = $this->validateGroupIds($row[$columnKey]);
                        } elseif ($columnKey == self::STORES_COLUMN) {
                            $stores[$rowName] = $row[$columnKey];
                        } elseif (in_array($columnKey, $this->_imageColumns)) {
                            // get image from external url
                            if (substr($row[$columnKey], 0, 5)==="https" || substr($row[$columnKey], 0, 4) === "http" || realpath($row[$columnKey])) {
                                $this->_importDataHelper->importImageExternalUrl($row[$columnKey]);
                                $fileInfo = $this->_fileSystemIo->getPathInfo($row[$columnKey]);
                                $columnValues[$columnKey] = '/' . $fileInfo['basename'];
                            } else {
                                $columnValues[$columnKey] = $row[$columnKey];
                            }
                        } elseif ($columnKey == self::PRODUCTS_COLUMN) {
                            $products[$rowName] = $row[$columnKey];
                        } else {
                            $columnValues[$columnKey] = $row[$columnKey];
                        }
                    }
                    $isBrandExists = $this->isBrandExists($row[static::UNIQUE_NAME_COLUMN]);
                    $defaultCategory = $this->_importDataHelper->getCategoryDefault();
                    $columnValues['categories'] = $this->jsonHelper->jsonEncode($defaultCategory);
                    $entityList[$rowName] = $columnValues;
                    if (Import::BEHAVIOR_REPLACE === $behavior) {
                        $this->countItemsCreated += (int) $isBrandExists;
                    } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                        $this->countItemsCreated += (int) !$isBrandExists;
                        $this->countItemsUpdated += (int) $isBrandExists;
                    }
                    $count++;
                }

                if (Import::BEHAVIOR_REPLACE === $behavior) {
                    if ($rows && $this->deleteEntityFinish(array_unique($rows))) {
                        $this->saveEntityFinish($entityList, $stores, $products);
                    }
                } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                    $this->saveEntityFinish($entityList, $stores, $products);
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $groups
     * @return string
     */
    private function validateGroupIds($groups)
    {
        $result = '';
        try {
            $arrGroup = explode(",", $groups);
            $allGroups = $this->_importDataHelper->getAllGroupBrandIds();
            $diff = array_intersect($arrGroup, $allGroups);
            if (is_array($diff) && !empty($diff)) {
                $result = implode(",", $diff);
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param $strStore
     * @return array|false|string[]
     */
    private function validateStoreIds($strStore)
    {
        $result = [];
        try {
            $arrStore = explode(",", $strStore);
            $allStoreIds = $this->_importDataHelper->getAllStoreIds();
            $result = array_intersect($arrStore, $allStoreIds);
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param $strProduct
     * @return array
     */
    private function validateProductIds($strProduct)
    {
        $result = [];
        try {
            $ids = explode(",", $strProduct);
            $ids = array_filter($ids, function ($id) {
                return $id != '';
            });
            if (is_array($ids) && !empty($ids)) {
                $catalogEntityTbl = $this->_importDataHelper->getTableNames(self::CATALOG_PRODUCT_ENTITY_TABLE);
                $select      = $this->connection->select()->from(
                    $catalogEntityTbl,
                    ['entity_id']
                )->where(
                    'entity_id IN ( ? )',
                    $ids
                );
                $result = $this->connection->fetchCol($select);
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $result;
    }

    /**
     * Save entities
     * @param array $entityData
     * @param $stores
     * @param $products
     * @return bool
     */
    private function saveEntityFinish(array $entityData, $stores, $products)
    {
        try {
            if ($entityData) {
                $tableName = $this->_importDataHelper->getTableNames(self::TABLE);
                $rows = [];
                foreach ($entityData as $rowName => $row) {
                    $row['related_brand'] = $this->formatRelatedBrand($row['related_brand']);
                    $rows[] = $row;
                }
                if ($rows) {
                    $this->connection->insertOnDuplicate($tableName, $rows, $this->getBrandColumns());
                    $brandNames = array_keys($stores);
                    $select = $this->connection->select()
                        ->from(
                            $tableName,
                            [
                                self::ENTITY_ID_COLUMN,
                                self::UNIQUE_NAME_COLUMN,
                                self::UNIQUE_URL_KEY_COLUMN
                            ]
                        )
                        ->where(
                            'name IN ( ? )',
                            $brandNames
                        );

                    $this->setStores($stores);
                    $this->setProducts($products);
                    $this->_resourceIterator->walk(
                        $select,
                        [[$this, 'callbackLinkedEntity']],
                        []
                    );
                    //Update summary product of each brand
                    if (!empty($this->_summary)) {
                        $this->connection->insertOnDuplicate($tableName, $this->_summary, [self::ENTITY_ID_COLUMN, 'summary']);
                        $this->_summary = [];
                    }

                    if (!empty($this->_brandStoresData)) {
                        $this->connection->insertOnDuplicate(
                            $this->_importDataHelper->getTableNames(self::BRAND_STORES_TABLE),
                            $this->_brandStoresData,
                            [
                                self::ENTITY_ID_COLUMN,
                                'store_id'
                            ]
                        );
                        $this->_brandStoresData = [];
                    }

                    if (!empty($this->_urlRewritesData)) {
                        $this->connection->insertOnDuplicate(
                            $this->_importDataHelper->getTableNames(self::URL_REWRITE_TABLE),
                            $this->_urlRewritesData,
                            $this->urlRewriteColumnNames
                        );
                        $this->_urlRewritesData = [];
                    }

                    if (!empty($this->_productBrandsData)) {
                        $this->connection->insertOnDuplicate(
                            $this->_importDataHelper->getTableNames(self::BRAND_PRODUCT_TABLE),
                            $this->_productBrandsData,
                            [
                                self::ENTITY_ID_COLUMN,
                                'product_id'
                            ]
                        );
                        $this->_productBrandsData = [];
                    }
                    $message = __(
                        'Created: %1, Updated: %2, Deleted: %3',
                        $this->countItemsCreated,
                        $this->countItemsUpdated,
                        $this->countItemsDeleted
                    );
                    $this->historyModel->setSummary($message);
                    return true;
                }
                return false;
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Linked brand with store and url_rewrite
     * @param $args
     */
    public function callbackLinkedEntity($args)
    {
        try {
            $row = isset($args['row']) ? $args['row'] : [];
            $brandName = isset($row[self::UNIQUE_NAME_COLUMN]) ? $row[self::UNIQUE_NAME_COLUMN] : null;
            $brandId = isset($row[self::ENTITY_ID_COLUMN]) ? $row[self::ENTITY_ID_COLUMN] : null;
            $urlKey = isset($row[self::UNIQUE_URL_KEY_COLUMN]) ? $row[self::UNIQUE_URL_KEY_COLUMN] : null;
            $stores = $this->getStores();
            if ($brandName != null && $brandId != null && !empty($stores) && isset($stores[$brandName])) {
                $store = $stores[$brandName];
                $arrStore = $this->validateStoreIds($store);
                foreach ($arrStore as $st) {
                    $this->_brandStoresData[] = [
                        self::ENTITY_ID_COLUMN => $brandId,
                        'store_id' => $st
                    ];
                    $this->_urlRewritesData[] = $this->addRewriteUrl($st, $brandId, $urlKey);
                }
            }
            $products = $this->getProducts();
            if ($brandName != null && $brandId != null && !empty($products) && isset($products[$brandName])) {
                $product = $products[$brandName];
                $productIds = $this->validateProductIds($product);
                $this->productAction->updateAttributes($productIds, ['brand_id' => $brandId], 0);
                $i = 0;
                foreach ($productIds as $productId) {
                    $this->_productBrandsData[] = [
                        self::ENTITY_ID_COLUMN => $brandId,
                        'product_id' => $productId
                    ];
                    $i++;
                }
                $this->_summary[] = [
                    self::ENTITY_ID_COLUMN => $brandId,
                    'summary' => $i
                ];
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }

    /**
     * @param $args
     */
    public function callbackClearLinkedEntity($args)
    {
        try {
            $row = isset($args['row']) ? $args['row'] : [];
            $brandId = isset($row[self::ENTITY_ID_COLUMN]) ? $row[self::ENTITY_ID_COLUMN] : null;
            $brandStoreTbl = $this->_importDataHelper->getTableNames(static::BRAND_STORES_TABLE);
            $this->connection->delete(
                $brandStoreTbl,
                $this->connection->quoteInto(self::ENTITY_ID_COLUMN . ' = (?)', $brandId)
            );
            $urlRewriteTbl = $this->_importDataHelper->getTableNames(static::URL_REWRITE_TABLE);
            $this->connection->delete(
                $urlRewriteTbl,
                [
                    "entity_id = ?" => $brandId,
                    "entity_type = ?" => 'brand'
                ]
            );
            $brandProductTbl = $this->_importDataHelper->getTableNames(static::BRAND_PRODUCT_TABLE);
            $this->connection->delete(
                $brandProductTbl,
                $this->connection->quoteInto(self::ENTITY_ID_COLUMN . ' = (?)', $brandId)
            );
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }

    /**
     * @param $storeId
     * @param $brandId
     * @param $urlKey
     * @return array
     */
    private function addRewriteUrl($storeId, $brandId, $urlKey)
    {
        $result = [];
        try {
            $shopByBrandUrl = $this->getShopByBrandUrl();
            $result = [
                'entity_type' => 'brand',
                'entity_id' => $brandId,
                'request_path' => $shopByBrandUrl . '/' . $urlKey,
                'target_path' => self::REWRITE_URL_TARGET_PATH . $brandId . '/',
                'store_id' => $storeId
            ];
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param $stores
     * @return $this
     */
    private function setStores($stores)
    {
        $this->_stores = $stores;
        return $this;
    }

    /**
     * @param $products
     * @return $this
     */
    private function setProducts($products)
    {
        $this->_products = $products;
        return $this;
    }

    /**
     * @return array
     */
    private function getStores()
    {
        return $this->_stores;
    }

    /**
     * @return array
     */
    private function getProducts()
    {
        return $this->_products;
    }

    /**
     * Delete entities
     *
     * @param array $entityIds
     *
     * @return bool
     */
    private function deleteEntityFinish(array $entityIds): bool
    {
        if ($entityIds) {
            try {
                //clear brand into linked table
                $tableName = $this->_importDataHelper->getTableNames(static::TABLE);
                $select = $this->connection->select()
                    ->from(
                        $tableName,
                        [
                            self::ENTITY_ID_COLUMN
                        ]
                    )
                    ->where(
                        'name IN ( ? )',
                        $entityIds
                    );
                $this->_resourceIterator->walk(
                    $select,
                    [[$this, 'callbackClearLinkedEntity']],
                    []
                );
                $this->countItemsDeleted += $this->connection->delete(
                    $tableName,
                    $this->connection->quoteInto(static::UNIQUE_NAME_COLUMN . ' IN (?)', $entityIds)
                );
                $message = __(
                    'Created: %1, Updated: %2, Deleted: %3',
                    $this->countItemsCreated,
                    $this->countItemsUpdated,
                    $this->countItemsDeleted
                );
                $this->historyModel->setSummary($message);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    private function getAvailableColumns(): array
    {
        return $this->validColumnNames;
    }

    /**
     * @return string[]
     */
    private function getBrandColumns()
    {
        $validColumnNames = $this->validColumnNames;
        if (($key = array_search(self::STORES_COLUMN, $validColumnNames)) !== false) {
            unset($validColumnNames[$key]);
        }
        if (($key = array_search(self::PRODUCTS_COLUMN, $validColumnNames)) !== false) {
            unset($validColumnNames[$key]);
        }
        return $validColumnNames;
    }

    /**
     * @return mixed
     */
    private function getShopByBrandUrl()
    {
        if ($this->_shopByBrandUrl == null) {
            $this->_shopByBrandUrl = $this->_scoreConfig->getValue(self::SHOPBYBRAND_URL);
        }
        return $this->_shopByBrandUrl;
    }

    /**
     * Format related brand data to json
     *
     * @param string $relatedBrand
     * @return array|string
     */
    public function formatRelatedBrand($relatedBrand)
    {
        if ($relatedBrand) {
            $relatedBrand = explode(',', $relatedBrand);
            $result = [];
            foreach ($relatedBrand as $v) {
                $result[$v] = $v;
            }
            return $this->jsonHelper->jsonEncode($result);
        }
        return "{}";
    }

    /**
     * Check whether brand name is exists
     *
     * @param $brandName
     * @return bool
     */
    public function isBrandExists($brandName)
    {
        $allBrandName = $this->_importDataHelper->getBrandName();
        if (isset($allBrandName) && count($allBrandName)) {
            return in_array($brandName, $allBrandName, true);
        }
        return false;
    }
}
