<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model;

use Klarna\Base\Api\OrderInterface;
use Klarna\Base\Api\OrderRepositoryInterface;
use Klarna\Base\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * @internal
 */
class OrderRepository extends RepositoryAbstract implements OrderRepositoryInterface
{
    /**
     * OrderRepository constructor.
     *
     * @param OrderFactory  $modelFactory
     * @param OrderResource $resourceModel
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        OrderFactory $modelFactory,
        OrderResource $resourceModel
    ) {
        parent::__construct($resourceModel, $modelFactory);
    }

    /**
     * @inheritDoc
     */
    public function getByKlarnaOrderId(string $klarnaOrderId): OrderInterface
    {
        return $this->getByKeyValuePair('klarna_order_id', $klarnaOrderId);
    }

    /**
     * @inheritDoc
     */
    public function getByOrder(MageOrder $mageOrder): OrderInterface
    {
        return $this->getByKeyValuePair('order_id', (string) $mageOrder->getId());
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): OrderInterface
    {
        return $this->getByKeyValuePair('id', (string) $id);
    }

    /**
     * @inheritDoc
     */
    public function getByReservationId(string $reservationId): OrderInterface
    {
        return $this->getByKeyValuePair('reservation_id', $reservationId);
    }

    /**
     * @inheritDoc
     */
    public function getBySessionId(string $sessionId): OrderInterface
    {
        return $this->getByKeyValuePair('session_id', $sessionId);
    }

    /**
     * Get order identifier by Reservation
     *
     * @param string $reservationId
     * @param OrderInterface|null $order
     * @return OrderInterface
     * @throws NoSuchEntityException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getIdByReservationId(string $reservationId, ?OrderInterface $order = null): OrderInterface
    {
        return $this->getByKeyValuePair('reservation_id', $reservationId);
    }

    /**
     * Get order identifier by Klarna Order ID
     *
     * @param string $klarnaOrderId
     * @param OrderInterface|null $order
     * @return OrderInterface
     * @throws NoSuchEntityException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getIdByKlarnaOrderId(string $klarnaOrderId, ?OrderInterface $order = null): OrderInterface
    {
        return $this->getByKeyValuePair('klarna_order_id', $klarnaOrderId);
    }

    /**
     * Get order identifier by order
     *
     * @param MageOrder $mageOrder
     * @param OrderInterface|null $order
     * @return OrderInterface
     * @throws NoSuchEntityException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getIdByOrder(MageOrder $mageOrder, ?OrderInterface $order = null): OrderInterface
    {
        return $this->getByKeyValuePair('order_id', (string) $mageOrder->getId());
    }

    /**
     * Get order ID by Session ID
     *
     * @param string $sessionId
     * @param OrderInterface|null $order
     * @return OrderInterface
     * @throws NoSuchEntityException
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function getIdBySessionId(string $sessionId, ?OrderInterface $order = null): OrderInterface
    {
        return $this->getByKeyValuePair('session_id', $sessionId);
    }
}
