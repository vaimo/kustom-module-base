<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Observer;

use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Event;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Base\Observer\DebugDataCollectorTaxConfigObserver;
use Magento\Framework\Event\Observer;

/**
 * @internal
 */
class DebugDataCollectorTaxConfigObserverTest extends TestCase
{
    /**
     * @var DebugDataCollectorObserver
     */
    private $debugDataCollectorConfigObserver;

    protected function setUp(): void
    {
        $this->debugDataCollectorConfigObserver = parent::setUpMocks(DebugDataCollectorTaxConfigObserver::class);
    }

    public function testExecutionAddsTaxConfigDataToDebugDataObject(): void
    {
        $debugDataObject = $this->createMock(DebugDataObject::class);
        $debugDataObject->expects($this->once())
            ->method('addData');

        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->disableAutoReturnValueGeneration()
            ->addMethods(['getDebugDataObject'])
            ->getMock();

        $event->expects($this->once())
            ->method('getDebugDataObject')
            ->willReturn($debugDataObject);

        $observer = $this->createMock(Observer::class);
        $observer
            ->expects($this->once())
            ->method('getEvent')
            ->willReturn($event);

        $this->dependencyMocks['extractor']
            ->expects($this->once())
            ->method('getTaxInfo')
            ->willReturn(['stringified data' => 'data']);

        $this->debugDataCollectorConfigObserver->execute($observer);
    }
}
