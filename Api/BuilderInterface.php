<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Api;

use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Base class to generate API configuration
 *
 * @api
 */
interface BuilderInterface
{
    public const GENERATE_TYPE_CREATE        = 'create';
    public const GENERATE_TYPE_UPDATE        = 'update';
    public const GENERATE_TYPE_PLACE         = 'place';
    public const GENERATE_TYPE_CLIENT_UPDATE = 'client_update';

    /**
     * Generate the create request
     *
     * @param CartInterface $quote
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    public function generateCreateRequest(CartInterface $quote);

    /**
     * Generate the update request
     *
     * @param CartInterface $quote
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    public function generateUpdateRequest(CartInterface $quote);

    /**
     * Generate the place order request
     *
     * @param CartInterface $quote
     * @return $this
     * @throws \Klarna\Base\Exception
     */
    public function generatePlaceOrderRequest(CartInterface $quote);

    /**
     * Getting back the parameter instance
     *
     * @return Parameter
     */
    public function getParameter();
}
