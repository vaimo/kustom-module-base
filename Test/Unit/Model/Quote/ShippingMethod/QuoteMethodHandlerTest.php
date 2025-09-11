<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\ShippingMethod;

use Klarna\Base\Model\Quote\ShippingMethod\QuoteMethodHandler;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Cart\ShippingMethod;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\ShippingMethod\QuoteMethodHandler
 */
class QuoteMethodHandlerTest extends TestCase
{
    /**
     * @var QuoteMethodHandler
     */
    private QuoteMethodHandler $quoteMethodHandler;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $shippingAddress;

    public function testSetShippingMethodCollectShippingRates(): void
    {
        $this->shippingAddress->expects(static::once())
            ->method('setShippingMethod')
            ->with('my_method');
        $this->shippingAddress->expects(static::once())
            ->method('setCollectShippingRates')
            ->with(true);
        $this->quote->expects(static::once())
            ->method('setTotalsCollectedFlag')
            ->with(false);

        $this->quoteMethodHandler->setShippingMethod($this->quote, 'my_method');
    }

    public function testUpdateShippingMethodShippingMethodNotChanged(): void
    {
        $this->shippingAddress->expects(static::never())
            ->method('setShippingMethod')
            ->with('my_method');
        $this->shippingAddress->method('getShippingMethod')
            ->willReturn('my_method');

        $this->quoteMethodHandler->updateShippingMethod($this->quote, 'my_method');
    }

    public function testUpdateShippingMethodCollectShippingRates(): void
    {
        $this->shippingAddress->expects(static::once())
            ->method('setShippingMethod')
            ->with('my_method');
        $this->shippingAddress->expects(static::once())
            ->method('setCollectShippingRates')
            ->with(true);
        $this->quote->expects(static::once())
            ->method('setTotalsCollectedFlag')
            ->with(false);
        $this->shippingAddress->method('getShippingMethod')
            ->willReturn('my_method2');

        $this->quoteMethodHandler->updateShippingMethod($this->quote, 'my_method');
    }

    public function testSetDefaultShippingMethodSettingDefaultMethod(): void
    {
        $shippingMethod = $this->mockFactory->create(ShippingMethod::class);
        $shippingMethod->method('getMethodCode')
            ->willReturn('method_name');
        $shippingMethod->method('getCarrierCode')
            ->willReturn('carrier_name');

        $this->shippingAddress->expects(static::once())
            ->method('setShippingMethod')
            ->with('carrier_name_method_name');
        $this->shippingAddress->expects(static::once())
            ->method('setCollectShippingRates')
            ->with(true);
        $this->quote->expects(static::once())
            ->method('setTotalsCollectedFlag')
            ->with(false);

        $this->dependencyMocks['shippingMethodManagement']->method('getList')
            ->willReturn([$shippingMethod]);
        $this->quoteMethodHandler->setDefaultShippingMethod($this->quote);
    }

    public function testSetDefaultShippingMethodNoRatesFound(): void
    {
        $this->dependencyMocks['shippingMethodManagement']->method('getList')
            ->willReturn([]);
        $this->shippingAddress->expects(static::never())
            ->method('setShippingMethod')
            ->with('my_method');

        $this->quoteMethodHandler->setDefaultShippingMethod($this->quote);
    }

    protected function setUp(): void
    {
        $this->quoteMethodHandler = parent::setUpMocks(QuoteMethodHandler::class);
        $this->shippingAddress = $this->mockFactory->create(
            Address::class,
            [
                'getShippingMethod'
            ],
            [
                'setShippingMethod',
                'setCollectShippingRates'
            ]
        );
        $this->quote = $this->mockFactory->create(
            Quote::class,
            [
                'getShippingAddress',
                'getExtensionAttributes'
            ],
            [
                'setTotalsCollectedFlag'
            ]
        );
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
    }
}