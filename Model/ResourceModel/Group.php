<?php
namespace Magenest\ShopByBrand\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Group
 * @package Magenest\ShopByBrand\Model\ResourceModel
 */
class Group extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_brand_group', 'group_id');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getGroupIds()
    {
        try {
            return $this->getConnection()->fetchCol(
                $this->getConnection()
                    ->select()
                    ->from($this->getMainTable(), ['group_id'])
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
