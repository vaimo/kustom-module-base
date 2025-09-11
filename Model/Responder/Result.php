<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Responder;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;

/**
 * Providing controller result objects
 *
 * @internal
 */
class Result
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @param JsonFactory   $jsonFactory
     * @codeCoverageIgnore
     */
    public function __construct(JsonFactory $jsonFactory)
    {
        $this->jsonFactory   = $jsonFactory;
    }

    /**
     * Getting back a json result
     *
     * @param int   $httpCode
     * @param array $data
     * @return Json
     */
    public function getJsonResult(int $httpCode, array $data = []): Json
    {
        $resultPage = $this->jsonFactory->create();
        $resultPage->setData($data);
        $resultPage->setHttpResponseCode($httpCode);

        return $resultPage;
    }
}
