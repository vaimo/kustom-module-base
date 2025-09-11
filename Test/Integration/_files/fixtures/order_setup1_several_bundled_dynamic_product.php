<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Quote\Api\Data\AddressInterface;

Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/product_bundled_dynamic.php');
Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php');

$objectManager = Bootstrap::getObjectManager();

$storeManager = $objectManager->get(StoreManagerInterface::class);
$productLoader = $objectManager->get(Product::class);
$formkey = $objectManager->get(FormKey::class);
$quote = $objectManager->get(QuoteFactory::class);
$quoteManagement = $objectManager->get(QuoteManagement::class);
$customerFactory = $objectManager->get(CustomerFactory::class);
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);

$store = $storeManager->getStore();
$websiteId = $storeManager->getStore()->getWebsiteId();
// Start New Sales Order Quote
$quote= $quote->create(); //Create object of quote
$quote->setStore($store); //set store for which you create quote
// Set Sales Order Quote Currency

    $customer = $customerFactory->create();
    $customer->setWebsiteId($websiteId)
        ->setStore($store)
        ->setFirstname('Jhon')
        ->setLastname('Deo')
        ->setEmail('sdfds@sdfsd.de')
        ->setPassword("password");
    $customer->save();
$customer= $customerRepository->getById($customer->getEntityId());
$quote->setGlobalCurrencyCode("USD")
    ->setBaseCurrencyCode("USD")
    ->setStoreCurrencyCode("USD")
    ->setQuoteCurrencyCode("USD");
// Assign Customer To Sales Order Quote
$quote->assignCustomer($customer);

// Configure Notification
$quote->setSendConfirmation(1);

$product = $productLoader->load(12);
$quote->addProduct($product, 2);

$addressData = [
    AddressInterface::KEY_TELEPHONE => '3468676',
    AddressInterface::KEY_POSTCODE => '36104',
    AddressInterface::KEY_COUNTRY_ID => 'US',
    AddressInterface::KEY_CITY => 'CityM',
    AddressInterface::KEY_COMPANY => 'CompanyName',
    AddressInterface::KEY_STREET => 'Green str, 67',
    AddressInterface::KEY_LASTNAME => 'Smith',
    AddressInterface::KEY_FIRSTNAME => 'John',
    AddressInterface::KEY_REGION_ID => 1,
    AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
];

// Set Sales Order Billing Address
$billingAddress = $quote->getBillingAddress()->addData($addressData);

// Set Sales Order Shipping Address
$shippingAddress = $quote->getShippingAddress()->addData($addressData);

// Collect Rates and Set Shipping & Payment Method
$shippingAddress->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('flatrate_flatrate')
    ->setPaymentMethod('checkmo');
$quote->setPaymentMethod('checkmo'); //payment method
$quote->setInventoryProcessed(false); //not effetc inventory
$quote->save(); //Now Save quote and your quote is ready
// Set Sales Order Payment
$quote->getPayment()->importData(array('method' => 'checkmo'));

$quote->setReservedOrderId('100000001');

// Collect Totals & Save Quote
$quote->setTotalsCollectedFlag(false);
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->collectTotals()->save();

// Create Order From Quote
$service = $quoteManagement->submit($quote);
$increment_id = $service->getRealOrderId();