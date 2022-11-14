<?php
/**
 * Alpine_Maintenance
 *
 * @copyright   Copyright (c) 2020 Alpine Consulting, Inc (www.alpineinc.com)
 * @author      Michal Zymela (mzymela@alpineinc.com)
 */

namespace Alpine\Maintenance\Error;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File as FileReader;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Filesystem\Driver\File;

class Maintenance
{
    /** @var int  */
    const RESPONSE_CODE = 503;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var FileReader
     */
    private $fileReader;
    /**
     * @var StoreManager
     */
    private $storeManager;
    /**
     * @var File
     */
    private $fileDriver;

    /**
     * Maintenance constructor.
     * @param Filesystem $filesystem
     * @param FileReader $fileReader
     * @param StoreManager $storeManager
     * @param File $fileDriver
     */
    public function __construct(
        Filesystem $filesystem,
        FileReader $fileReader,
        StoreManager $storeManager,
        File $fileDriver
    ) {
        $this->filesystem = $filesystem;
        $this->fileReader = $fileReader;
        $objectManagerFactory = Bootstrap::createObjectManagerFactory(BP, $_SERVER);
        $this->objectManager = $objectManagerFactory->create($_SERVER);
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Render maintenance page
     */
    public function renderPage()
    {
        $response = $this->objectManager->create(Http::class);
        $response->setHttpResponseCode(self::RESPONSE_CODE);
        $response->setBody(
            $this->setTemplate()
        );

        $response->sendResponse();
    }

    /**
     * @return bool|Phrase|string
     */
    private function setTemplate()
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::PUB)->getAbsolutePath() . "maintenance/";
        $store = $this->storeManager->getStore();

        // Default template ([store_code].html)
        $template = $path . $store->getCode() . ".html";
        if (!$this->fileDriver->isExists($template)) {
            // Second template ([website_code].html)
            $template = $path . $store->getWebsite()->getCode() . ".html";
            if (!$this->fileDriver->isExists($template)) {
                // Third template (fallback.html)
                $template = $path . "fallback.html";
                if (!$this->fileDriver->isExists($template)) {
                    // Last template (index.html)
                    $template = $path . "index.html";
                }
            }
        }

        $template = $this->fileReader->read($template);

        if (!$template) {
            return __('Maintenance html file not found. Upload your index.html file under pub/maintenance directory.');
        }

        return $template;
    }
}
