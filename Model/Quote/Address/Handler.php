<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Customer\Model\Vat;

/**
 * @internal
 */
class Handler
{
    /**
     * @var Import
     */
    private Import $import;
    /**
     * @var FormFactory
     */
    private FormFactory $formFactory;
    /**
     * @var Fields
     */
    private Fields $fields;
    /**
     * @var Vat
     */
    private Vat $vat;

    /**
     * @param FormFactory $formFactory
     * @param Import $import
     * @param Fields $fields
     * @param Vat $vat
     * @codeCoverageIgnore
     */
    public function __construct(FormFactory $formFactory, Import $import, Fields $fields, Vat $vat)
    {
        $this->import = $import;
        $this->formFactory = $formFactory;
        $this->fields = $fields;
        $this->vat = $vat;
    }

    /**
     * Setting the shipping address on the quote
     *
     * @param DataObject    $request
     * @param CartInterface $quote
     */
    public function setShippingAddressDataFromRequest(DataObject $request, CartInterface $quote): void
    {
        if (!$request->hasShippingAddress()) {
            return;
        }

        $this->performImport($request->getShippingAddress(), $quote->getShippingAddress());
    }

    /**
     * Setting the shipping address on the quote
     *
     * @param array $data
     * @param CartInterface $quote
     */
    public function setShippingAddressDataFromArray(array $data, CartInterface $quote): void
    {
        if ($quote->isVirtual()) {
            return;
        }
        $this->performImport($data, $quote->getShippingAddress());
    }

    /**
     * Setting the billing address on the quote
     *
     * @param DataObject    $request
     * @param CartInterface $quote
     */
    public function setBillingAddressDataFromRequest(DataObject $request, CartInterface $quote): void
    {
        if (!$request->hasBillingAddress()) {
            return;
        }

        $this->performImport($request->getBillingAddress(), $quote->getBillingAddress());
    }

    /**
     * Setting the billing address on the quote
     *
     * @param array $data
     * @param CartInterface $quote
     */
    public function setBillingAddressDataFromArray(array $data, CartInterface $quote): void
    {
        $this->performImport($data, $quote->getBillingAddress());
    }

    /**
     * Performing the import
     *
     * @param array $klarnaAddress
     * @param QuoteAddress $quoteAddress
     */
    private function performImport(array $klarnaAddress, QuoteAddress $quoteAddress): void
    {
        $this->import->importAddressFromRequest(
            $this->fields->getQuoteAddressFieldsByKlarnaAddress($klarnaAddress),
            $this->formFactory->createCustomerAddressForm(),
            $quoteAddress
        );

        /**
         * For guests the ID is always null since no address is assigned to them.
         * For logged in customers we have to set it to null to avoid on a save operation of the quote that the address
         * from the address book of the customer is loaded and used again.
         */
        $quoteAddress->setCustomerAddressId(null);
    }

    /**
     * Setting a default shipping address if no one exists
     *
     * @param CartInterface $quote
     */
    public function setDefaultShippingAddress(CartInterface $quote): void
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($this->vat->getMerchantCountryCode($quote->getStore()));
    }
}
