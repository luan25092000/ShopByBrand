<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ShopByBrand\Controller\Adminhtml\Brand;

use Magenest\ShopByBrand\Model\BrandFactory;
use Magenest\ShopByBrand\Model\ResourceModel\Brand as BrandResource;
use Magenest\ShopByBrand\Model\Theme\Upload;
use Magento\Backend\App\Action;

/**
 * Class UploadLogoWhite
 * @package Magenest\ShopByBrand\Controller\Adminhtml\Brand
 */
class UploadLogoWhite extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @var BrandResource
     */
    protected $_brandResource;
    /**
     * @var BrandFactory
     */
    protected $_brandFactory;
    /**
     * @var Upload
     */
    protected $_upload;


    /**
     * UploadLogoWhite constructor.
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param BrandResource $brandResource
     * @param BrandFactory $brandFactory
     * @param Upload $upload
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        BrandResource $brandResource,
        BrandFactory $brandFactory,
        Upload $upload,
        Action\Context $context
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_filesystem = $filesystem;
        $this->_brandFactory = $brandFactory;
        $this->_brandResource = $brandResource;
        $this->_upload = $upload;
        parent::__construct($context);
    }

    public function execute()
    {
        $brandResource = $this->_brandResource;
        if ($this->getRequest()->getParam('id')) {
            $id = $this->getRequest()->getParam('id');
            $brand = $this->_brandFactory->create();
            $brandResource->load($brand, $id);
            if ($brand->getLogoBrandDetail()) {
                $this->_upload->deleteFile($brand->getLogoBrandDetail());
            }

            $brand->setLogoBrandDetail('');
            $brandResource->save($brand);
            return;
        }
    }
}
