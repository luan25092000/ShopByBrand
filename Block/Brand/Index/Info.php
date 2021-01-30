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

namespace Magenest\ShopByBrand\Block\Brand\Index;

use Magenest\ShopByBrand\Helper\Brand as Helper;
use Magenest\ShopByBrand\Model\Config\Router;

/**
 * Class Info
 * @package Magenest\ShopByBrand\Block\Brand\Index
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magenest\ShopByBrand\Model\GroupFactory
     */
    protected $_group;

    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollection;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * SideBar constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magenest\ShopByBrand\Model\GroupFactory $groupFactory
     * @param \Magenest\ShopByBrand\Model\Brand $brandFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\ShopByBrand\Model\GroupFactory $groupFactory,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        Helper $helper,
        array $data = []
    ) {
        $this->_brandCollection = $brandCollection;
        $this->_group = $groupFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getGroupInfo()
    {
        return $this->_group->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->setOrder('name', 'ASC')
            ->getData();
    }

    /**
     * @param $groupId
     * @return int|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQtyBrand($groupId)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $collection = $this->_brandCollection->create()
            ->setOrder('name', 'ASC')
            ->addStoreToFilter($storeId)
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('groups', [['finset' => $groupId], ['finset' => '0']])
            ->getData();
        return count($collection);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseGroupUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . Router::ROUTER_GROUP;
    }

    /**
     * @return mixed
     */
    public function getTitleBrandPage()
    {
        return $this->helper->getBrandTitle();
    }
}
