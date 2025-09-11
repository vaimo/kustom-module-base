<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\ShippingMethod;

use Klarna\Base\Model\Quote\ShippingMethod\SelectionAssurance;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

class SelectionAssuranceTest extends TestCase
{
    /**
     * @var SelectionAssurance
     */
    private SelectionAssurance $selectionAssurance;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $address;

    public function testEnsureShippingMethodSelectedQuoteIsVirtual(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->dependencyMocks['quoteMethodHandler']->expects(static::never())
            ->method('setDefaultShippingMethod');

        $this->selectionAssurance->ensureShippingMethodSelected($this->quote);
    }

    public function testEnsureShippingMethodSelectedShippingMethodIsSet(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->address->method('getShippingMethod')
            ->willReturn('my_method');
        $this->dependencyMocks['quoteMethodHandler']->expects(static::never())
            ->method('setDefaultShippingMethod');

        $this->selectionAssurance->ensureShippingMethodSelected($this->quote);
    }

    public function testEnsureShippingMethodSelectedCallingSetDefaultMethodLogic(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->address->method('getShippingMethod')
            ->willReturn(null);
        $this->dependencyMocks['quoteMethodHandler']->expects(static::once())
            ->method('setDefaultShippingMethod');

        $this->selectionAssurance->ensureShippingMethodSelected($this->quote);
    }

    public function testEnsureShippingMethodSelectedWithPreCollectQuoteIsVirtual(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->address->expects(static::never())
            ->method('collectShippingRates');
        $this->dependencyMocks['quoteMethodHandler']->expects(static::never())
            ->method('setDefaultShippingMethod');

        $this->selectionAssurance->ensureShippingMethodSelectedWithPreCollect($this->quote);
    }

    public function testEnsureShippingMethodSelectedWithPreCollectShippingMethodIsSet(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->address->method('getShippingMethod')
            ->willReturn('my_method');
        $this->dependencyMocks['quoteMethodHandler']->expects(static::never())
            ->method('setDefaultShippingMethod');
        $this->address->expects(static::once())
            ->method('collectShippingRates');

        $this->selectionAssurance->ensureShippingMethodSelectedWithPreCollect($this->quote);
    }

    public function testEnsureShippingMethodSelectedWithPreCollectCallingSetDefaultMethodLogic(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->address->method('getShippingMethod')
            ->willReturn(null);
        $this->dependencyMocks['quoteMethodHandler']->expects(static::once())
            ->method('setDefaultShippingMethod');
        $this->address->expects(static::once())
            ->method('collectShippingRates');

        $this->selectionAssurance->ensureShippingMethodSelectedWithPreCollect($this->quote);
    }

    protected function setUp(): void
    {
        $this->selectionAssurance = parent::setUpMocks(SelectionAssurance::class);

        $this->address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(Quote::class);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->address);
    }
}