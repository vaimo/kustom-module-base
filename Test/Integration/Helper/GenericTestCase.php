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
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class GenericTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataProvider
     */
    public $dataProvider;
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
     * @var PrePurchaseValidator
     */
    public $prePurchaseValidator;
    /**
     * @var PostPurchaseValidator
     */
    public $postPurchaseValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->dataProvider = $this->objectManager->get(DataProvider::class);
        $this->session = $this->objectManager->get(MagentoSession::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->prePurchaseValidator = $this->objectManager->get(PrePurchaseValidator::class);
        $this->postPurchaseValidator = $this->objectManager->get(PostPurchaseValidator::class);
    }
}
