<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Api;

/**
 * @deprecated Will be moved in the next major version to OrderInterface
 * @see OrderInterface
 */
interface OrderAuthorizedPaymentMethodInterface
{
    /**
     * Get authorized payment method type
     *
     * @return string
     */
    public function getAuthorizedPaymentMethod(): string;

    /**
     * Set authorized payment method type
     *
     * @param string $authorizedPaymentMethod
     *
     * @return OrderInterface
     */
    public function setAuthorizedPaymentMethod(string $authorizedPaymentMethod): OrderInterface;
}
