<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Api;

use Magento\Sales\Api\Data\OrderInterface as MageOrder;
use Magento\Framework\Model\AbstractModel;

/**
 * Interface OrderRepositoryInterface
 *
 * @api
 */
interface OrderRepositoryInterface
{
    /**
     * Save an order
     *
     * @param AbstractModel $order
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(AbstractModel $order);

    /**
     * Get order by ID
     *
     * @param int $id
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function getById(int $id);

    /**
     * Load by Klarna order id
     *
     * @param string $klarnaOrderId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByKlarnaOrderId(string $klarnaOrderId);

    /**
     * Load by session id
     *
     * @param string $sessionId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySessionId(string $sessionId);

    /**
     * Load by reservation id
     *
     * @param string $reservationId
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByReservationId(string $reservationId);

    /**
     * Load by an order
     *
     * @param MageOrder $mageOrder
     * @return OrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrder(MageOrder $mageOrder);
}
