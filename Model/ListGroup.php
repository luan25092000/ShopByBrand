<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 02/11/2016
 * Time: 11:28
 */
namespace Magenest\ShopByBrand\Model;

use Magenest\ShopByBrand\Model\ResourceModel\Group\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ListGroup
 *
 * @package Magenest\ShopByBrand\Model
 */

class ListGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $_groupCollection;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     *
     * @param CollectionFactory $groupCollection
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $groupCollection,
        LoggerInterface $logger
    ) {
        $this->_groupCollection = $groupCollection;
        $this->_logger=$logger;
    }

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $groups    = $this->_groupCollection->create()
            ->addFieldToFilter('status', '1');
        $listGroup = [];
        foreach ($groups as $group) {
            $listGroup[] = [
                'label' => $group->getName(),
                'value' => $group->getGroupId(),
            ];
        }

        return $listGroup;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($empty = false)
    {
        $options = $this->getAvailableTemplate();

        if ($empty) {
            array_unshift(
                $options,
                [
                    'value' => '0',
                    'label' => __('All Group Views'),
                ]
            );
        }

        return $options;
    }
}
