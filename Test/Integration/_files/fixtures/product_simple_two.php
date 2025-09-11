<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(999999)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product 2')
    ->setSku('simple-1')
    ->setPrice(12)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setUrlKey('simple-product-duplicated')
    ->save();

$productHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Catalog\Helper\Product::class);
$productHelper->setSkipSaleableCheck(true);

$stockCheckSql = "SELECT * FROM cataloginventory_stock_status WHERE product_id = " . $product->getId();
$connection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
$stockCheckResult = $connection->fetchAll($stockCheckSql);
if (count($stockCheckResult) === 0) {
    $stockSql = "INSERT INTO cataloginventory_stock_status (product_id, website_id, stock_id, qty, stock_status) " .
        "VALUES (" . $product->getId() . ",0,1,12345,1)";
    $connection->query($stockSql);
}