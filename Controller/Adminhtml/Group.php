<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 01/11/2016
 * Time: 16:31
 */
namespace Magenest\ShopByBrand\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\ShopByBrand\Model\GroupFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Group
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml
 */
abstract class Group extends Action
{
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
     * @var \Magento\UrlRewrite\Model\UrlRewrite
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
     * @var GroupFactory
     */
    protected $_groupBrandFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_storeManage;
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;
    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $brandFactory;

    /**
     * Group constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param GroupFactory $groupFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param Filter $filter
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        GroupFactory $groupFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        Filter $filter,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
    ) {
        $this->_storeManage       = $storeManager;
        $this->_categoryFactory   = $categoryFactory;
        $this->_productFactory    = $productFactory;
        $this->_context           = $context;
        $this->_coreRegistry      = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->resultRawFactory   = $resultRawFactory;
        $this->layoutFactory      = $layoutFactory;
        $this->_groupBrandFactory      = $groupFactory;
        $this->_filter            = $filter;
        $this->_urlRewrite        = $urlRewrite;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_cacheFrontendPool   = $cacheFrontendPool;
        $this->brandFactory = $brandFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_ShopByBrand::group')
            ->addBreadcrumb(__('Manage Group'), __('Manage Group'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ShopByBrand::brand');
    }
}
