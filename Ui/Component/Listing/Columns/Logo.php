<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 23/05/2016
 * Time: 00:51
 */

namespace Magenest\ShopByBrand\Ui\Component\Listing\Columns;

use Magenest\ShopByBrand\Helper\Brand;
use Magenest\ShopByBrand\Model\Config\Router;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Logo
 *
 * @package Magenest\ShopByBrand\Ui\Component\Listing\Columns
 */
class Logo extends Column
{
    /**
     * Url path
     */
    const BLOG_URL_PATH_EDIT = 'shopbybrand/brand/edit';
    const BLOG_URL_PATH_DELETE = 'shopbybrand/brand/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var string
     */
    private $editUrl;
    protected $repository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Brand
     */
    protected $brandHelper;

    /**
     * @param Brand $brandHelper
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storemanager
     * @param Repository $repository
     * @param Filesystem $filesystem
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        Brand $brandHelper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storemanager,
        Repository $repository,
        Filesystem $filesystem,
        array $components = [],
        array $data = [],
        $editUrl = self::BLOG_URL_PATH_EDIT
    ) {
        $this->brandHelper = $brandHelper;
        $this->_storeManager = $storemanager;
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->repository = $repository;
        $this->filesystem = $filesystem;
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
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $imgdefault = $this->repository->getUrl("Magento_Catalog::images/product/placeholder/thumbnail.jpg");

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $template = new \Magento\Framework\DataObject($item);
                $imageUrl = $mediaDirectory . Router::ROUTER_MEDIA . $template->getLogo();
                if ($template->getLogo() && $this->brandHelper->isImageExists(Router::ROUTER_MEDIA . $template->getLogo())) {
                    $item[$fieldName . '_src'] = $imageUrl;
                } else {
                    $item[$fieldName . '_src'] = $imgdefault;
                }

                $item[$fieldName . '_alt'] = $template->getName();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'shopbybrand/brand/edit',
                    [
                        'brand_id' => $item['brand_id'],
                        'store' => $this->context->getRequestParam('store'),
                    ]
                );
                $id = $template->getShopByBrandTemplateId();

                if ($template->getLogo()) {
                    $item[$fieldName . '_orig_src'] = $imageUrl;
                } else {
                    $item[$fieldName . '_orig_src'] = $imgdefault;
                }
            }
        }

        return $dataSource;
    }
}
