<?php
/**
 * Created by PhpStorm.
 * User: luannguyen
 * Date: 12/11/2020
 * Author: luannh@magenest.com
 */

namespace Magenest\ShopByBrand\Block\Adminhtml\Renderer;

use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magenest\ShopByBrand\Helper\Brand
     */
    protected $brandHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $repository;

    /**
     * Image constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magenest\ShopByBrand\Helper\Brand $brandHelper
     * @param \Magento\Framework\View\Asset\Repository $repository
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\ShopByBrand\Helper\Brand $brandHelper,
        \Magento\Framework\View\Asset\Repository $repository,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->brandHelper = $brandHelper;
        $this->repository = $repository;
        parent::__construct($context, $data);
    }

    public function render(DataObject $row)
    {
        $image = $this->_getValue($row);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $srcImg = $mediaUrl . Router::ROUTER_MEDIA . $image;
        if (!$image || !$this->brandHelper->isImageExists(Router::ROUTER_MEDIA . $image)) {
            $srcImg = $this->repository->getUrl("Magento_Catalog::images/product/placeholder/thumbnail.jpg");
        }
        return '<img class="admin__control-thumbnail image-banner-thumbnail" src="' . $srcImg . '" />';
    }
}
