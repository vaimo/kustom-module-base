<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
// phpcs:ignoreFile
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Controller;

use Klarna\Base\Controller\RequestTrait;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;

/**
 * This class is used to test the RequestTrait functionality.
 * @skip
 */
class ATestClassToUseRequestTrait
{
    use RequestTrait;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
}