<?php
/**
 * Alpine_Maintenance
 *
 * @copyright   Copyright (c) 2020 Alpine Consulting, Inc (www.alpineinc.com)
 * @author      Michal Zymela (mzymela@alpineinc.com)
 */

namespace Alpine\Maintenance\Plugin;

use Alpine\Maintenance\Error\Maintenance;
use Exception;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

class HttpPlugin
{

    /**
     * @var Maintenance
     */
    private $maintenance;

    /**
     * HttpPlugin constructor.
     *
     * @param Maintenance $maintenance
     */
    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * @param Http $subject
     * @param callable $proceed
     * @param Bootstrap $bootstrap
     * @param Exception $exception
     *
     * @return bool
     */
    public function aroundCatchException(
        Http $subject,
        callable $proceed,
        Bootstrap $bootstrap,
        Exception $exception
    ) {
        if (!$bootstrap->isDeveloperMode() && $bootstrap->getErrorCode() === Bootstrap::ERR_MAINTENANCE) {
            $this->maintenance->renderPage();

            return true;
        }

        return $proceed($bootstrap, $exception);
    }
}
