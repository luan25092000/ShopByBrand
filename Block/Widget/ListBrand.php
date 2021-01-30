<?php
namespace Magenest\ShopByBrand\Block\Widget;

use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ListBrand extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/listbrand.phtml";


    /**
     * @var CollectionFactory
     */
    protected $_brandCollection;

    /**
     * ListBrand constructor.
     * @param Template\Context $context
     * @param CollectionFactory $brandCollection
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $brandCollection,
        array $data
    ) {
        $this->_brandCollection = $brandCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getBrands()
    {
        $number = $this->getData('number_of_brand');

        if ($number == 0) {
            return [];
        }


        $brandCollection = $this->_brandCollection->create()
            ->addFieldToSelect('*')
            ->setPageSize($number)
            ->addFieldToFilter('status', 1)
            ->getData();
        return $brandCollection;
    }

    public function getBaseImageUrl()
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        return $baseUrl . Router::ROUTER_MEDIA;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseBrandUrl()
    {
        $configUrl = $this->getUrlRewrite();
        return $this->_storeManager->getStore()->getBaseUrl() . $configUrl;
    }

    public function getUrlRewrite()
    {
        $value = $this->_scopeConfig->getValue('shopbybrand/page/url');
        return $value;
    }

    /**
     * @param $brand
     * @return string
     */
    public function getImage($brand)
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        if ($brand['logo'] != '') {
            $brand['logo'] = $baseUrl . Router::ROUTER_MEDIA . $brand['logo'];
        } else {
            $brand['logo'] = $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        }
        return $brand['logo'];
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        if (!$this->hasData('brand_sort_by')) {
            $this->setData('brand_sort_by', 'asc');
        }

        return $this->getData('brand_sort_by');
    }
}
