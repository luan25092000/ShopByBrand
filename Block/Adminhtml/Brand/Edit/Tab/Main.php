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

use Magenest\ShopByBrand\Model\ListGroup;
use Magenest\ShopByBrand\Model\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as FormGeneric;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Main
 * @package Magenest\ShopByBrand\Block\Adminhtml\Brand\Edit\Tab
 */
class Main extends FormGeneric
{
    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var SystemStore
     */
    protected $_systemStore;

    /**
     * @var ListGroup
     */
    protected $_listGroup;

    /**
     * Main constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Status $status
     * @param SystemStore $systemStore
     * @param ListGroup $listGroup
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        SystemStore $systemStore,
        ListGroup $listGroup,
        array $data = []
    ) {
        $this->_listGroup = $listGroup;
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return FormGeneric
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('shopbybrand');
        $data = $model->getData();

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

        if ($model->getId()) {
            $data['brand_name'] = $data['name'];
            $fieldset->addField(
                'brand_id',
                'hidden',
                ['name' => 'brand_id']
            );
        }

        $fieldset->addField(
            'brand_name',
            'text',
            [
                'name' => 'brand_name',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => $this->_status->getOptionArray(),
            ]
        );
        $fieldset->addField(
            'featured',
            'select',
            [
                'label' => __('Add to Featured Brands'),
                'title' => __('Add to Featured Brands'),
                'name' => 'featured',
                'required' => false,
                'options' => $this->_status->getOptionArray(),
            ]
        );
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name' => 'store_ids[]',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $fieldset->addField(
            'groups',
            'multiselect',
            [
                'name' => 'groups[]',
                'label' => __('Groups'),
                'title' => __('Groups'),
                'required' => false,
                'values' => $this->_listGroup->getAllOptions(true),
            ]
        );
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
