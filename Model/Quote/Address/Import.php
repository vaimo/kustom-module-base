<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Customer\Model\Form;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * @internal
 */
class Import
{

    /**
     * Importing the address of the Klarna request in the quote Address
     *
     * @param array $klarnaAddressData
     * @param Form $customerForm
     * @param QuoteAddress $address
     * @return QuoteAddress
     */
    public function importAddressFromRequest(
        array $klarnaAddressData,
        Form $customerForm,
        QuoteAddress $address
    ): QuoteAddress {
        $customerForm->setEntity($address);
        $addressData = $customerForm->extractData($customerForm->prepareRequest($klarnaAddressData));
        $customerForm->compactData($addressData);

        return $address;
    }
}
