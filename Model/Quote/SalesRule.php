<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote;

use Magento\SalesRule\Model\Validator;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class SalesRule
{
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param Validator $validator
     * @codeCoverageIgnore
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Returns true if a sales rule apply to shipping is used
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function isApplyToShippingUsed(CartInterface $quote): bool
    {
        $address = $quote->getShippingAddress();
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        }

        $this->validator->init(
            $quote->getStore()->getWebsiteId(),
            $quote->getCustomerGroupId(),
            $quote->getCouponCode()
        );
        foreach ($this->validator->getRules($address) as $rules) {
            if ($rules->getApplyToShipping()) {
                return true;
            }
        }

        return false;
    }
}
