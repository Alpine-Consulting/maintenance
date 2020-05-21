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
     * Maintenance constructor.
     *
     * @param Filesystem $filesystem
     * @param FileReader $fileReader
     */
    public function __construct(
        Filesystem $filesystem,
        FileReader $fileReader
    ) {
        $this->filesystem = $filesystem;
        $this->fileReader = $fileReader;
        $objectManagerFactory = Bootstrap::createObjectManagerFactory(BP, $_SERVER);
        $this->objectManager = $objectManagerFactory->create($_SERVER);
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
        $templatePath = $this->filesystem
            ->getDirectoryRead(DirectoryList::PUB)
            ->getAbsolutePath('maintenance/index.html');

        $template = $this->fileReader->read($templatePath);

        if (!$template) {
            return __('Maintenance html file not found. Upload your index.html file under pub/maintenance directory.');
        }

        return $template;
    }
}
