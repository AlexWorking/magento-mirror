<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/18/2018
 * Time: 11:39 AM
 */
class Potoky_ViewedCommodities_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getViewedJson ($products)
    {
        $prodsInfoArr = [];
        foreach ($products as $product) {
            $productSku = trim($product->getSku());
            $prodsInfoArr[$productSku] = [
                'product_url' => $product->getProductUrl(),
                'image_src'   => Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(50, 50)->setWatermarkSize('30x10')->__toString(),
                'image_alt'   => Mage::helper('core')->escapeHtml($product->getName()),
                'name_link'   => Mage::helper('catalog/output')->productAttribute($product, $product->getName() , 'name')
            ];
        }

        return $prodsInfoJson = Mage::helper('core')->jsonEncode($prodsInfoArr);
    }
}
