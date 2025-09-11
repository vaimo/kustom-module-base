<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper;

/**
 * @internal
 */
class DataConverter
{
    /**
     * Prepare float for API call
     *
     * @param float $float
     *
     * @return float
     */
    public function toApiFloat($float)
    {
        return (int) round($float * 100);
    }

    /**
     * Convert value to a shop specific value
     *
     * @param int $value
     * @return float
     */
    public function toShopFloat($value): float
    {
        return round($value / 100, 2);
    }
}
