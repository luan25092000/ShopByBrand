<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 10/12/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Controller\Brand;

use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Filter extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CollectionFactory
     */
    protected $_brandCollection;
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var Brand
     */
    protected $brandHelper;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Filter constructor.
     * @param Context $context
     * @param CollectionFactory $brandCollection
     * @param Brand $brandHelper
     * @param \Magento\Framework\UrlInterface $_urlBuilder
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $brandCollection,
        Brand $brandHelper,
        \Magento\Framework\UrlInterface $_urlBuilder,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_brandCollection = $brandCollection;
        $this->brandHelper = $brandHelper;
        parent::__construct($context);
    }
    public function execute()
    {
        $key = $this->getRequest()->getParam('key');
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_resultJsonFactory->create();
        $collection = $this->_brandCollection->create();
        $collection->addFieldToFilter('name', ['like'=>"%" . $key . "%"])->addFieldToFilter('status', 1);
        $newCollection = [];
        if ($collection->getData()) {
            $newCollection = $this->brandHelper->changeImagePathCollection($collection->getData());
        }
        return $resultJson->setData($newCollection);
    }
}
