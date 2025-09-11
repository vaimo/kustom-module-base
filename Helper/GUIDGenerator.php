<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Helper;

use Random\RandomException;

/**
 * @internal
 */
class GUIDGenerator
{
    /**
     * Generates a GUID with com_create_guid if available
     * Otherwise generates a GUID with random_int())
     * PHP Docs for com_create_guid https://www.php.net/manual/en/function.com-create-guid.php
     *
     * @return string
     * @throws RandomException
     */
    public function generateGUID(): string
    {
        try {
            if (function_exists('com_create_guid')) {
                return trim(com_create_guid(), '{}');
            }

            return sprintf(
                '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(16384, 20479),
                random_int(32768, 49151),
                random_int(0, 65535),
                random_int(0, 65535),
                random_int(0, 65535)
            );
        } catch (RandomException $e) {
            throw new RandomException($e->getMessage());
        }
    }
}
