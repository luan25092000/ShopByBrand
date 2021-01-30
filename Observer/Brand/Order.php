<?php

namespace Magenest\ShopByBrand\Observer\Brand;

use Magenest\ShopByBrand\Model\ResourceModel\Brand;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Order implements ObserverInterface
{
    /**
     * @var Brand
     */
    protected $_brandResource;
    /**
     * @var \Magenest\ShopByBrand\Model\BrandFactory
     */
    protected $_brandFactory;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * Order constructor.
     * @param \Magenest\ShopByBrand\Model\BrandFactory $brandFactory
     * @param Brand $brandResource
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magenest\ShopByBrand\Model\BrandFactory $brandFactory,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand $brandResource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_brandFactory = $brandFactory;
        $this->_brandResource = $brandResource;
        $this->_productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        $data = [];
        $brandModel = $this->_brandFactory->create();
        $orderData = $observer->getData("orders") != null ? $observer->getData("orders") : [$observer->getEvent()->getOrder()];
        foreach ($orderData as $item) {
            foreach ($item->getAllVisibleItems() as $orderItem) {
                $productId = $orderItem->getProductId();
                $price = $orderItem->getRowTotal();
                $productModel = $this->_productRepository->getById($productId);
                $brandModel->unsetData();
                $this->_brandResource->load($brandModel, $productModel->getBrandId());
                if ($brandModel->getId()) {
                    $data['order_total'] = $brandModel->getData('order_total')+$price;
                }
                $this->_brandResource->save($brandModel->addData($data));
            }
        }
    }
}
