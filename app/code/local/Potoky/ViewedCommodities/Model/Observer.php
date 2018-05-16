<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/14/2018
 * Time: 11:00 AM
 */
class Potoky_ViewedCommodities_Model_Observer
{
    /**
     * Observes the loaded page and redirects the script to corresponding methods.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        if (in_array('catalog_product_view', $layout->getUpdate()->getHandles())) {
            $this->productToJs(Mage::registry('current_product'));
            return;
        }
        $blocks = $layout->getAllBlocks();
        foreach ($blocks as $block) {
            if ($block instanceof Mage_Reports_Block_Product_Viewed) {
                $block->setTemplate('viewedcommodities/commodity_viewed.phtml');
                break;
            };
        }
    }

    /**
     * Converts product data to JSON and stores it to JS Local Storage.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function productToJs(Mage_Catalog_Model_Product $product)
    {
        $prodInfoArr= [
            'product_url' => $product->getProductUrl(),
            'image_src'   => Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(50, 50)->setWatermarkSize('30x10')->__toString(),
            'image_alt'   => Mage::helper('core')->escapeHtml($product->getName()),
            'name_link'   => Mage::helper('catalog/output')->productAttribute($product, $product->getName() , 'name')
        ];
        //Just so
        $productSku = $product->getSku();
        $prodInfoJson = Mage::helper('core')->jsonEncode($prodInfoArr);
        echo <<<EOP
            <script>
            var viewedList = JSON.parse(localStorage.getItem("viewedCommodities"));
            if (viewedList === null) {
                viewedList = Object();
            }
            viewedList.$productSku = $prodInfoJson;
            localStorage.setItem("viewedCommodities", JSON.stringify(viewedList));
            </script>
EOP;
    }
}
