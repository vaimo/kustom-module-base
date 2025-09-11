<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Customer
{

    /**
     * Setting the customer info on the quote based on the Klarna request data
     *
     * @param DataObject    $klarnaRequest
     * @param CartInterface $quote
     */
    public function setCustomerDataFromRequest(DataObject $klarnaRequest, CartInterface $quote): void
    {
        if (!$klarnaRequest->hasBillingAddress()) {
            return;
        }
        if ($quote->getCustomerId()) {
            return;
        }

        $billingAddress = $klarnaRequest->getBillingAddress();

        $quote->setCustomerEmail($billingAddress['email']);
        $quote->setCustomerFirstname($billingAddress['given_name']);
        $quote->setCustomerLastname($billingAddress['family_name']);
    }
}
