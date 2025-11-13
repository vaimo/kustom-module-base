<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * @internal
 */
class Fields
{
    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(RegionFactory $regionFactory, DataObjectFactory $dataObjectFactory)
    {
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Getting back the quote address fields filled by the Klarna address
     *
     * @param array $klarnaAddressInput
     * @return array
     */
    public function getQuoteAddressFieldsByKlarnaAddress(array $klarnaAddressInput): array
    {
        $klarnaAddressInstance = $this->dataObjectFactory->create(['data' => $klarnaAddressInput]);
        $country = strtoupper($klarnaAddressInstance->getCountry());

        $data = [
            AddressInterface::KEY_LASTNAME      => $klarnaAddressInstance->getFamilyName(),
            AddressInterface::KEY_FIRSTNAME     => $klarnaAddressInstance->getGivenName(),
            AddressInterface::KEY_EMAIL         => $klarnaAddressInstance->getEmail(),
            AddressInterface::KEY_COMPANY       => $klarnaAddressInstance->getOrganizationName(),
            AddressInterface::KEY_PREFIX        => $klarnaAddressInstance->getTitle(),
            AddressInterface::KEY_STREET        => $this->getStreetData($klarnaAddressInstance),
            AddressInterface::KEY_POSTCODE      => $klarnaAddressInstance->getPostalCode(),
            AddressInterface::KEY_CITY          => $klarnaAddressInstance->getCity(),
            AddressInterface::KEY_REGION_ID     => (int)$this->regionFactory->create()->loadByCode(
                $klarnaAddressInstance->getRegion(),
                $country
            )->getId(),
            AddressInterface::KEY_REGION        => $klarnaAddressInstance->getRegion(),
            AddressInterface::KEY_TELEPHONE     => $klarnaAddressInstance->getPhone(),
            AddressInterface::KEY_COUNTRY_ID    => $country
        ];

        if ($klarnaAddressInstance->hasCustomerDob()) {
            $data['dob'] = $klarnaAddressInstance->getCustomerDob();
        }

        if ($klarnaAddressInstance->hasCustomerGender()) {
            $data['gender'] = $klarnaAddressInstance->getCustomerGender();
        }

        return $data;
    }

    /**
     * Getting back the street data
     *
     * @param DataObject $klarnaAddressData
     * @return array
     */
    private function getStreetData(DataObject $klarnaAddressData): array
    {
        return array_filter(
            [
                $klarnaAddressData->getStreetAddress() . $klarnaAddressData->getHouseExtension(),
                $klarnaAddressData->getData('street_address2'),
            ]
        );
    }
}
