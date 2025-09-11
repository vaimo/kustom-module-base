<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Api;

/**
 * @api
 */
interface OrderInterface
{
    /**
     * Get Entity ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set Entity ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Magento Order ID
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set the Magento Order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get Reservation ID
     *
     * @return string
     */
    public function getReservationId();

    /**
     * Set Reservation ID
     *
     * @param string $reservationId
     * @return $this
     */
    public function setReservationId($reservationId);

    /**
     * Get Session ID
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Set Session ID
     *
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId);

    /**
     * Set Klarna Order ID
     *
     * @param string $orderId
     * @return $this
     */
    public function setKlarnaOrderId($orderId);

    /**
     * Get Klarna Order ID
     *
     * @return string
     */
    public function getKlarnaOrderId();

    /**
     * Set status of acknowledging the order with Klarna
     *
     * @param int $acknowledged
     * @return $this
     */
    public function setIsAcknowledged($acknowledged);

    /**
     * Get status of acknowledging the order with Klarna
     *
     * @return int
     */
    public function getIsAcknowledged();

    /**
     * Setting the is_b2b flag
     *
     * @param bool $flag
     */
    public function setIsB2b(bool $flag): self;

    /**
     * Returns true if the order is a b2b order
     *
     * @return bool
     */
    public function isB2b(): bool;

    /**
     * Setting the MID
     *
     * @param string $mid
     */
    public function setUsedMid(string $mid): self;

    /**
     * Getting back the MID
     *
     * @return string|null
     */
    public function getUsedMid(): ?string;
}
