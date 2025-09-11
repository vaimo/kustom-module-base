<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Checkout\Model\Session as MagentoSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Klarna\Base\Api\BuilderInterface;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\PageCache\Model\Cache\Type;
use Magento\Framework\App\Request\Http;

/**
 * @internal
 */
class ControllerTestCase extends AbstractController
{
    /**
     * @var DataProvider
     */
    public $dataProvider;
    /**
     * @var Parameter
     */
    public $parameter;
    /**
     * @var MagentoSession
     */
    public $session;
    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;
    /**
     * @var BuilderInterface
     */
    public $requestBuilder;
    /**
     * @var PrePurchaseValidator
     */
    public $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->dataProvider = $this->objectManager->get(DataProvider::class);
        $this->session = $this->objectManager->get(MagentoSession::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->validator = $this->objectManager->get(PrePurchaseValidator::class);
    }

    public function sendRequest(array $params, string $uri, string $httpMethod): array
    {
        $request = $this->getRequest();
        $request->setParams($params);
        $request->setContent(json_encode($params));
        $request->setMethod($httpMethod);

        if ($httpMethod !== Http::METHOD_POST) {
            $cacheState = $this->objectManager->get(\Magento\Framework\App\Cache\State::class);
            $cacheState->setEnabled(Type::TYPE_IDENTIFIER, true);
        }

        $this->dispatch($uri);
        return [
            'body' => json_decode($this->getResponse()->getBody(), true),
            'statusCode' => $this->getResponse()->getStatusCode()
        ];
    }
}
