<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 01/11/2016
 * Time: 16:22
 */

namespace Magenest\ShopByBrand\Model\ResourceModel\Group;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'group_id';

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\ShopByBrand\Model\Group', 'Magenest\ShopByBrand\Model\ResourceModel\Group');
    }

    public function addProductToFilter($product_id)
    {
        $this->addFieldToFilter('product_id', $product_id);
        return $this;
    }

    /**
     * @param $id
     * @param $url
     * @return $this
     */
    public function addUrlFilter($id, $url)
    {
        if ($id==null) {
            $this->addFieldToFilter('url_key', $url);
            return $this;
        } else {
            $this->addFieldToFilter('group_id', array('nin'=>$id));
            $this->addFieldToFilter('url_key', $url);
            return $this;
        }
    }

}
