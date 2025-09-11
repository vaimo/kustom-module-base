<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * RequestTrait trait.
 */
trait RequestTrait
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * Return request object
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
