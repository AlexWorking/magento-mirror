<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/18/2018
 * Time: 11:39 AM
 */
class Potoky_ViewedCommodities_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductInfo($products)
    {
        $prodsInfoArr =[];
        foreach ($products as $product) {
            $productSku = trim($product->getSku());
            $prodsInfoArr[$productSku] = [
                'product_url' => $product->getProductUrl(),
                'image_src'   => Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(50, 50)->setWatermarkSize('30x10')->__toString(),
                'image_alt'   => Mage::helper('core')->escapeHtml($product->getName()),
                'name_link'   => Mage::helper('catalog/output')->productAttribute($product, $product->getName() , 'name')
            ];
        }

        return $prodsInfoArr;
    }

    /**
     * Adds needed Java Script to page
     *
     * @param Mage_Core_Model_Layout $layout
     * @param string $cookieVal
     */
    public function addJsVC(Mage_Core_Model_Layout $layout, $cookieVal = null) {
        $layout->getBlock('head')->addJs('local/storage.js');
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'localstorage_rendering',
            array('template' => 'viewedcommodities/storage_execution.phtml'
            ));
        $layout->getBlock('before_body_end')->append($endBlock);
        setcookie('viewed_commodities', $cookieVal, 0,'/');
    }

    /**
     * Checks whether Viewed products will be loaded from JS block.
     * Unsets session variable 'viewed_commodities' if expired.
     *
     * @return bool
     */
    public function isAllowedJsBlock()
    {
        if (!isset($_SESSION['viewed_commodities']) || $_SESSION['viewed_commodities'] - time() < 0) {
            unset($_SESSION['viewed_commodities']);

            return false;
        }
        if (isset($_COOKIE['viewed_commodities'])) {

            return false;
        }

        return true;
    }
}
