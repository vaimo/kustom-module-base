<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Observer;

use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Base\Helper\Debug\InfoExtractor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @internal
 */
class DebugDataCollectorConfigObserver implements ObserverInterface
{
    /**
     * @var InfoExtractor
     */
    private InfoExtractor $extractor;

    /**
     * @param InfoExtractor $extractor
     * @codeCoverageIgnore
     */
    public function __construct(
        InfoExtractor $extractor
    ) {
        $this->extractor = $extractor;
    }

    /**
     * Collects data from the database and adds it to the debug data object
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->setDataToDataObject($observer->getEvent()->getDebugDataObject());
    }

    /**
     * Adds data from the database to the debug data object
     *
     * @param DebugDataObject $dataObject
     * @return void
     */
    private function setDataToDataObject(DebugDataObject $dataObject): void
    {
        /**
         * This should be spread across all modules.
         * Each module should send its own configuration,
         * and then we will receive them from $this->dataObject->getData()
         */
        $configs = json_encode(
            $this->extractor->getAllKlarnaConfigs(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
        $dataObject->addData('klarna_configs', $configs);
    }
}
