<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Handler;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\FormFactory
 */
class HandlerTest extends TestCase
{
    /**
     * @var Handler
     */
    private Handler $handler;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var DataObject
     */
    private DataObject $dataObject;
    /**
     * @var Address
     */
    private Address $address;

    public function testSetShippingAddressDataFromRequestNoShippingAddressSet(): void
    {
        $this->dependencyMocks['import']->expects(static::never())
            ->method('importAddressFromRequest');

        $this->handler->setShippingAddressDataFromRequest($this->dataObject, $this->quote);
    }

    public function testSetShippingAddressDataFromRequestCallingAddressUpdateLogic(): void
    {
        $this->dependencyMocks['import']->expects(static::once())
            ->method('importAddressFromRequest');

        $this->dataObject->method('hasShippingAddress')
            ->willReturn(true);
        $this->dataObject->method('getShippingAddress')
            ->willReturn([]);
        $this->handler->setShippingAddressDataFromRequest($this->dataObject, $this->quote);
    }

    public function testSetShippingAddressDataFromArrayQuoteIsVirtualImpliesNothingIsCalled(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->dependencyMocks['import']->expects(static::never())
            ->method('importAddressFromRequest');

        $this->handler->setShippingAddressDataFromArray([], $this->quote);
    }

    public function testSetShippingAddressDataFromArrayCallingAddressUpdateLogic(): void
    {
        $this->dependencyMocks['import']->expects(static::once())
            ->method('importAddressFromRequest');

        $this->handler->setShippingAddressDataFromArray([], $this->quote);
    }

    public function testSetBillingAddressDataFromRequestNoBillingAddressSet(): void
    {
        $this->dependencyMocks['import']->expects(static::never())
            ->method('importAddressFromRequest');

        $this->handler->setBillingAddressDataFromRequest($this->dataObject, $this->quote);
    }

    public function testSetBillingAddressDataFromRequestCallingAddressUpdateLogic(): void
    {
        $this->dependencyMocks['import']->expects(static::once())
            ->method('importAddressFromRequest');

        $this->dataObject->method('hasBillingAddress')
            ->willReturn(true);
        $this->dataObject->method('getBillingAddress')
            ->willReturn([]);
        $this->handler->setBillingAddressDataFromRequest($this->dataObject, $this->quote);
    }

    public function testSetBillingAddressDataFromArrayCallingAddressUpdateLogic(): void
    {
        $this->dependencyMocks['import']->expects(static::once())
            ->method('importAddressFromRequest');

        $this->handler->setShippingAddressDataFromArray([], $this->quote);
    }

    public function testSetDefaultShippingAddressSettingCountryId(): void
    {
        $expected = 'DE';
        $this->dependencyMocks['vat']->method('getMerchantCountryCode')
            ->willReturn($expected);

        $this->address->expects(static::once())
            ->method('setCountryId')
            ->with($expected);
        $this->handler->setDefaultShippingAddress($this->quote);
    }

    protected function setUp(): void
    {
        $this->handler = parent::setUpMocks(Handler::class);

        $this->address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(Quote::class);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->address);
        $this->quote->method('getBillingAddress')
            ->willReturn($this->address);
        $this->dataObject = $this->mockFactory->create(
            DataObject::class,
            [],
            [
                'hasShippingAddress',
                'getShippingAddress',
                'hasBillingAddress',
                'getBillingAddress'
            ]
        );
    }
}
