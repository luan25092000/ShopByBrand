<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 26/10/2016
 * Time: 09:00
 */

namespace Magenest\ShopByBrand\Block\Featured\View;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\UrlInterface;

/**
 * Class Listing
 * @package Magenest\ShopByBrand\Block\Featured\View
 */
class Listing extends \Magento\Framework\View\Element\Template
{

    /** @var \Magenest\ShopByBrand\Model\Brand $brand */
    protected $brand;

    /** @var Router $router */
    protected $router;

    protected $_product = null;

    /** @var ProductRepositoryInterface $_productRepository */
    protected $_productRepository;

    /** @var AbstractProduct $_absProduct */
    protected $_absProduct;

    /** @var Helper  */
    protected $helper;

    /**
     * Listing constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\ShopByBrand\Model\Brand $brand
     * @param Router $router
     * @param AbstractProduct $abstractProduct
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\ShopByBrand\Model\Brand $brand,
        \Magenest\ShopByBrand\Model\Config\Router $router,
        AbstractProduct $abstractProduct,
        Helper $helper,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->_absProduct = $abstractProduct;
        $this->brand = $brand;
        $this->router = $router;
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->prepareTemplate();
    }

    public function prepareTemplate()
    {
        $sidebar = $this->getFeaturedConfig();
        if (strpos($sidebar, '1') !== false) {
            $this->setTemplate('Magenest_ShopByBrand::featured/view/carousel.phtml');
        }
    }

    /**
     * @return UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * @return mixed
     */
    public function getFeaturedConfig()
    {
        return $this->helper->getFeaturedProduct();
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListId()
    {
        $visibility = [Visibility::VISIBILITY_BOTH];
        $id = $this->getRequest()->getParam('brand_id');
        $collection = $this->brand->getCollection()
            ->addProductFeatured($id)
            ->getData();

        $data = [];
        foreach ($collection as $row) {
            $product_id = $row['product_id'];
            $product = $this->getProduct($product_id);
            $product_visibility = $product->getVisibility();
            if (in_array($product_visibility, $visibility)) {
                $data[] = $row['product_id'];
            }
        }

        return $data;
    }

    public function getItems($productId)
    {
        return $this->getProduct($productId);
    }

    /**
     * @param $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($productId)
    {
        $product = $this->_productRepository->getById($productId);
        return $product;
    }

    /**
     * @param $product
     * @param $param
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($product, $param)
    {
        $img = $this->_absProduct->getImage($product, $param);
        return $img;
    }

    /**
     * @param $productId
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getNameProduct($productId)
    {
        $productName = '';
        $product = $this->getProduct($productId);
        if ($product->getId()) {
            $productName = $product->getName();
        }
        return $productName;
    }

    /**
     * @param $productId
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceProduct($productId)
    {
        $priceProduct = 0;
        $product = $this->getProduct($productId);
        if ($product->getId()) {
            $priceProduct = $product->getFinalPrice();
        }
        return $priceProduct;
    }

    /**
     * @return mixed
     */
    public function getTitleProduct()
    {
        return $this->helper->getTitleFeaturedProduct();
    }
}
