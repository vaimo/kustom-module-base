<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Payment;

use Magento\Payment\Model\InfoInterface;

/**
 * @internal
 */
class EnablementChecker
{
    /**
     * Returns true when the payment method name of the instance starts with "klarna_".
     *
     * @param InfoInterface $payment
     * @return bool
     */
    public function ispPaymentMethodInstanceCodeStartsWithKlarna(InfoInterface $payment): bool
    {
        return $this->isKlarnaPaymentMethodCode($payment->getMethod());
    }

    /**
     * Returns true if the payment method name starts with "klarna_"
     *
     * @param string $paymentMethodCode
     * @return bool
     */
    public function isPaymentMethodCodeStartsWithKlarna(string $paymentMethodCode): bool
    {
        return $this->isKlarnaPaymentMethodCode($paymentMethodCode);
    }

    /**
     * Checking if the given string starts with "klarna_"
     *
     * @param string $paymentMethodCode
     * @return bool
     */
    private function isKlarnaPaymentMethodCode(string $paymentMethodCode)
    {
        return strpos($paymentMethodCode, 'klarna_') === 0;
    }
}
