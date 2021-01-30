<?php
/**
 * Created by PhpStorm.
 * User: thien
 * Date: 21/09/2017
 * Time: 09:14
 */

namespace Magenest\ShopByBrand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassExport
 *
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class MassExport extends Action
{
    const STATUS_ENABLE = 'Enable';
    const STATUS_DISABLE = 'Disable';
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_filesystem;
    /**
     * @var \DOMDocument
     */
    protected $domDocument;
    /**
     * @var \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $_brandCollection;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * MassExport constructor.
     * @param Action\Context $context
     * @param Filter $filter
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $filesystem
     * @param \Magento\Framework\DomDocument\DomDocumentFactory $documentFactory
     * @param \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $filesystem,
        \Magento\Framework\DomDocument\DomDocumentFactory $documentFactory,
        \Magenest\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->_filter = $filter;
        $this->_directoryList = $directoryList;
        $this->_filesystem = $filesystem;
        $this->domDocument = $documentFactory->create();
        $this->_brandCollection = $brandCollection;
        $this->json = $json;
        parent::__construct($context);
    }

    public function execute()
    {
        $path = $this->_directoryList->getPath('var') . "/importexport";
        if (!is_dir($path)) {
            $this->_filesystem->mkdir($path, 0775);
        }
        $brandCollection = $this->_brandCollection->create();
        $collections = $this->_filter->getCollection($brandCollection);
        $status = (int)$this->getRequest()->getParam('status');
        if ($status) {
            $this->exportXML($collections, $path);
        } else {
            $this->exportCSV($collections, $path);
        }
    }

    /**
     * @param $data
     * @param $path
     * @throws \Exception
     */
    private function exportCSV($data, $path)
    {
        $values = [];
        $row = [];
        $heading = [
            'store',
            'products',
            'name',
            'url_key',
            'description',
            'logo',
            'banner',
            'page_title',
            'meta_keywords',
            'meta_description',
            'groups',
            'status',
            'featured',
            'logo_brand_detail',
            'short_description_hover',
            'related_brand'
        ];
        $outputFile = $path . "/magenest_shopbybrand_import.csv";
        $handle = fopen($outputFile, 'w');
        fputcsv($handle, $heading);
        foreach ($data as $brand) {
            foreach ($brand->getData() as $key => $value) {
                if ($key == 'brand_id') {
                    $values[] = $this->getStore($value);
                    $values[] = $this->getListProduct($value);
                } elseif ($key == 'featured' || $key == 'status') {
                    $values[] = $value;
                } elseif (in_array($key, ['created_at','updated_at','order_total','slogan','summary','categories'])) {
                    continue;
                } elseif ($key == 'url_key') {
                    $values[] = str_replace(substr($value, -5), "", $value);
                } elseif ($key == 'related_brand') {
                    $values[] = $this->formatJsonToString($value);
                } else {
                    $values[] = $value;
                }
            }
            $row[] = $values;
            $values = [];
        }
        foreach ($row as $item) {
            fputcsv($handle, $item);
        }
        try {
            $this->downloadCsv($outputFile);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function exportXML($data, $path)
    {
        $xml = $this->domDocument;
        $this->domDocument = new $xml('1.0', 'UTF-8');

        $head = $this->domDocument->createElement('root');
        $head = $this->domDocument->appendChild($head);

        foreach ($data as $brand) {
            $row = $this->domDocument->createElement('row');
            $row = $head->appendChild($row);
            foreach ($brand->getData() as $key => $value) {
                if ($key == 'brand_id') {
                    $store = $this->domDocument->createElement('store', $this->getStore($value));
                    $products = $this->domDocument->createElement('products', $this->getListProduct($value));
                    $row->appendChild($store);
                    $row->appendChild($products);
                } elseif ($key == 'featured' || $key == 'status') {
                    $status = $this->domDocument->createElement($key, $value);
                    $row->appendChild($status);
                } elseif (in_array($key, ['created_at','updated_at','order_total','slogan','summary','categories'])) {
                    continue;
                } elseif ($key == 'url_key') {
                    $url = $this->domDocument->createElement($key, str_replace(substr($value, -5), "", $value));
                    $row->appendChild($url);
                } elseif ($key == 'related_brand') {
                    $related = $this->domDocument->createElement($key, $this->formatJsonToString($value));
                    $row->appendChild($related);
                } else {
                    $dom = $this->domDocument->createElement($key, $value);
                    $row->appendChild($dom);
                }
            }
        }

        $this->domDocument->save($path . "/magenest_shopbybrand_import.xml");
        $outputFile = $path . "/magenest_shopbybrand_import.xml";
        try {
            $this->downloadCsv($outputFile);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $idBrand
     * @return string
     */
    public function getStore($idBrand)
    {
        $collection = $this->_brandCollection->create()
            ->addBrandStore($idBrand)
            ->getData();
        $data = [];
        foreach ($collection as $row) {
            $data[] = $row['store_id'];
        }

        return implode(",", $data);
    }

    /**
     * @param $idBrand
     * @return string
     */
    public function getListProduct($idBrand)
    {
        $collection = $this->_brandCollection->create()
            ->addBrandIdToFilter($idBrand)
            ->getData();
        $data = [];
        foreach ($collection as $row) {
            $data[] = $row['product_id'];
        }

        return implode(",", $data);
    }

    /**
     * @param $file
     */
    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            //set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
        }
    }

    /**
     * Format json to string
     *
     * @param $value
     * @return string
     */
    public function formatJsonToString($value)
    {
        $result = '';
        if ($value) {
            $result = implode(',', $this->json->unserialize($value));
        }
        return $result;
    }
}
