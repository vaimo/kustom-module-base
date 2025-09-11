<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Model;

use PHPUnit\Framework\TestCase;

/**
 *
 * @coversDefaultClass \Klarna\Base\Model\Order
 */
class OrderTest extends TestCase
{
    /**
     * @var CartInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mageOrderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var Order
     */
    private $model;

    /**
     * @covers ::getIsAcknowledged()
     * @covers ::setIsAcknowledged()
     * @covers ::isAcknowledged()
     */
    public function testIsAcknowledgedAccessors()
    {
        $value = 1;

        $result = $this->model->setIsAcknowledged($value)->getIsAcknowledged();
        $this->assertEquals($value, $result);
        $this->assertEquals($value, $this->model->isAcknowledged());
    }

    /**
     * @covers ::getKlarnaOrderId()
     * @covers ::setKlarnaOrderId()
     */
    public function testKlarnaOrderIdAccessors()
    {
        $value = 'KLARNA-ORDER-ID';

        $result = $this->model->setKlarnaOrderId($value)->getKlarnaOrderId();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers ::getOrderId()
     * @covers ::setOrderId()
     */
    public function testOrderIdAccessors()
    {
        $value = 1;

        $result = $this->model->setOrderId($value)->getOrderId();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers ::getIdentities()
     */
    public function testGetIdentities()
    {
        $value = 1;

        $this->model->setId($value);
        $result = $this->model->getIdentities();
        $this->assertEquals([\Klarna\Base\Model\Order::CACHE_TAG . '_' . $value], $result);
    }

    /**
     * @covers ::getReservationId()
     * @covers ::setReservationId()
     */
    public function testReservationIdAccessors()
    {
        $value = 'RESERVATION-ID';

        $result = $this->model->setReservationId($value)->getReservationId();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers ::getSessionId()
     * @covers ::setSessionId()
     */
    public function testSessionIdAccessors()
    {
        $value = 'klarna-session-id';

        $result = $this->model->setSessionId($value)->getSessionId();
        $this->assertEquals($value, $result);
    }

    /**
     * @covers ::getAuthorizedPaymentMethod()
     * @covers ::setAuthorizedPaymentMethod()
     */
    public function testAuthorizedPaymentMethodAccessors(): void
    {
        $value = 'direct_debit';

        $result = $this->model->setAuthorizedPaymentMethod($value)->getAuthorizedPaymentMethod();
        $this->assertEquals($value, $result);
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->resourceMock = $this->getMockBuilder(\Klarna\Base\Model\ResourceModel\Order::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->mageOrderMock = $this->createMock(\Magento\Sales\Api\Data\OrderInterface::class);

        $this->model = $objectManager->getObject(\Klarna\Base\Model\Order::class);
    }
}
