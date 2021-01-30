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

namespace Magenest\ShopByBrand\Helper;

use Magenest\ShopByBrand\Model\Brand as BrandModel;
use Magenest\ShopByBrand\Model\Config\Router;
use Magenest\ShopByBrand\Model\Theme\Upload;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Brand
 * @package Magenest\ShopByBrand\Helper
 */
class Brand extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TITLE_MEMU = 'shopbybrand/general/title_menu';
    const FEATURED_PRODUCT = 'shopbybrand/product/featured_product';
    const TITLE_FEATURED_PRODUCT = 'shopbybrand/product/title_product';
    const FEATURED_BRAND = 'shopbybrand/brandpage/featured_brand';
    const TITLE_FEATURED_BRAND = 'shopbybrand/brandpage/title_brand';
    const PRODUCT_DETAIL = 'shopbybrand/general/show';
    const ICON_IN_LIST_PRODUCT = 'shopbybrand/general/icon_brand';
    const BRAND_URL = 'shopbybrand/page/url';
    const BRAND_TITLE = 'shopbybrand/brandpage/title';
    const BRAND_KEYWORD = 'shopbybrand/page/keywords';
    const BRAND_DESCRIPTION = 'shopbybrand/page/description';
    const BRAND_BACKGROUND_COLOR = 'shopbybrand/brandpage/color_picker_brand';
    const BRAND_TITLE_COLOR = 'shopbybrand/brandpage/color_picker_font';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magenest\ShopByBrand\Model\Brand
     */
    protected $_brand;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var
     */
    protected $_uploadImage;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Brand constructor.
     * @param BrandModel $brand
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param Upload $_uploadImage
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Repository $repository
     * @param RequestInterface $request
     */
    public function __construct(
        BrandModel $brand,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        Upload $_uploadImage,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Repository $repository,
        RequestInterface $request
    ) {
        $this->_storeManager = $storeManager;
        $this->_brand = $brand;
        $this->_urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_uploadImage = $_uploadImage;
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return Brand
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getTitleMenu()
    {
        return $this->_scopeConfig->getValue(self::TITLE_MEMU);
    }

    /**
     * @return mixed
     */
    public function getFeaturedProduct()
    {
        return $this->_scopeConfig->getValue(self::FEATURED_PRODUCT);
    }

    /**
     * @return mixed
     */
    public function getBackgroundColorBrand()
    {
        return $this->_scopeConfig->getValue(self::BRAND_BACKGROUND_COLOR);
    }

    /**
     * @return mixed
     */
    public function getTitleColorBrand()
    {
        return $this->_scopeConfig->getValue(self::BRAND_TITLE_COLOR);
    }

    /**
     * @return mixed
     */
    public function getTitleFeaturedProduct()
    {
        return $this->_scopeConfig->getValue(self::TITLE_FEATURED_PRODUCT);
    }

    /**
     * @return mixed
     */
    public function getFeaturedBrand()
    {
        return $this->_scopeConfig->getValue(self::FEATURED_BRAND);
    }

    /**
     * @return mixed
     */
    public function getTitleFeaturedBrand()
    {
        return $this->_scopeConfig->getValue(self::TITLE_FEATURED_BRAND);
    }

    /**
     * @return mixed
     */
    public function isShowInProductDetail()
    {
        return $this->_scopeConfig->getValue(self::PRODUCT_DETAIL);
    }

    /**
     * @return mixed
     */
    public function getBrandUrl()
    {
        return $this->_scopeConfig->getValue(self::BRAND_URL);
    }

    /**
     * @return mixed
     */
    public function getBrandTitle()
    {
        return $this->_scopeConfig->getValue(self::BRAND_TITLE);
    }

    /**
     * @return mixed
     */
    public function getBrandKeyword()
    {
        return $this->_scopeConfig->getValue(self::BRAND_KEYWORD);
    }

    /**
     * @return mixed
     */
    public function getBrandDescription()
    {
        return $this->_scopeConfig->getValue(self::BRAND_DESCRIPTION);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return Brand
     */
    public function setProduct(Product $product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowBrand()
    {
        $mode = $this->isShowInProductDetail();
        if ($mode == 0) {
            return false;
        }

        $storeId = $this->_storeManager->getStore()->getId();
        $brand = $this->_brand->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreToFilter($storeId)
            ->addProductToFilter($this->_product->getId())
            ->getLastItem()
            ->getData();

        if (isset($brand['brand_id'])) {
            $this->data = $brand;
            return true;
        }

        return false;
    }

    /**
     * Get all store id
     *
     * @return array
     */
    public function getBrandAllStoreView()
    {
        $arrayStoreId = $this->getAllStoreId();
        $sumStore = count($arrayStoreId) - 1;
        $arrayBrand = [];
        $allBand = $this->getAllBrand();

        foreach ($allBand as $brand) {
            $dem = 0;
            foreach ($arrayStoreId as $storeId) {
                $temp = $this->_brand->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addStoreAllToFilter($storeId, $brand['brand_id'])
                    ->getData();
                if (count($temp)) {
                    $dem = $dem + 1;
                }
            }
            if ($dem == $sumStore) {
                $arrayBrand[] = $brand;
            }
        }

        return $arrayBrand;
    }

    /**
     * @return array
     */
    public function getAllBrand()
    {
        return $this->_brand->getCollection()->getData();
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getBrandCollection()
    {
        return $this->_brand->getCollection();
    }

    /**
     * @return array
     */
    public function getAllStoreId()
    {
        $allStoreId = [];

        $allStore = $this->_storeManager->getStores($withDefault = false);

        foreach ($allStore as $store) {
            $allStoreId[] = $store->getStoreId();
        }

        return $allStoreId;
    }

    /**
     * @return array
     */
    public function getBrand()
    {
        $configUrl = $this->getUrlRewrite();
        $data = [];
        if (isset($this->data)) {
            $logo = $this->data['logo'];
            $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
            if ($logo == "") {
                $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_STATIC]);
                $data['logo'] = $baseUrl . Router::ROUTER_STATIC;
            } else {
                $data['logo'] = $baseUrl . Router::ROUTER_MEDIA . $this->data['logo'];
            }
            $data['meta_description'] = $this->data['short_description_hover'];
            $data['name'] = $this->data['name'];
            $data['url'] = $configUrl . '/' . $this->data['url_key'];
        }

        return $data;
    }

    /**
     * Get Url Rewrite
     *
     * @return mixed
     */
    public function getUrlRewrite()
    {
        return $this->_scopeConfig->getValue(self::BRAND_URL);
    }

    /**
     * @return bool
     */
    public function isShowRelate()
    {
        $relate = $this->_product->getData('brand_related');
        if (isset($relate)) {
            if ($relate == 1) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function isShowStore()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $brand = $this->_brand->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreToFilter($storeId);
        return $brand;
    }

    /**
     * @return array
     */
    public function getIdRelateProduct()
    {
        $brand = $this->_brand->getCollection()
            ->addFieldToFilter('status', 1)
            ->addProductToFilter($this->_product->getId())
            ->getLastItem();
        $id = $brand->getData('brand_id');
        $collection = $this->_brand->getCollection()
            ->addBrandIdToFilter($id)
            ->getData();
        $data = [];
        foreach ($collection as $row) {
            $data[] = $row['product_id'];
        }

        return $data;
    }

    /**
     * @return Upload
     */
    public function getUploadImage()
    {
        return $this->_uploadImage;
    }

    /**
     * @param $str
     * @return mixed
     */
    public function convertVNtoEN($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        return $str;
    }

    /**
     * Check whether image is exists
     *
     * @param $imagePath
     * @return bool
     */
    public function isImageExists($imagePath)
    {
        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath($imagePath);
        return (bool) file_exists($absolutePath);
    }

    public function changeImagePathCollection($collection)
    {
        $params = ['_secure' => $this->request->isSecure()];
        foreach ($collection as $key => $brand) {
            if ($brand['logo'] && $this->isImageExists(Router::ROUTER_MEDIA . $brand['logo'])) {
                $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
                $collection[$key]['logo'] = $baseUrl . Router::ROUTER_MEDIA . $brand['logo'];
            } else {
                $imgDefaultPath = $this->repository->getUrlWithParams('Magento_Catalog::images/product/placeholder/thumbnail.jpg', $params);
                $collection[$key]['logo'] = $imgDefaultPath;
            }
        }
        return $collection;
    }
}
