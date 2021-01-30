<?php
namespace Magenest\ShopByBrand\Plugin;

class ConfiguredPricePlugin
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * ConfiguredPricePlugin constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
        $this->_registry = $registry;
    }
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        $routeName = $this->_request->getRouteName();
        $isCatalogProduct = $this->_registry->registry('current_product');
        $isCatalogCategory = $this->_registry->registry('current_category');
        if ($this->_scopeConfig->getValue('shopbybrand/general/icon_brand') && $routeName=='shopbybrand') {
            return ['Magenest_ShopByBrand::product/price/shopbybrand_configured_price.phtml'];
        } elseif ($this->_scopeConfig->getValue('shopbybrand/general/icon_brand') && $routeName=='catalog' && $isCatalogProduct) {
            return ['Magenest_ShopByBrand::product/price/catalog_configured_price.phtml'];
        } elseif ($this->_scopeConfig->getValue('shopbybrand/general/icon_brand') && $routeName=='catalog' && $isCatalogCategory) {
            return ['Magenest_ShopByBrand::product/price/shopbybrand_configured_price.phtml'];
        } else {
            return [$template];
        }
    }
}
