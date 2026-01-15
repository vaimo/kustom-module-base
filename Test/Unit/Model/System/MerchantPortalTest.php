<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\System;

use Klarna\Base\Api\OrderInterface as KlarnaOrder;
use Klarna\Base\Model\System\MerchantPortal;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Base\Model\System\MerchantPortal
 */
class MerchantPortalTest extends TestCase
{
    /**
     * @var MerchantPortal
     */
    private $model;
    /**
     * @var MockObject|KlarnaOrder
     */
    private $klarnaOrder;
    /**
     * @var MockObject|MageOrder
     */
    private $mageOrder;

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(MerchantPortal::class);

        $this->klarnaOrder = $this->mockFactory->create(KlarnaOrder::class);
        $this->mageOrder = $this->getMockBuilder(MageOrder::class)
            ->onlyMethods(['getOrderCurrencyCode'])
            ->addMethods(['getStore'])
            ->getMockForAbstractClass();

        $store = $this->mockFactory->create(Store::class);
        $this->mageOrder->method('getStore')
            ->willReturn($store);
        $this->mageOrder->method('getOrderCurrencyCode')
            ->willReturn('currency_code');

        $merchantId = 'MERCHANT-123';
        $this->dependencyMocks['apiConfiguration']
            ->method('getUsername')
            ->willReturn($merchantId);
        $this->klarnaOrder
            ->method('getKlarnaOrderId')
            ->willReturn('ORDER-123');
    }

    /**
     * @covers ::getOrderMerchantPortalLink
     */
    public function testGetOrderMerchantPortalLinkToLive(): void
    {
        $this->dependencyMocks['apiConfiguration']
            ->method('isTestMode')
            ->willreturn(false);

        $urlPath = 'orders/ORDER-123?merchantId=MERCHANT-123';
        $expected = MerchantPortal::MERCHANT_PORTAL . $urlPath;

        $result = $this->model->getOrderMerchantPortalLink($this->mageOrder, $this->klarnaOrder);

        static::assertEquals($result, $expected);
    }

    /**
     * @covers ::getOrderMerchantPortalLink
     */
    public function testGetOrderMerchantPortalLinkToTest(): void
    {
        $this->dependencyMocks['apiConfiguration']
            ->method('isTestMode')
            ->willreturn(true);

        $urlPath = 'orders/ORDER-123?merchantId=MERCHANT-123';
        $expected = MerchantPortal::MERCHANT_TEST_PORTAL . $urlPath;

        $result = $this->model->getOrderMerchantPortalLink($this->mageOrder, $this->klarnaOrder);

        static::assertEquals($result, $expected);
    }
}
