<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 23/05/2016
 * Time: 01:17
 */

namespace Magenest\ShopByBrand\Model\Theme;

use Magenest\ShopByBrand\Model\Config\Router;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\UrlInterface;

/**
 * Class Upload
 *
 * @package Magenest\ShopByBrand\Model\Theme
 */
class Upload
{

    /**
     * media sub folder
     *
     * @var string
     */
    protected $subDir = 'shopbybrand/brand';

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * Upload constructor.
     * @param UploaderFactory $uploaderFactory
     * @param UrlInterface $urlBuilder
     * @param Filesystem $filesystem
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        UrlInterface $urlBuilder,
        Filesystem $filesystem
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param $input
     * @param $destinationFolder
     * @param $data
     * @return string
     */
    public function uploadFileAndGetName($input, $destinationFolder, $data)
    {
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = $this->uploaderFactory->create(array('fileId' => $input));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));

                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function getUploadFile($input)
    {
        return $uploadFile = $this->uploaderFactory->create(array('fileId' => $input));
    }

    /**
     * @param $file
     */
    public function deleteFile($file)
    {
        $path = $this->fileSystem->getDirectoryRead(
            DirectoryList::MEDIA
        );
        if ($path->isFile(Router::ROUTER_MEDIA .'/'. $file)) {
            $this->fileSystem->getDirectoryWrite(
                DirectoryList::MEDIA
            )->delete(Router::ROUTER_MEDIA. '/' . $file);
        }
    }

    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseImageUrl()
    {
        return $this->urlBuilder->getBaseUrl(array('_type' => UrlInterface::URL_TYPE_MEDIA)).$this->subDir.'/image/';
    }

    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseImageDir()
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->subDir.'/image');
    }
}
