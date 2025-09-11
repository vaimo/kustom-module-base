<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * @internal
 */
class DataProvider
{
    /**
     * @var AddressInterfaceFactory
     */
    private AddressInterfaceFactory $addressFactory;
    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @param AddressInterfaceFactory $addressFactory
     * @param DataObjectHelper $dataObjectHelper
     * @codeCoverageIgnore
     */
    public function __construct(AddressInterfaceFactory $addressFactory, DataObjectHelper $dataObjectHelper)
    {
        $this->addressFactory = $addressFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    public function getUkAddressData()
    {
        $addressData = [
            AddressInterface::KEY_TELEPHONE => '01624567564',
            AddressInterface::KEY_POSTCODE => 'W13 3BG',
            AddressInterface::KEY_COUNTRY_ID => 'GB',
            AddressInterface::KEY_CITY => 'London',
            AddressInterface::KEY_COMPANY => 'CompanyName',
            AddressInterface::KEY_STREET => 'Green str, 67',
            AddressInterface::KEY_LASTNAME => 'Smith',
            AddressInterface::KEY_FIRSTNAME => 'John',
            AddressInterface::KEY_REGION_ID => 'Greater London',
            AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
        ];

        return $this->createAddress($addressData);
    }

    public function getDeAddressData()
    {
        $addressData = [
            AddressInterface::KEY_TELEPHONE => '01624567564',
            AddressInterface::KEY_POSTCODE => '13055',
            AddressInterface::KEY_COUNTRY_ID => 'DE',
            AddressInterface::KEY_CITY => 'Berlin',
            AddressInterface::KEY_COMPANY => 'CompanyName',
            AddressInterface::KEY_STREET => 'Green str, 67',
            AddressInterface::KEY_LASTNAME => 'Smith',
            AddressInterface::KEY_FIRSTNAME => 'John',
            AddressInterface::KEY_REGION_ID => 82,
            AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
        ];

        return $this->createAddress($addressData);
    }

    public function getUsAddressData()
    {
        $addressData = [
            AddressInterface::KEY_TELEPHONE => '3468676',
            AddressInterface::KEY_POSTCODE => '36104',
            AddressInterface::KEY_COUNTRY_ID => 'US',
            AddressInterface::KEY_CITY => 'CityM',
            AddressInterface::KEY_COMPANY => 'CompanyName',
            AddressInterface::KEY_STREET => 'Green str, 67',
            AddressInterface::KEY_LASTNAME => 'Smith',
            AddressInterface::KEY_FIRSTNAME => 'John',
            AddressInterface::KEY_REGION_ID => 1,
            AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
        ];

        return $this->createAddress($addressData);
    }

    public function getUsKlarnaAddressData(): array
    {
        return [
            'phone' => '3468676',
            'postal_code' => '36104',
            'country' => 'US',
            'city' => 'CityM',
            'street_address' => 'Green str, 67',
            'family_name' => 'Smith',
            'given_name' => 'John',
            'email' => 'any_mail@mail.me'
        ];
    }

    public function getNzAddressData()
    {
        $addressData = [
            AddressInterface::KEY_TELEPHONE => '6427555290',
            AddressInterface::KEY_POSTCODE => '6011',
            AddressInterface::KEY_COUNTRY_ID => 'NZ',
            AddressInterface::KEY_CITY => 'Auckland',
            AddressInterface::KEY_COMPANY => 'CompanyName',
            AddressInterface::KEY_STREET => '286 Mount Wellington Highway',
            AddressInterface::KEY_LASTNAME => 'Smith',
            AddressInterface::KEY_FIRSTNAME => 'John',
            AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
        ];

        return $this->createAddress($addressData);
    }

    private function createAddress(array $address)
    {
        $addressInstance = $this->addressFactory->create();
        $this->dataObjectHelper->populateWithArray($addressInstance, $address, AddressInterfaceFactory::class);

        return $addressInstance;
    }
}
