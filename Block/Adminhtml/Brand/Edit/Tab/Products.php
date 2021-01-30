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

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab;

use Magenest\ShopByBrand\Helper\Brand;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Class Products
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab
 */
class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /** @var Brand $_brandHelper */
    protected $_brandHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Products constructor.
     *
     * @param Brand $brandHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magenest\ShopByBrand\Helper\Brand $brandHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_brandHelper = $brandHelper;
        $this->_productFactory = $productFactory;
        $this->_coreRegistry   = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magenest_brand_product');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->_coreRegistry->registry('shopbybrand');
    }

    /**
     * @return Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        if ($this->getBrand()->getId()) {
            $this->setDefaultFilter(['in_brand' => 1]);
        }
        $visibility = [Visibility::VISIBILITY_BOTH];
        $collection = $this->_productFactory->create()->getCollection();
        $brandProductTable = $collection->getTable('magenest_brand_product');
        $collection->addAttributeToFilter(
            'visibility',
            ['in',$visibility]
        )->addAttributeToFilter(
            'status',
            Status::STATUS_ENABLED
        )
            ->addAttributeToSelect(
                'name'
            )->addAttributeToSelect(
                'sku'
            )->addAttributeToSelect(
                'price'
            )->joinField(
                'position',
                $brandProductTable,
                'position',
                'product_id=entity_id',
                'brand_id=' . (int) $this->getRequest()->getParam('brand_id', 0),
                'left'
            )->joinField(
                'featured_product',
                $brandProductTable,
                'featured_product',
                'product_id=entity_id',
                null,
                'left'
            );

        $this->setCollection($collection);
        $id=$this->getBrand()->getId();
        if (isset($id)) {
            $listProductId=$this->getBrand()
                ->getResource()
                ->getListIdProductOut($id);
            if (!empty($listProductId)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin'=>[$listProductId]]);
            }
        } else {
            $listProductId=$this->getBrand()
                ->getResource()
                ->getListIdProduct();
            if (!empty($listProductId)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin'=>[$listProductId]]);
            }
        }

        return parent::_prepareCollection();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('shopbybrand/brand/grid', ['_current' => true]);
    }

    /**
     * @return Grid\Extended|void
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        if (!$this->getBrand()->getProductsReadonly()) {
            $this->addColumn(
                'in_brand',
                [
                    'type'             => 'checkbox',
                    'name'             => 'in_brand',
                    'values'           => $this->_getSelectedProducts(),
                    'index'            => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction',
                ]
            );
        }

        $this->addColumn(
            'entity_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'price',
            [
                'header'        => __('Price'),
                'type'          => 'currency',
                'currency_code' => (string) $this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index'         => 'price',
            ]
        );
        $this->addColumn(
            'featured_product',
            [
                'header'           => 'Featured Brand',
                'type'             => 'checkbox',
                'field_name'       => 'featured_product[]',
                'values'           => ['1'],
                'index'            => 'featured_product',
                'filter'     => false,
            ]
        );
        $this->addColumn(
            'position',
            [
                'header'   => __('Position'),
                'type'     => 'number',
                'index'    => 'position',
                'column_css_class' => 'position',
                'editable' => !$this->getBrand()->getProductsReadonly(),
            ]
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'catalog/product/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
    }

    /**
     * @param Column $column
     * @return $this|Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_brand') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            $products = $this->getBrand()->getProductsPosition();
            return array_keys($products);
        }

        return $products;
    }

    /**
     * @param string $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterToHtml($html)
    {
        $status = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setProduct($this->_getSelectedProducts())
            ->setTemplate("Magenest_ShopByBrand::tab/status.phtml")
            ->setJsObjectName($this->getJsObjectName())
            ->toHtml();
        return $status . $html;
    }

    /**
     * @return $this|Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'task action-secondary',
                ]
            )->setDataAttribute(
                [
                    'action' => 'grid-filter-apply'
                ]
            )
        );
        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return bool|string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Button::class
            )->setData(
                [
                    'label' => __('Add Product'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'task action-secondary'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getFilterVisibility()) {
            $html .= $this->getAddFilterButtonHtml();
            $html .= $this->getSearchButtonHtml();
            $html .= $this->getResetFilterButtonHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getAddFilterButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}
