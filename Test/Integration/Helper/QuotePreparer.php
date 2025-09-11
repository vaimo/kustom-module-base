<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * @internal
 */
class QuotePreparer
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
    }

    public function addProduct($quote, string $sku, int $quantity)
    {
        for ($i = 1; $i <= $quantity; $i++) {
            $product = $this->productRepository->get($sku);

            if (str_contains($sku, 'downloadable')) {
                $this->addDownloadableProductToQuote($quote, $product);
            } else if (str_contains($sku, 'configurable')) {
                $this->addConfigurableProductToQuote($quote, $product);
            } else {
                $quote->addProduct($product);
            }
        }
    }

    private function addDownloadableProductToQuote($quote, $product): void
    {
        $quote->addProduct($product, new \Magento\Framework\DataObject([
            'links' => array_keys($product->getDownloadableLinks())
        ]));
    }

    private function addConfigurableProductToQuote($quote, $product)
    {
        $options = $this->objectManager->create(
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection::class
        );

        $eavConfig = $this->objectManager->get(\Magento\Eav\Model\Config::class);
        $attribute = $eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'test_configurable');
        $option = $options->setAttributeFilter($attribute->getId())->getFirstItem();

        $requestInfo = new \Magento\Framework\DataObject(
            [
                'product' => $product->getId(),
                'selected_configurable_option' => 1,
                'qty' => 1,
                'super_attribute' => [
                    $attribute->getId() => $option->getId()
                ]
            ]
        );

        $quote->addProduct($product, $requestInfo);
    }

    public function configureQuote($quote, string $currency, $billingAddress, $shippingAddress, string $shippingMethod = ''): void
    {
        $quote->setBaseCurrencyCode($currency);

        $quote->setBillingAddress($billingAddress);
        $quote->setShippingAddress($shippingAddress);

        if ($shippingMethod !== '') {
            $quote->getShippingAddress()->setShippingMethod($shippingMethod);
        }
    }

    public function configureQuoteButAddressJustOnCountryAndShippingMethod($quote, string $currency, $billingAddress, $shippingAddress, string $shippingMethod = ''): void
    {
        $quote->setBaseCurrencyCode($currency);

        $quote->getBillingAddress()->setCountryId($billingAddress[AddressInterface::KEY_COUNTRY_ID]);
        $quote->getShippingAddress()->setCountryId($shippingAddress[AddressInterface::KEY_COUNTRY_ID]);

        if ($shippingMethod !== '') {
            $quote->getShippingAddress()->setShippingMethod($shippingMethod);
        }
    }

    public function saveQuote($quote)
    {
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->getBillingAddress()->setCollectShippingRates(true);

        $quote->setTotalsCollectedFlag(false);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->save();
    }
}