<?php

namespace Magenest\ShopByBrand\Observer\Product;

use Magenest\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;

class AfterImport implements ObserverInterface
{
    /**
     * @var BrandResource
     */
    protected $_brandResource;
    /**
     * @var RequestInterface
     */
    protected $_requestInterface;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var Json
     */
    protected $_json;
    /**
     * @var ProductResource
     */
    protected $_productResource;
    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * AfterImport constructor.
     * @param BrandResource $brandResource
     * @param RequestInterface $requestInterface
     * @param ProductRepository $productRepository
     * @param Json $json
     * @param ProductResource $productResource
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        BrandResource $brandResource,
        RequestInterface $requestInterface,
        ProductRepository $productRepository,
        Json $json,
        ProductResource $productResource,
        ResourceConnection $resourceConnection
    ) {
        $this->_productResource = $productResource;
        $this->_brandResource = $brandResource;
        $this->_requestInterface = $requestInterface;
        $this->_productRepository = $productRepository;
        $this->_json = $json;
        $this->_resourceConnection = $resourceConnection;
    }

    public function execute(Observer $observer)
    {
        $productEventCollections = $observer->getEvent()->getBunch();
        $shopBrandTable = $this->_resourceConnection->getTableName('magenest_shop_brand');
        $categoryTable = $this->_resourceConnection->getTableName('catalog_category_product');
        $brandProductTable = $this->_brandResource->getBrandProductTable();
        $behavior = $this->_requestInterface->getParam("behavior");
        $connection = $this->_brandResource->getConnection();
        if ($behavior == "append") {
            $brand_ids = [];
            foreach ($productEventCollections as $productEventCollection) {
                $productCurr = $this->_productRepository->get($productEventCollection["sku"]);
                if ($productCurr) {
                    $productId = $productCurr->getId();
                    if (isset($productEventCollection["brand_id"])) {
                        $brandName = $productEventCollection["brand_id"];
                        $sql = "SELECT `brand_id` FROM {$shopBrandTable} WHERE name = '{$brandName}'";
                        $brand_id = $connection->fetchOne($sql);
                        $brand_ids[] = $brand_id;
                        $sql = "SELECT count(`product_id`) FROM {$brandProductTable} WHERE product_id = {$productId}";
                        $result = $connection->fetchOne($sql);
                        //Update brand product
                        if ($result == 0) {
                            //Create
                            $sql = "INSERT INTO {$brandProductTable} (`brand_id`, `product_id`, `position`, `featured_product`) VALUES ({$brand_id}, {$productId}, 0, 0)";
                            $connection->query($sql);
                        } elseif ($result > 1) {
                            //Delete old
                            $sql = "DELETE FROM {$brandProductTable} WHERE product_id = {$productId}";
                            $connection->query($sql);
                            $sql = "INSERT INTO {$brandProductTable} (`brand_id`, `product_id`, `position`, `featured_product`) VALUES ({$brand_id}, {$productId}, 0, 0)";
                            $connection->query($sql);
                        } else {
                            //Update
                            $sql = "UPDATE {$brandProductTable} SET `position` = 0, `featured_product` = 0, `brand_id` = {$brand_id} WHERE `product_id` = {$productId} ";
                            $connection->query($sql);
                        }
                    }
                } else {
                    $productCurr->setData("brand_id", 0);
                    $this->_productResource->save($productCurr);
                    $sql = "DELETE FROM {$brandProductTable} WHERE product_id = {$productId}";
                    $connection->query($sql);
                }
            }
            $brand_ids = array_unique($brand_ids);
            foreach ($brand_ids as $brand_id_update) {
                //Update brand categories
                $sql = "SELECT `product_id` FROM {$brandProductTable} WHERE `brand_id` = {$brand_id_update}";
                $products = ($connection->fetchCol($sql));
                $products = implode(",", $products);
                $sql = "SELECT DISTINCT `category_id` FROM {$categoryTable} WHERE `product_id` IN ({$products})";
                $categoryMerge =  $this->_json->serialize($connection->fetchCol($sql));
                $sql = "UPDATE {$shopBrandTable} SET `categories` = '{$categoryMerge}' WHERE `brand_id` = {$brand_id_update} ";
                $connection->query($sql);
            }
        } elseif ($behavior == "replace") {
            $brand_ids = [];
            foreach ($productEventCollections as $productEventCollection) {
                $productCurr = $this->_productRepository->get($productEventCollection["sku"]);
                if ($productCurr) {
                    $productId = $productCurr->getId();
                    if (isset($productEventCollection["brand_id"])) {
                        $brandName = $productEventCollection["brand_id"];
                        $sql = "SELECT `brand_id` FROM {$shopBrandTable} WHERE name = '{$brandName}'";
                        $brand_id = $connection->fetchOne($sql);
                        $brand_ids[] = $brand_id;
                        //Delete old
                        $sql = "DELETE FROM {$brandProductTable} WHERE product_id = {$productId}";
                        $connection->query($sql);
                        $query = "INSERT INTO {$brandProductTable} (`brand_id`, `product_id`, `position`, `featured_product`) VALUES ({$brand_id}, {$productId}, 0, 0)";
                        $connection->query($query);
                    } else {
                        $productCurr->setData("brand_id", 0);
                        $this->_productResource->save($productCurr);
                        $sql = "DELETE FROM {$brandProductTable} WHERE product_id = {$productId}";
                        $connection->query($sql);
                    }
                }
            }
            $brand_ids = array_unique($brand_ids);
            foreach ($brand_ids as $brand_id_update) {
                //Update brand categories
                $sql = "SELECT `product_id` FROM {$brandProductTable} WHERE `brand_id` = {$brand_id_update}";
                $products = $connection->fetchCol($sql);
                $products = implode(",", $products);
                $sql = "SELECT DISTINCT `category_id` FROM {$categoryTable} WHERE `product_id` IN ({$products})";
                $categoryMerge =  $this->_json->serialize($connection->fetchCol($sql));

                $sql = "UPDATE {$shopBrandTable} SET `categories` = '{$categoryMerge}' WHERE `brand_id` = {$brand_id_update} ";
                $connection->query($sql);
            }
        }
    }
}
