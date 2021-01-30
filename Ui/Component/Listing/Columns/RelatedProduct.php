<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ShopByBrand extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ShopByBrand
 */

namespace Magenest\ShopByBrand\Ui\Component\Listing\Columns;

use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RelatedProduct extends Column
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var Attribute
     */
    protected $_eavAttribute;

    /**
     * RelatedProduct constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductFactory $productFactory
     * @param Attribute $eavAttribute
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductFactory $productFactory,
        Attribute $eavAttribute,
        array $components = [],
        array $data = []
    ) {
        $this->_eavAttribute = $eavAttribute;
        $this->productFactory = $productFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $productId = $item['entity_id'];
                $productData = $this->productFactory->create()->load($productId);
                $brandId = $productData->getBrandId();
                if (isset($brandId)) {
                    $item[$this->getData('name')] = $brandId;
                }
            }
        }

        return $dataSource;
    }
}
