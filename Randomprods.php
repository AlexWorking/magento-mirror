<?php
require_once 'app/Mage.php';
$app = Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$default        = 100;
$api            = new Mage_Catalog_Model_Product_Api();
$attr_api       = new Mage_Catalog_Model_Product_Attribute_Set_Api();
$attribute_sets = $attr_api->items();
$prefix    = 'demo.';
$tax_class = 2;
for($x=0; $x<=$default; $x++) {
    $sku       = rand(str_replace(array('.',' '), '', microtime(false)), true);
    $price     = str_replace('0','1', substr($sku, 2, 2)) . '.00';
    $weight    = str_replace('0','1', substr($sku, 6, 2)) . '.00';
    $productData = array();
    $productData['website_ids'] = array(1);
    $productData['categories'] = array(23);
    $productData['status'] = 1;
    $productData['name'] = utf8_encode($sku);
    $productData['description'] = utf8_encode('This is a Demo Description for the Product: Demo ' . $sku);
    $productData['short_description'] = utf8_encode('This is a short Demo Description for the Product: Demo ' . $sku);
    $productData['price'] = $price;
    $productData['weight'] = $weight;
    $productData['tax_class_id'] = $tax_class;
    $new_product_id = $api->create('simple', $attribute_sets[0]['set_id'], $prefix . $sku, $productData);
    $stockItem = Mage::getModel('cataloginventory/stock_item');
    $stockItem->loadByProduct( $new_product_id );
    $stockItem->setData('use_config_manage_stock', 1);
    $stockItem->setData('qty', 100);
    $stockItem->setData('min_qty', 0);
    $stockItem->setData('use_config_min_qty', 1);
    $stockItem->setData('min_sale_qty', 0);
    $stockItem->setData('use_config_max_sale_qty', 1);
    $stockItem->setData('max_sale_qty', 0);
    $stockItem->setData('use_config_max_sale_qty', 1);
    $stockItem->setData('is_qty_decimal', 0);
    $stockItem->setData('backorders', 0);
    $stockItem->setData('notify_stock_qty', 0);
    $stockItem->setData('is_in_stock', 1);
    $stockItem->setData('tax_class_id', 0);
    $stockItem->save();
    $product = Mage::getModel('catalog/product')->load($new_product_id);
    $product->save();
}
