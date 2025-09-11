<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote;

use Klarna\Base\Model\Quote\Customer;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Customer
 */
class CustomerTest extends TestCase
{
    /**
     * @var Customer
     */
    private $model;
    /**
     * @var Quote|PHPUnit_Framework_MockObject_MockObject
     */
    private $quote;
    /**
     * @var DataObject|PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObject;

    public function testUpdateCustomerNoBillingAddressExist(): void
    {
        $this->quote->expects(static::never())
            ->method('setCustomerFirstname');
        $this->model->setCustomerDataFromRequest($this->dataObject, $this->quote);
    }

    public function testUpdateCustomerCustomerIdExist(): void
    {
        $this->quote->expects(static::never())
            ->method('setCustomerFirstname');
        $this->quote->method('getCustomerId')
            ->willReturn('12345');
        $this->dataObject->method('hasBillingAddress')
            ->willReturn(true);
        $this->model->setCustomerDataFromRequest($this->dataObject, $this->quote);
    }

    public function testUpdateCustomerUpdatingTheQuote(): void
    {
        $billingAddress = [
            'email' => 'any_email',
            'given_name' => 'any given name',
            'family_name' => 'any family name'
        ];

        $this->quote->method('getCustomerId')
            ->willReturn(0);
        $this->dataObject->method('hasBillingAddress')
            ->willReturn(true);
        $this->dataObject->method('getBillingAddress')
            ->willReturn($billingAddress);

        $this->quote->expects(static::once())
            ->method('setCustomerFirstname');
        $this->model->setCustomerDataFromRequest($this->dataObject, $this->quote);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Customer::class, [DataObjectFactory::class => ['create']]);

        $this->quote      = $this->mockFactory->create(Quote::class, [], ['setCustomerFirstname', 'getCustomerId']);
        $this->dataObject = $this->mockFactory->create(
            DataObject::class,
            [],
            [
                'hasBillingAddress',
                'getBillingAddress'
            ]
        );
    }
}
