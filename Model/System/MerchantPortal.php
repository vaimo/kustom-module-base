<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System;

use Klarna\Base\Api\OrderInterface as KlarnaOrder;
use Klarna\AdminSettings\Model\Configurations\Api;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class MerchantPortal
{
    public const MERCHANT_PORTAL = 'https://portal.kustom.co/';
    public const MERCHANT_TEST_PORTAL = 'https://portal.playground.kustom.co/';

    /**
     * @var Api
     */
    private api $apiConfiguration;

    /**
     * @param Api $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(Api $apiConfiguration)
    {
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Get Merchant Portal link for order
     *
     * @param MageOrder $mageOrder
     * @param KlarnaOrder $klarnaOrder
     * @return string
     */
    public function getOrderMerchantPortalLink(MageOrder $mageOrder, KlarnaOrder $klarnaOrder): string
    {
        $store = $mageOrder->getStore();
        $currency = $mageOrder->getOrderCurrencyCode();

        $isTest = $this->apiConfiguration->isTestMode($store, $currency);
        $portalBaseUrl = $isTest ? self::MERCHANT_TEST_PORTAL : self::MERCHANT_PORTAL;
        $merchantId = $this->apiConfiguration->getUserName($store, $currency);

        return $portalBaseUrl .
            "orders/" .
            $klarnaOrder->getKlarnaOrderId() .
            "?merchantId=" .
            $merchantId;
    }
}
