<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Model;

use Klarna\Base\Test\Unit\Mock\TestCase;
use Klarna\Base\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @coversDefaultClass \Klarna\Base\Model\OrderRepository
 */
class OrderRepositoryTest extends TestCase
{
    /**
     * @var OrderRepository
     */
    private $model;

    /**
     * @var \Klarna\Base\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderMock;

    /**
     * @var Order|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mageOrderMock;

    /**
     * @covers ::getById()
     */
    public function testGetByIdWithException()
    {
        $this->expectException(NoSuchEntityException::class);

        $orderId = 123;

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        $this->model->getById($orderId);
    }

    /**
     * @covers ::getByOrder()
     */
    public function testGetByOrder()
    {
        $orderId = '15';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getByOrder($this->mageOrderMock));
    }

    /**
     * @covers ::getByOrder()
     */
    public function testGetByOrderWithException()
    {
        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        $this->expectException(NoSuchEntityException::class);

        static::assertEquals($this->orderMock, $this->model->getByOrder($this->mageOrderMock));
    }

    /**
     * @covers ::getByReservationId()
     */
    public function testGetByReservationId()
    {
        $orderId = '15';
        $reservationId = 'RESERVATION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getByReservationId($reservationId));
    }

    /**
     * @covers ::getByReservationId()
     */
    public function testGetByReservationIdWithException()
    {
        $this->expectException(NoSuchEntityException::class);

        $reservationId = '';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::assertEquals($this->orderMock, $this->model->getByReservationId($reservationId));
    }

    /**
     * @covers ::getBySessionId()
     */
    public function testGetBySessionId()
    {
        $orderId = '15';
        $sessionId = 'SESSION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getBySessionId($sessionId));
    }

    /**
     * @covers ::getBySessionId()
     */
    public function testGetBySessionIdWithException()
    {
        $this->expectException(NoSuchEntityException::class);

        $sessionId = '';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::assertEquals($this->orderMock, $this->model->getBySessionId($sessionId));
    }

    /**
     * @covers ::getById()
     */
    public function testGetById()
    {
        $orderId = 15;

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getById($orderId));
    }

    /**
     * @covers ::getByKlarnaOrderId()
     */
    public function testGetByKlarnaOrderId()
    {
        $orderId = '15';
        $klarnaOrderId = 'KLARNA-ORDER-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getByKlarnaOrderId($klarnaOrderId));
    }

    /**
     * @covers ::getByKlarnaOrderId()
     */
    public function testGetByKlarnaOrderIdNotExists()
    {
        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::expectException(NoSuchEntityException::class);
        static::assertEquals($this->orderMock, $this->model->getByKlarnaOrderId('1'));
    }

    /**
     * @covers ::save()
     */
    public function testSave()
    {
        $this->dependencyMocks['resourceModel']->expects(static::once())
            ->method('save')
            ->with($this->orderMock)
            ->willReturn($this->orderMock);

        $this->model->save($this->orderMock);
    }

    /**
     * @covers ::save()
     */
    public function testSaveWithException()
    {
        $this->expectException(CouldNotSaveException::class);

        $exceptionMessage = 'No such entity with payments_quote_id = ';
        $this->dependencyMocks['resourceModel']->expects(static::once())
            ->method('save')
            ->with($this->orderMock)
            ->willThrowException(new \Exception($exceptionMessage));

        $this->model->save($this->orderMock);
    }

    /**
     * @covers ::getIdByReservationId()
     */
    public function testGetIdByReservationId()
    {
        $reservationId = 'RESERVATION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn('1');

        static::assertEquals($this->orderMock, $this->model->getIdByReservationId($reservationId, $this->orderMock));
    }

    /**
     * @covers ::getIdByReservationId()
     */
    public function testGetIdByReservationIdWithoutOrderModel()
    {
        $reservationId = 'RESERVATION-ID';
        $expected = '1';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($expected);

        static::assertEquals($this->orderMock, $this->model->getIdByReservationId($reservationId));
    }

    /**
     * @covers ::getIdByReservationId()
     */
    public function testGetIdByReservationIdFails()
    {
        $reservationId = 'RESERVATION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::expectException(NoSuchEntityException::class);

        $this->model->getIdByReservationId($reservationId);
    }

    /**
     * @covers ::getIdByKlarnaOrderId()
     */
    public function testGetIdByKlarnaOrderId()
    {
        $klarnaOrderId = 'KLARNA-ORDER-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn('1');

        static::assertEquals($this->orderMock, $this->model->getIdByKlarnaOrderId($klarnaOrderId, $this->orderMock));
    }

    /**
     * @covers ::getIdByKlarnaOrderId()
     */
    public function testGetIdByKlarnaOrderIdWithoutOrderModel()
    {
        $klarnaOrderId = 'KLARNA-ORDER-ID';
        $expected = '1';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($expected);

        static::assertEquals($this->orderMock, $this->model->getIdByKlarnaOrderId($klarnaOrderId));
    }

    /**
     * @covers ::getIdByKlarnaOrderId()
     */
    public function testGetIdByKlarnaOrderIdFails()
    {
        $klarnaOrderId = 'KLARNA-ORDER-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::expectException(NoSuchEntityException::class);

        $this->model->getIdByKlarnaOrderId($klarnaOrderId);
    }

    /**
     * @covers ::getIdByOrder()
     */
    public function testGetIdByOrder()
    {
        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn('1');

        static::assertEquals($this->orderMock, $this->model->getIdByOrder($this->mageOrderMock, $this->orderMock));
    }

    /**
     * @covers ::getIdByOrder()
     */
    public function testGetIdByOrderWithoutOrderModel()
    {
        $orderId= '15';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($orderId);

        static::assertEquals($this->orderMock, $this->model->getIdByOrder($this->mageOrderMock));
    }

    /**
     * @covers ::getIdByOrder()
     */
    public function testGetIdByOrderFails()
    {
        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::expectException(NoSuchEntityException::class);

        $this->model->getIdByOrder($this->mageOrderMock);
    }

    /**
     * @covers ::getIdBySessionId()
     */
    public function testGetIdBySessionId()
    {
        $sessionId = 'SESSION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn('1');

        static::assertEquals($this->orderMock, $this->model->getIdBySessionId($sessionId, $this->orderMock));
    }

    /**
     * @covers ::getIdBySessionId()
     */
    public function testGetIdBySessionIdWithoutOrderModel()
    {
        $sessionId = 'SESSION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn($sessionId);

        static::assertEquals($this->orderMock, $this->model->getIdBySessionId($sessionId));
    }

    /**
     * @covers ::getIdBySessionId()
     */
    public function testGetIdBySessionIdFails()
    {
        $sessionId = 'SESSION-ID';

        $this->dependencyMocks['modelFactory']->expects(static::once())
            ->method('create')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(static::once())
            ->method('getId')
            ->willReturn(null);

        static::expectException(NoSuchEntityException::class);

        $this->model->getIdBySessionId($sessionId);
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(OrderRepository::class,
            [
                OrderFactory::class  => ['create'],
                OrderResource::class => ['save', 'load']
            ]
        );
        $this->mageOrderMock = $this->createSingleMock(Order::class);
        $this->orderMock = $this->createSingleMock(\Klarna\Base\Model\Order::class);
    }
}
