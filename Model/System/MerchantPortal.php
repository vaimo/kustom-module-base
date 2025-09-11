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
    public const MERCHANT_PORTAL = 'https://portal.klarna.com/orders/';

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
        $merchantId = $this->apiConfiguration->getUserName($store, $mageOrder->getOrderCurrencyCode());

        $merchantIdArray = explode("_", $merchantId);
        return self::MERCHANT_PORTAL .
            "merchants/" .
            $merchantIdArray[0] .
            "/orders/" .
            $klarnaOrder->getKlarnaOrderId();
    }
}
