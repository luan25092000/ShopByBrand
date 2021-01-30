<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 01/11/2016
 * Time: 16:20
 */
namespace Magenest\ShopByBrand\Model;

use Magenest\ShopByBrand\Model\ResourceModel\Group as Resource;
use Magenest\ShopByBrand\Model\ResourceModel\Group\Collection;

class Group extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'group_brand';
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Resource $resource,
        Collection $resourceCollection,
        array $data = array()
    ) {
    

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('Magenest\ShopByBrand\Model\ResourceModel\Group');
        $this->setIdFieldName('group_id');
    }
}
