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

namespace Magenest\ShopByBrand\Controller\Brand;

use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory;

/**
 * Class Index
 * @package Magenest\ShopByBrand\Controller\Brand
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Brand
     */
    protected $helper;

    /**
     * @var CollectionFactory
     */
    protected $collectionBrand;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Brand $helper,
        CollectionFactory $collectionBrand,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->collectionBrand = $collectionBrand;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $totalBrand = $this->collectionBrand->create()->addFieldToFilter('status', 1)->load()->count();
        if ($totalBrand == 0) {
            $this->messageManager->addErrorMessage('No brand is not activated!');
            return $this->_redirect($this->helper->getBaseUrl());
        }

        return $resultPage;
    }
}
