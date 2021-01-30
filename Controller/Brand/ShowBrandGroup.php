<?php

namespace Magenest\ShopByBrand\Controller\Brand;

use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ShowBrandGroup
 * @package Magenest\ShopByBrand\Controller\Brand
 */
class ShowBrandGroup extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magenest\ShopByBrand\Model\Brand
     */
    protected $brand;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Brand
     */
    protected $helper;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * ShowBrandGroup constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\ShopByBrand\Model\Brand $group
     * @param StoreManagerInterface $storeManagerInterface
     * @param Brand $helper
     * @param Repository $repository
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\ShopByBrand\Model\Brand $group,
        StoreManagerInterface $storeManagerInterface,
        Brand $helper,
        Repository $repository,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->brand = $group;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManagerInterface;
        $this->helper = $helper;
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $groupId = $this->getRequest()->getParam('group_id');
        $storeId = $this->_storeManager->getStore()->getId();
        $collectionBrand = $this->brand->getCollection()
            ->setOrder('name', 'ASC')
            ->addStoreToFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('groups', [['finset' => $groupId], ['finset' => '0']])
            ->getData();

        $arrayBrand = [];

        foreach ($collectionBrand as $brand) {
            $imageUrl = $this->getLogoUrl($brand['logo']);
            $arrayBrand[] = [
                "name" => $brand['name'],
                "url_key" => $brand['url_key'],
                "summary" => $brand['summary'],
                "image_url" => $imageUrl,
                "nameUS" => $this->helper->convertVNtoEN($brand['name'])
            ];
        }

        return $this->resultJsonFactory->create()->setData(
            [
                'message' => $arrayBrand
            ]
        );
    }

    /**
     * @param $logo
     * @return string
     */
    public function getLogoUrl($logo)
    {
        $params = ['_secure' => $this->request->isSecure()];
        $baseUrl = $this->_url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        if ($logo != '' && $this->helper->isImageExists(Router::ROUTER_MEDIA . $logo)) {
            $logo = $baseUrl . Router::ROUTER_MEDIA . $logo;
        } else {
            $logo = $this->repository->getUrlWithParams('Magento_Catalog::images/product/placeholder/thumbnail.jpg', $params);
        }
        return $logo;
    }
}
