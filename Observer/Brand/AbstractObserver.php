<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 09/11/2016
 * Time: 09:39
 */
namespace Magenest\ShopByBrand\Observer\Brand;

/**
 * Class AbstractObserver
 *
 * @package Magenest\ShopByBrand\Observer\Brand
 */
class AbstractObserver
{
    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $_context;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $_brandFactory;

    /**
     * AbstractObserver constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_productFactory = $productFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_context = $context;
        $this->_logger = $context->getLogger();
        $this->_orderFactory = $orderFactory;
        $this->_brandFactory = $brandFactory;
    }
}
