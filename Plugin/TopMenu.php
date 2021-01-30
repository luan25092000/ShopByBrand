<?php

namespace Magenest\ShopByBrand\Plugin;

use Magenest\ShopByBrand\Helper\Brand as BrandHelper;
use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

/**
 * Class TopMenu
 *
 * @package Magenest\ShopByBrand\Plugin
 */
class TopMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_template = "Magenest_ShopByBrand::menu/menu.phtml";

    /**
     * @var \Magenest\ShopByBrand\Model\Brand
     */
    protected $brand;

    /**
     * @var string
     */
    protected $html = "";

    /**
     * @var BrandHelper
     */
    protected $_brandHelper;
    /**
     * @var CollectionFactory
     */
    protected $_brandCollection;

    /**
     * TopMenu constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\ShopByBrand\Model\Brand $brand
     * @param BrandHelper $brandHelper
     * @param CollectionFactory $brandCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\ShopByBrand\Model\Brand $brand,
        BrandHelper $brandHelper,
        CollectionFactory $brandCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->brand = $brand;
        $this->_brandHelper = $brandHelper;
        $this->_brandCollection = $brandCollection;
        $this->prepareHtml();
    }

    /**
     * @return mixed
     */

    public function getCollection()
    {
        return $this->_brandCollection->create()->addFieldToFilter('status', 1);
    }

    public function getCurrentStore()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Prepare Html
     */
    public function prepareHtml()
    {
        $show = $this->_scopeConfig->getValue('shopbybrand/general/menu_link');
        if ($show == '1') {
            $this->html = $this->_toHtml();
        } else {
            $this->html = '';
        }
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetHtml($subject, $result)
    {
        return $result . $this->html;
    }

    /**
     * @return string
     */
    public function getBrandUrl()
    {
        $configUrl = $this->_scopeConfig->getValue('shopbybrand/page/url');
        return $this->getBaseUrl() . $configUrl;
    }

    public function getTitleMenu()
    {
        return $this->_scopeConfig->getValue('shopbybrand/general/title_menu');
    }
    public function isShowBrand()
    {
        $data = $this->_brandHelper->isShowStore();
        return $data;
    }
}
