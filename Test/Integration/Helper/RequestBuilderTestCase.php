<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Klarna\Backend\Model\Api\Builder;
use Klarna\Kp\Model\Api\Builder\Request;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\CreditmemoFactory;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @internal
 */
class RequestBuilderTestCase extends GenericTestCase
{
    /**
     * @var Request
     */
    public $requestBuilder;

    public $invoiceCollection;

    public $creditmemoCollection;

    public $searchCriteriaBuilder;

    public $orderRepository;
    /**
     * @var Builder
     */
    public $orderManagementBuilder;

    public $creditmemoFactory;
    /**
     * @var QuotePreparer
     */
    public $quotePreparer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestBuilder = $this->objectManager->get(Request::class);
        $this->invoiceCollection = $this->objectManager->get(InvoiceCollectionFactory::class);
        $this->creditmemoCollection = $this->objectManager->get(CreditmemoCollectionFactory::class);
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        $this->orderRepository = $this->objectManager->get(OrderRepository::class);
        $this->orderManagementBuilder = $this->objectManager->get(Builder::class);
        $this->creditmemoFactory = $this->objectManager->get(CreditmemoFactory::class);
        $this->quotePreparer = $this->objectManager->get(QuotePreparer::class);
    }

    protected function getPlaceOrderRequest($quote, string $currency, AddressInterface $address, string $shippingMethod = '')
    {
        $this->quotePreparer->configureQuote($quote, $currency, $address, $address, $shippingMethod);
        $this->quotePreparer->saveQuote($quote);

        return $this
            ->requestBuilder
            ->generatePlaceOrderRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    protected function getCreateSessionRequest($quote, string $currency, AddressInterface $address, string $shippingMethod = ''): array
    {
        $this->quotePreparer->configureQuote($quote, $currency, $address, $address, $shippingMethod);
        $this->quotePreparer->saveQuote($quote);

        return $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, 'a-random-auth-callback-token')
            ->toArray();
    }

    protected function getCreateSessionRequestNoAuthToken($quote, string $currency, AddressInterface $address, string $shippingMethod = ''): array
    {
        $this->quotePreparer->configureQuote($quote, $currency, $address, $address, $shippingMethod);
        $this->quotePreparer->saveQuote($quote);

        return $this
            ->requestBuilder
            ->generateCreateSessionRequest($quote, null)
            ->toArray();
    }

    protected function getCaptureRequest(float $amount, InvoiceInterface $invoice): array
    {
        return $this->orderManagementBuilder->getCaptureRequest($amount, $invoice);
    }

    protected function getRefundRequest(float $amount, $creditmemo)
    {
        return $this->orderManagementBuilder->getRefundRequest($amount, $creditmemo);
    }

    protected function getInvoiceByOrder($order): InvoiceInterface
    {
        return $this->invoiceCollection->create()->setOrderFilter($order)->setPageSize(1)->getFirstItem();
    }

    protected function getCreditmemoByOrder($order)
    {
        return $this->creditmemoCollection->create()->setOrderFilter($order)->setPageSize(1)->getFirstItem();
    }

    protected function getOrder(string $incrementalId): OrderInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $incrementalId)
            ->create();
        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        return reset($orders);
    }

    protected function getInvoiceByOrderIncrementId(string $incrementId): InvoiceInterface
    {
        return $this->getInvoiceByOrder($this->getOrder($incrementId));
    }

    protected function getCreditMemoByOrderIncrementId(string $incrementId)
    {
        $invoice = $this->getInvoiceByOrderIncrementId($incrementId);
        return $this->creditmemoFactory->createByInvoice($invoice);
    }
}
