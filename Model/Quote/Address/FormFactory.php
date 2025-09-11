<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Customer\Model\FormFactory as AddressFormFactory;
use Magento\Customer\Model\Form as CustomerForm;

/**
 * @internal
 */
class FormFactory
{
    /**
     * @var AddressFormFactory
     */
    private AddressFormFactory $addressFormFactory;

    /**
     * @param AddressFormFactory $addressFormFactory
     * @codeCoverageIgnore
     */
    public function __construct(AddressFormFactory $addressFormFactory)
    {
        $this->addressFormFactory = $addressFormFactory;
    }

    /**
     * Creating a customer address form
     *
     * @return CustomerForm
     */
    public function createCustomerAddressForm(): CustomerForm
    {
        $addressForm = $this->addressFormFactory->create();

        $addressForm->setFormCode('customer_address_edit');
        $addressForm->setEntityType('customer_address');

        return $addressForm;
    }
}
