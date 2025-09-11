<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Model;

use Klarna\Base\Model\OrderRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class OrderRepositoryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ResourceConnection
     */
    private $connection;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->connection = $this->objectManager->get(ResourceConnection::class);
        $this->orderRepository = $this->objectManager->get(OrderRepository::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testSaveEntryWasSaved(): void
    {
        $expected = 'my_new_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_core_order(klarna_order_id, session_id, order_id) VALUES " .
            "('a', 'b', '1')";
        $connection->query($query);

        $query = "SELECT * FROM klarna_core_order where order_id = 1";
        $result = $connection->fetchAll($query);
        $klarnaOrder = $this->orderRepository->getById((int) $result[0]['id']);
        $klarnaOrder->setSessionId($expected);

        $this->orderRepository->save($klarnaOrder);

        $query = "SELECT * FROM klarna_core_order where order_id = 1";
        $result = $connection->fetchAll($query);
        static::assertEquals($expected, $result[0]['session_id']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByKlarnaOrderIdFoundEntry(): void
    {
        $expected = 'my_value';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_core_order(klarna_order_id, session_id, order_id) VALUES " .
            "('" . $expected . "', 'a', '1')";
        $connection->query($query);

        $klarnaOrder = $this->orderRepository->getByKlarnaOrderId($expected);
        static::assertEquals($expected, $klarnaOrder->getKlarnaOrderId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByKlarnaOrderIdNoEntryWasFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getByKlarnaOrderId('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdByKlarnaOrderIdNoEntryWasFound(): void
    {
        static::expectException(NoSuchEntityException::class);

        $this->orderRepository->getIdByKlarnaOrderId('999999999');
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByOrderFoundEntry(): void
    {
        $order = $this->objectManager->create(Order::class)->load('100000001', 'increment_id');

        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_core_order(klarna_order_id, session_id, order_id) VALUES " .
            "('1', 'a', '" . $order->getId() . "')";
        $connection->query($query);

        $klarnaOrder = $this->orderRepository->getByOrder($order);
        static::assertEquals($order->getId(), $klarnaOrder->getOrderId());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetByOrderNoEntryWasFound(): void
    {
        $order = $this->objectManager->create(Order::class)
            ->load('100000001', 'increment_id');

        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getByOrder($order);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdByOrderNoEntryWasFound(): void
    {
        $order = $this->objectManager->create(Order::class)
            ->load('100000001', 'increment_id');

        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getIdByOrder($order);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdBySessionIdIdFoundEntry(): void
    {
        $expected = 'session_id';
        $connection = $this->connection->getConnection();
        $query = "INSERT INTO " .
            "klarna_core_order(klarna_order_id, session_id, order_id) VALUES " .
            "('123', '" . $expected . "', '1')";
        $connection->query($query);

        $klarnaOrder = $this->orderRepository->getBySessionId($expected);
        static::assertEquals($expected, $klarnaOrder->getSessionId());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdBySessionIdIdNoEntryWasFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getBySessionId('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdBySessionIdNoEntryWasFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getIdBySessionId('999999999');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testGetIdByReservationIdNoEntryWasFound(): void
    {
        static::expectException(NoSuchEntityException::class);
        $this->orderRepository->getIdByReservationId('999999999');
    }
}