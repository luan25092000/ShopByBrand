<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\ShopByBrand\Model\Import;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Import Sample File Provider model.
 * This class support only *.csv.
 */
class SampleFileProvider
{
    /**
     * Associate an import entity to its module, e.g ['entity_name' => 'module_name']
     * @var array
     */
    private $samples;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $readFactory;

    /**
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     * @param array $samples
     */
    public function __construct(
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        array $samples = array()
    ) {
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->samples = $samples;
    }

    /**
     * Returns the Size for the given file associated to an Import entity
     *
     * @param string $entityName
     * @throws NoSuchEntityException
     * @return int|null
     */
    public function getSize(string $entityName)
    {
        $directoryRead = $this->getDirectoryRead();
        $filePath = $this->getPath($entityName);
        $fileSize = isset($directoryRead->stat($filePath)['size'])
            ? $directoryRead->stat($filePath)['size'] : null;

        return $fileSize;
    }

    /**
     * Returns Content for the given file associated to an Import entity
     *
     * @param string $entityName
     * @throws NoSuchEntityException
     * @return string
     */
    public function getFileContents(string $entityName)
    {
        $directoryRead = $this->getDirectoryRead();
        $filePath = $this->getPath($entityName);
        return $directoryRead->readFile($filePath);
    }

    /**
     * @param string $entityName
     * @return string
     * @throws NoSuchEntityException
     */
    private function getPath(string $entityName)
    {
        $moduleName = "Magenest_ShopByBrand";
        $directoryRead = $this->getDirectoryRead();
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $entityName . '.csv';

        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);

        if (!$directoryRead->isFile($filePath)) {
            throw new NoSuchEntityException(__("There is no file: %file", array('file' => $filePath)));
        }

        return $filePath;
    }

    private function getDirectoryRead()
    {
        $moduleName = "Magenest_ShopByBrand";
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $directoryRead = $this->readFactory->create($moduleDir);

        return $directoryRead;
    }
}
