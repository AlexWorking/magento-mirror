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
        $this->storageScript();
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
    private function productToJs(Mage_Catalog_Model_Product $product)
    {
        $prodInfoArr= [
            'product_url' => $product->getProductUrl(),
            'image_src'   => Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(50, 50)->setWatermarkSize('30x10')->__toString(),
            'image_alt'   => Mage::helper('core')->escapeHtml($product->getName()),
            'name_link'   => Mage::helper('catalog/output')->productAttribute($product, $product->getName() , 'name')
        ];
        //Just so
        $productSku = trim($product->getSku());
        $prodInfoJson = Mage::helper('core')->jsonEncode($prodInfoArr);
        echo <<<EOP
            <script>
                var viewedList = JSON.parse(localStorage.getItem("viewedCommodities"));
                if (viewedList === null) {
                    viewedList = Object();
                }
                viewedList.$productSku = $prodInfoJson;
                renderStorage("viewedCommodities", viewedList, 3600000);
            </script>
EOP;
    }

    /**
     * Echos the script with the JS function for local storage arrangement.
     *
     * @return void
     */
    private function storageScript() {
        echo <<<EOR
        <script>
            var renderStorage = function(key, jsonValue, lifeTime) {
                localStorage.setItem(key, JSON.stringify(jsonValue));
                setTimeout(function() {
                    localStorage.removeItem(key);  
                }, lifeTime);
            }
        </script>
EOR;

    }
}
