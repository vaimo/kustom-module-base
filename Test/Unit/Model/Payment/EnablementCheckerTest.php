<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Payment;

use Klarna\Base\Model\Payment\EnablementChecker;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Payment\Model\Info;

/**
 * @coversDefaultClass \Klarna\Base\Model\Payment\EnablementChecker
 */
class EnablementCheckerTest extends TestCase
{
    /**
     * @var EnablementChecker
     */
    private EnablementChecker $model;
    /**
     * @var Info|\PHPUnit\Framework\MockObject\MockObject
     */
    private Info $paymentInfo;

    public function testIspPaymentMethodInstanceCodeStartsWithKlarnaSameCode(): void
    {
        $this->paymentInfo->method('getMethod')
            ->willReturn('klarna_pay_later');
        static::assertTrue($this->model->ispPaymentMethodInstanceCodeStartsWithKlarna($this->paymentInfo));
    }

    public function testIspPaymentMethodInstanceCodeStartsWithKlarnaDifferentCode(): void
    {
        $this->paymentInfo->method('getMethod')
            ->willReturn('pay_later_klarna');
        static::assertFalse($this->model->ispPaymentMethodInstanceCodeStartsWithKlarna($this->paymentInfo));
    }

    public function testIsPaymentMethodCodeStartsWithKlarnaSameCode(): void
    {
        static::assertTrue($this->model->isPaymentMethodCodeStartsWithKlarna('klarna_pay_later'));
    }

    public function testIsPaymentMethodCodeStartsWithKlarnaDifferentCode(): void
    {
        static::assertFalse($this->model->isPaymentMethodCodeStartsWithKlarna('pay_later_klarna'));
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(EnablementChecker::class);
        $this->paymentInfo = $this->mockFactory->create(Info::class, [], ['getMethod']);
    }
}
