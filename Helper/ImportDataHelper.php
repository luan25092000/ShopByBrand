<?php
namespace Magenest\ShopByBrand\Helper;

use Magenest\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Magenest\ShopByBrand\Model\ResourceModel\Group as GroupBrandResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ImportDataHelper
 * @package Magenest\ShopByBrand\Helper
 */
class ImportDataHelper
{
    protected $_tableNames = [];

    protected $_shopByBrandMediaFolder = null;

    protected $_allGroupBrandIds = null;

    protected $_allStoreIds = null;

    protected $_categoryDefault = null;

    protected $_allBrandName = [];

    /** @var GroupBrandResource  */
    protected $_groupBrandResource;

    /** @var ResourceConnection  */
    protected $_resourceConnection;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface  */
    protected $connection;

    /** @var IoFile  */
    protected $_ioFile;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var DirectoryList  */
    protected $_directoryList;

    /** @var StoreManagerInterface  */
    protected $_storeManagement;

    /** @var CategoryCollectionFactory  */
    protected $_categoriesCollection;

    /**
     * @var BrandResource
     */
    protected $brandResource;

    /**
     * ImportDataHelper constructor.
     * @param GroupBrandResource $groupBrandResource
     * @param BrandResource $brandResource
     * @param ResourceConnection $resourceConnection
     * @param IoFile $ioFile
     * @param LoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManager
     * @param CategoryCollectionFactory $categoryCollection
     */
    public function __construct(
        GroupBrandResource $groupBrandResource,
        BrandResource $brandResource,
        ResourceConnection $resourceConnection,
        IoFile $ioFile,
        LoggerInterface $logger,
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollection
    ) {
        $this->_groupBrandResource = $groupBrandResource;
        $this->brandResource = $brandResource;
        $this->_resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->_ioFile = $ioFile;
        $this->_logger = $logger;
        $this->_directoryList = $directoryList;
        $this->_storeManagement = $storeManager;
        $this->_categoriesCollection = $categoryCollection;
    }

    /**
     * @param $tableName
     * @return mixed
     */
    public function getTableNames($tableName)
    {
        if (empty($this->_tableNames) || !isset($this->_tableNames[$tableName])) {
            $this->_tableNames[$tableName] = $this->_resourceConnection->getTableName($tableName);
        }
        return $this->_tableNames[$tableName];
    }

    /**
     * @param $imageUrl
     * @return bool|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importImageExternalUrl($imageUrl)
    {
        try {
            $tmpDir = $this->getMediaDirTmpDir();
            $this->_ioFile->checkAndCreateFolder($tmpDir);
            $newFileName = $tmpDir . "/" . baseName($imageUrl);
            return $this->_ioFile->read($imageUrl, $newFileName);
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
            return $imageUrl;
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirTmpDir()
    {
        if ($this->_shopByBrandMediaFolder == null) {
            $this->_shopByBrandMediaFolder = $this->_directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'shopbybrand/brand/image';
        }
        return $this->_shopByBrandMediaFolder;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getAllGroupBrandIds()
    {
        if ($this->_allGroupBrandIds == null) {
            $this->_allGroupBrandIds = $this->_groupBrandResource->getGroupIds();
        }
        return $this->_allGroupBrandIds;
    }

    /**
     * @return array
     */
    public function getAllStoreIds()
    {
        if ($this->_allStoreIds == null) {
            $this->_allStoreIds = array_keys($this->_storeManagement->getStores($withDefault = true));
        }
        return $this->_allStoreIds;
    }

    /**
     * @return array
     */
    public function getCategoryDefault()
    {
        if ($this->_categoryDefault == null) {
            $collection = $this->_categoriesCollection->create()->addIsActiveFilter()->setPageSize(1);
            $this->_categoryDefault = $collection->getFirstItem()->getId();
        }
        return [$this->_categoryDefault];
    }

    /**
     * Get all brand name
     *
     * @return array
     * @throws \Exception
     */
    public function getBrandName()
    {
        if (!count($this->_allBrandName)) {
            $this->_allBrandName = $this->brandResource->getAllBrandName();
        }
        return $this->_allBrandName;
    }
}
