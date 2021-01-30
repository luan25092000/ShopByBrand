<?php
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

namespace Magenest\ShopByBrand\Controller\Adminhtml;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\BrandFactory;
use Magenest\ShopByBrand\Model\ResourceModel\BrandProduct\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
/**
 * Class Brand
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml
 */
abstract class Brand extends Action
{
    const FIELD_NAME_SOURCE_FILE = 'import_file';

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_urlRewrite;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scoreConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagement;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;
    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var BrandFactory
     */
    protected $_brandFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoriesCollection;

    /**
     * @var \DOMDocument
     */
    protected $domDocument;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand
     */
    protected $_brandResource;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollection;
    /**
     * @var Json
     */
    protected $_json;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory
     */
    protected $_urlRewriteFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollection;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $_productResource;
    /**
     * @var DirectoryList
     */
    protected $_directoryList;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;
    protected $_brandProductCollection;
    protected $_groupBrandCollection;

    /**
     * Brand constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param BrandFactory $brandFactory
     * @param Filter $filter
     * @param Helper $helper
     * @param Json $json
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $configInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagement
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $sourceCsvFactory
     * @param \Magento\ImportExport\Model\Export\Adapter\CsvFactory $outputCsvFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param \Magento\Framework\DomDocument\DomDocumentFactory $documentFactory
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory $urlRewriteFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param CollectionFactory $brandProductCollection
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory $groupBrandCollection
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        BrandFactory $brandFactory,
        Filter $filter,
        Helper $helper,
        Json $json,
        LoggerInterface $logger,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $configInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $sourceCsvFactory,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $outputCsvFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        \Magento\Framework\DomDocument\DomDocumentFactory $documentFactory,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteFactory $urlRewriteFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        CollectionFactory $brandProductCollection,
        \Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory $groupBrandCollection
    ) {
        $this->_storeManagement = $storeManagement;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_context = $context;
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_brandFactory = $brandFactory;
        $this->_filter = $filter;
        $this->_urlRewrite = $urlRewrite;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_scoreConfig = $configInterface;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->sourceCsvFactory = $sourceCsvFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->outputCsvFactory = $outputCsvFactory;
        $this->filesystem = $filesystem;
        $this->_logger = $logger;
        $this->varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->categoriesCollection = $categoriesCollection;
        $this->domDocument = $documentFactory->create();
        $this->helper = $helper;
        $this->productAction = $productAction;
        $this->_brandResource = $brandResource;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->_brandCollection = $brandCollection;
        $this->_json = $json;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_productCollection = $productCollection;
        $this->_productResource = $productResource;
        $this->_directoryList = $directoryList;
        $this->file = $file;
        $this->_brandProductCollection = $brandProductCollection;
        $this->_groupBrandCollection = $groupBrandCollection;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ShopByBrand::brand');
    }

    public function getCategoryDefault()
    {
        $collection = $this->categoriesCollection->create()->addIsActiveFilter()->getFirstItem();
        return $collection->getId();
    }
}
