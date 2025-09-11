<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote;

use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class PaymentMethod
{
    /**
     * Setting the payment method on the Magento quote
     *
     * @param CartInterface $magentoQuote
     * @param string $paymentMethod
     */
    public function setPaymentMethod(CartInterface $magentoQuote, string $paymentMethod): void
    {
        $data = ['method' => $paymentMethod];
        $payment = $magentoQuote->getPayment();
        $payment->setQuote($magentoQuote);

        $address = $magentoQuote->getBillingAddress();
        if (!$magentoQuote->isVirtual()) {
            $address = $magentoQuote->getShippingAddress();
        }

        $address->setPaymentMethod($data['method']);
        $payment->importData($data);
    }
}
