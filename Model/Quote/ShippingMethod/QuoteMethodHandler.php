<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\ShippingMethod;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;
use Magento\Quote\Api\ShippingMethodManagementInterface;

/**
 * @internal
 */
class QuoteMethodHandler
{
    /**
     * @var MageQuoteRepository
     */
    private MageQuoteRepository $mageQuoteRepository;
    /**
     * @var ShippingMethodManagementInterface
     */
    private ShippingMethodManagementInterface $shippingMethodManagement;

    /**
     * @param MageQuoteRepository $mageQuoteRepository
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @codeCoverageIgnore
     */
    public function __construct(
        MageQuoteRepository $mageQuoteRepository,
        ShippingMethodManagementInterface $shippingMethodManagement
    ) {
        $this->mageQuoteRepository = $mageQuoteRepository;
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

    /**
     * Setting the shipping method
     *
     * @param CartInterface $quote
     * @param string $shippingMethod
     */
    public function setShippingMethod(CartInterface $quote, string $shippingMethod): void
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setShippingMethod($shippingMethod);

        $extensionAttributes = $quote->getExtensionAttributes();
        if ($extensionAttributes !== null) {
            $shipping_assignments = $quote->getExtensionAttributes()->getShippingAssignments();

            if ($shipping_assignments !== null) {
                foreach ($shipping_assignments as $assignment) {
                    $assignment->getShipping()->setMethod($shippingMethod);
                }
            }
        }

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
    }

    /**
     * Updating the quote with the given shipping method code
     *
     * @param CartInterface $quote
     * @param string $shippingMethod
     */
    public function updateShippingMethod(CartInterface $quote, string $shippingMethod): void
    {
        if ($quote->getShippingAddress()->getShippingMethod() === $shippingMethod) {
            return;
        }

        $this->setShippingMethod($quote, $shippingMethod);
        $this->mageQuoteRepository->save($quote);
    }

    /**
     * Setting the default shipping method on the quote
     *
     * @param CartInterface $quote
     */
    public function setDefaultShippingMethod(CartInterface $quote): void
    {
        $rates = $this->shippingMethodManagement->getList($quote->getId());

        if (!empty($rates)) {
            $rate = (reset($rates));
            $code = $rate->getCarrierCode() . '_' . $rate->getMethodCode();
            $this->setShippingMethod($quote, $code);
        }
    }
}
