<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Controller;

use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;

class RequestTraitTest extends TestCase
{
    public function testGetRequest()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $testController = new ATestClassToUseRequestTrait($requestMock);

        $result = $testController->getRequest();

        $this->assertInstanceOf(RequestInterface::class, $result);
    }
}
