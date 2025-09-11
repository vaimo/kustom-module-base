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

/**
 * @internal
 */
class SelectionAssurance
{
    /**
     * @var QuoteMethodHandler
     */
    private QuoteMethodHandler $quoteMethodHandler;

    /**
     * @param QuoteMethodHandler $quoteMethodHandler
     * @codeCoverageIgnore
     */
    public function __construct(QuoteMethodHandler $quoteMethodHandler)
    {
        $this->quoteMethodHandler = $quoteMethodHandler;
    }

    /**
     * Ensuring that a shipping method is selected.
     *
     * @param CartInterface $quote
     *
     * @return self
     * @throws \Klarna\Base\Model\Quote\ShippingMethod\StateException&\Throwable
     * @throws \Klarna\Base\Model\Quote\ShippingMethod\LocalizedException&\Throwable
     * @throws \Klarna\Base\Model\Quote\ShippingMethod\NoSuchEntityException&\Throwable
     */
    public function ensureShippingMethodSelected(CartInterface $quote): self
    {
        if ($quote->isVirtual()) {
            return $this;
        }

        if ($quote->getShippingAddress()->getShippingMethod()) {
            return $this;
        }

        $this->setCustomShippingMethod($quote);

        if (!$quote->getShippingAddress()->getShippingMethod()) {
            $this->quoteMethodHandler->setDefaultShippingMethod($quote);
        }
        return $this;
    }

    /**
     * Ensuring that a shipping method is selected with a pre collect of available shipping methods.
     *
     * @param CartInterface $magentoQuote
     * @return $this
     */
    public function ensureShippingMethodSelectedWithPreCollect(CartInterface $magentoQuote): self
    {
        if ($magentoQuote->isVirtual()) {
            return $this;
        }

        $address = $magentoQuote->getShippingAddress();
        $address->setCollectShippingRates(true);
        $address->collectShippingRates();

        return $this->ensureShippingMethodSelected($magentoQuote);
    }

    /**
     * Empty method so that plugins can set a custom shipping method.
     *
     * @param CartInterface $quote
     * @return SelectionAssurance
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomShippingMethod(CartInterface $quote): SelectionAssurance
    {
        return $this;
    }
}
