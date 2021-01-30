<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 09/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab;

use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\Status;
use Magento\Backend\Block\Widget\Grid;

/**
 * Class RelatedBrand
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab
 */
class RelatedBrand extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magenest\ShopByBrand\Model\Brand\Status
     */
    protected $status;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magenest\ShopByBrand\Model\Brand\Feature
     */
    protected $feature;
    /**
     * RelatedBrand constructor.
     *
     * @param Brand $brandHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $collectionFactory
     * @param \Magenest\ShopByBrand\Model\Brand\Status $status
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\ShopByBrand\Model\Brand\Feature $feature
     * @param array $data
     */
    public function __construct(
        \Magenest\ShopByBrand\Helper\Brand $brandHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $collectionFactory,
        \Magenest\ShopByBrand\Model\Brand\Status $status,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\ShopByBrand\Model\Brand\Feature $feature,
        array $data = []
    ) {
        $this->_brandHelper = $brandHelper;
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->status = $status;
        $this->json = $json;
        $this->logger = $logger;
        $this->feature = $feature;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('magenest_shop_brand');
        $this->setDefaultSort('brand_id');
        $this->setUseAjax(true);
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->_coreRegistry->registry('relatedbrand');
    }

    /**
     * @return Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $brandId = $this->getRequest()->getParam('brand_id');
        $collection = $this->collectionFactory->create()->addFieldToFilter('status', Status::STATUS_ENABLED);
        if ($brandId) {
            $collection->addFieldToFilter('brand_id', ['neq' => $brandId]);
        }
        $this->setCollection($collection);
        $currentBrandId = $this->getBrand()->getId();
        $resetFilter = $this->getRequest()->getParam('filter');
        if (isset($currentBrandId) && !isset($resetFilter)) {
            $listBrandId = [];
            try {
                $listBrandId = $this->json->unserialize($this->getBrand()->getRelatedBrand());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
            if (!empty($listBrandId) && is_array($listBrandId)) {
                $this->getCollection()->addFieldToFilter('brand_id', ['in' => $listBrandId]);
            }
        }

        return parent::_prepareCollection();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('shopbybrand/brand/related', ['_current' => true]);
    }

    /**
     * @return Grid\Extended|void
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_brand',
            [
                'type' => 'checkbox',
                'name' => 'in_brand',
                'values' => $this->_getSelectedBrand(),
                'index' => 'brand_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
            ]
        );
        $this->addColumn(
            'logo',
            [
                'header' => __('Logo'),
                'index' => 'logo',
                'renderer' => 'Magenest\ShopByBrand\Block\Adminhtml\Renderer\Image',
                'column_css_class' => "data-grid-thumbnail-cell",
                'filter'=> false
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'brand_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'filter' => false
            ]
        );
        $this->addColumn(
            'featured',
            [
                'header' => 'Featured Brand',
                'type' => 'options',
                'index' => 'featured',
                'options' => $this->feature->getOptionArray(),
                'filter' => false
            ]
        );
        $this->addColumn(
            'summary',
            [
                'header' => 'Total Product',
                'type' => 'text',
                'index' => 'summary'
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
                            'base' => 'shopbybrand/brand/edit'
                        ],
                        'field' => 'brand_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @param Grid\Column $column
     * @return $this|RelatedBrand
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_brand') {
            $brandIds = $this->_getSelectedBrand();
            if (empty($brandIds)) {
                $brandIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('brand_id', ['in' => $brandIds]);
            } elseif (!empty($brandIds)) {
                $this->getCollection()->addFieldToFilter('brand_id', ['nin' => $brandIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
    /**
     * @return array
     */
    protected function _getSelectedBrand()
    {
        $selectedBrand = $this->getRequest()->getPost('selected_brand');
        if ($selectedBrand === null) {
            try {
                $selectedBrand = $this->json->unserialize($this->getBrand()->getRelatedBrand());
                return array_keys($selectedBrand);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
        return $selectedBrand;
    }

    /**
     * @param string $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterToHtml($html)
    {
        $status = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setProduct($this->_getSelectedBrand())
            ->setTemplate("Magenest_ShopByBrand::tab/status.phtml")
            ->setJsObjectName($this->getJsObjectName())
            ->toHtml();
        return $status . $html;
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
                    'label' => __('Add Brand'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'task action-secondary'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
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
