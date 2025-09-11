<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @internal
 */
class Country
{
    /**
     * @var DirectoryHelper
     */
    private DirectoryHelper $directoryHelper;

    /**
     * @param DirectoryHelper $directoryHelper
     * @codeCoverageIgnore
     */
    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * Getting back the used country
     *
     * @param ExtensibleDataInterface $dataObject
     * @return string
     */
    public function getCountry(ExtensibleDataInterface $dataObject): string
    {
        $billingAddress = $dataObject->getBillingAddress();
        if ($billingAddress !== null) {
            $country = $this->getCountryByAddress($billingAddress);
        }

        if (empty($country)) {
            $shippingAddress = $dataObject->getShippingAddress();
            if ($shippingAddress !== null) {
                $country = $this->getCountryByAddress($shippingAddress);
            }
        }
        if (empty($country)) {
            $country = $this->directoryHelper->getDefaultCountry($dataObject->getStore());
        }

        if (empty($country)) {
            return '';
        }

        return $country;
    }

    /**
     * Getting back the country from the address
     *
     * @param AbstractExtensibleModel $address
     * @return string|null
     */
    private function getCountryByAddress(AbstractExtensibleModel $address): ?string
    {
        $country = $address->getCountry();
        if (empty($country)) {
            return $address->getCountryId();
        }

        return $country;
    }

    /**
     * Returns true if the used country is US
     *
     * @param ExtensibleDataInterface $dataObject
     * @return bool
     */
    public function isUsCountry(ExtensibleDataInterface $dataObject): bool
    {
        return 'US' === $this->getCountry($dataObject);
    }
}
