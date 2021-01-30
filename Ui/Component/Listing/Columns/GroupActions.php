<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 01/11/2016
 * Time: 16:50
 */

namespace Magenest\ShopByBrand\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class GroupActions
 *
 * @package Magenest\ShopBybrand\Ui\Component\Listing\Columns
 */
class GroupActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = array(),
        array $data = array()
    ) {
        $this->urlBuilder = $urlBuilder;
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
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = array(
                    'href' => $this->urlBuilder->getUrl(
                        'shopbybrand/group/edit',
                        array(
                            'group_id' => $item['group_id'],
                        )
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                );
            }
        }

        return $dataSource;
    }
}
