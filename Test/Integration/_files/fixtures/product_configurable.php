<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Framework\Indexer\IndexerRegistry;

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize();

Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/product_varchar_attribute.php');
Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/configurable_attribute.php');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(CategorySetup::class);
$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'test_configurable');
/* Create simple products per each option value*/
/** @var AttributeOptionInterface[] $options */
$options = $attribute->getOptions();

$attributeValues = [];
$attributeSetId = $installer->getAttributeSetId(Product::ENTITY, 'Default');
$associatedProductIds = [];
$idsToReindex = $productIds = [10, 20];
array_shift($options); //remove the first option which is empty

foreach ($options as $option) {
    /** @var $product Product */
    $product = Bootstrap::getObjectManager()->create(Product::class);
    $productId = array_shift($productIds);
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setId($productId)
        ->setAttributeSetId($attributeSetId)
        ->setWebsiteIds([1])
        ->setName('Configurable Option' . $option->getLabel())
        ->setSku('simple_' . $productId)
        ->setPrice(14)
        ->setTestConfigurable($option->getValue())
        ->setVarcharAttribute('varchar' . $productId)
        ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
        ->setStatus(Status::STATUS_ENABLED)
        ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);

    $product = $productRepository->save($product);

    /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
    $stockItem = Bootstrap::getObjectManager()->create(\Magento\CatalogInventory\Model\Stock\Item::class);
    $stockItem->load($productId, 'product_id');

    if (!$stockItem->getProductId()) {
        $stockItem->setProductId($productId);
    }
    $stockItem->setUseConfigManageStock(1);
    $stockItem->setQty(1000);
    $stockItem->setIsQtyDecimal(0);
    $stockItem->setIsInStock(1);
    $stockItem->save();

    $attributeValues[] = [
        'label' => 'test',
        'attribute_id' => $attribute->getId(),
        'value_index' => $option->getValue(),
    ];
    $associatedProductIds[] = $product->getId();

    $stockCheckSql = "SELECT * FROM cataloginventory_stock_status WHERE product_id = " . $product->getId();
    $connection = Bootstrap::getObjectManager()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
    $stockCheckResult = $connection->fetchAll($stockCheckSql);
    if (count($stockCheckResult) === 0) {
        $stockSql = "INSERT INTO cataloginventory_stock_status (product_id, website_id, stock_id, qty, stock_status) " .
            "VALUES (" . $product->getId() . ",0,1,12345,1)";
        $connection->query($stockSql);
    }
}

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);

/** @var Factory $optionsFactory */
$optionsFactory = Bootstrap::getObjectManager()->create(Factory::class);

$configurableAttributesData = [
    [
        'attribute_id' => $attribute->getId(),
        'code' => $attribute->getAttributeCode(),
        'label' => $attribute->getStoreLabel(),
        'position' => '0',
        'values' => $attributeValues,
    ],
];

$configurableOptions = $optionsFactory->create($configurableAttributesData);

$extensionConfigurableAttributes = $product->getExtensionAttributes();
$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
$extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);

$product->setExtensionAttributes($extensionConfigurableAttributes);

// Remove any previously created product with the same id.
/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
try {
    $productToDelete = $productRepository->getById(1);

    /** @var \Magento\Quote\Model\ResourceModel\Quote\Item $itemResource */
    $itemResource = Bootstrap::getObjectManager()->get(\Magento\Quote\Model\ResourceModel\Quote\Item::class);
    $itemResource->getConnection()->delete(
        $itemResource->getMainTable(),
        'product_id = ' . $productToDelete->getId()
    );

    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(IndexerRegistry::class)
        ->get(Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID)
        ->reindexAll();
} catch (\Exception $e) {
    // Nothing to remove
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

$product->setTypeId(Configurable::TYPE_CODE)
    ->setId(9998)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([1])
    ->setName('Configurable Product')
    ->setSku('configurable')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setShortDescription("Short description")
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStockData(
        [
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        ]
    )->setCanSaveCustomOptions(true)
    ->setHasOptions(true);

$product = $productRepository->save($product);

/** @var \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [2]
);

$productHelper = Bootstrap::getObjectManager()->get(\Magento\Catalog\Helper\Product::class);
$productHelper->setSkipSaleableCheck(true);

$stockCheckSql = "SELECT * FROM cataloginventory_stock_status WHERE product_id = " . $product->getId();
$connection = Bootstrap::getObjectManager()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
$stockCheckResult = $connection->fetchAll($stockCheckSql);
if (count($stockCheckResult) === 0) {
    $stockSql = "INSERT INTO cataloginventory_stock_status (product_id, website_id, stock_id, qty, stock_status) " .
        "VALUES (" . $product->getId() . ",0,1,12345,1)";
    $connection->query($stockSql);
}