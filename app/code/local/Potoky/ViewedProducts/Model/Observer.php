<?php

class Potoky_ViewedProducts_Model_Observer
{
    /**
     * Observes the loaded page and ads necessary JS and scripts if
     * the localstorage needs a renewal of viewed product or products
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        if (Mage::helper('viewedproducts/lifetime')->getLifetime() == false) {

            return;
        }
        $layout = $observer->getEvent()->getLayout();
        $viewedPresent = (Mage::registry('viewed_block')) ? true : false;
        $viewPresent = in_array('catalog_product_view', $layout->getUpdate()->getHandles());
        if (($viewedPresent || $viewPresent) && !$this->jsGenerationAllowed()) {
            $cookieVal = (isset($_COOKIE['viewed_products'])) ? null : 'reset';
            $this->addJsVC($layout, 'storage_execution.phtml', $cookieVal);
            return;
        }
        elseif ($viewPresent) {
                Mage::register('viewed_product',$this->getProdInfo());
                $this->addJsVC($layout, 'gatherer.phtml', 'add');
        }
        elseif ($viewedPresent) {
            Mage::register('jsblock_allowed', true);
            $this->addJsVC($layout);
        }
    }

    /**
     * Observes if any changes were made concerning Catalog section
     * in System Config and if so provides a reset to data
     * responsive for JS Block forming
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function systemConfigWatch(Varien_Event_Observer $observer)
    {
            Mage::helper('viewedproducts/session')->unsetSessionSetCookieForViewedProducts();
    }

    /**
     * Retrieves necessary information about the product being viewed
     * to be used in JS script that will add it to the "localStorage"
     *
     * @return array
     */
    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedproducts/product')
            ->getProductInfo([Mage::registry('current_product')]);
        $sku = array_keys($prodsInfoArr)[0];

        return [
            'sku'         => $sku,
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }

    /**
     * Adds needed Java Script to a page and creates control cookie
     *
     * @param Mage_Core_Model_Layout $layout
     * @param string $template
     * @param string $cookieVal
     */
    private function addJsVC(Mage_Core_Model_Layout $layout, $template = null, $cookieVal = null) {
        $layout->getBlock('head')->addJs('local/storage.js');

        if (!$template) {
            return;
        }
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'localstorage_rendering',
            array('template' => 'viewedproducts/' . $template,
            ));
        $layout->getBlock('before_body_end')->append($endBlock);

        if (!$cookieVal) {
            return;
        }
        setcookie('viewed_products', $cookieVal, 0,'/');
    }

    /**
     * Checks whether Viewed products may be loaded from JS block.
     * Unsets session variable 'viewed_products' if expired.
     *
     * @return bool
     */
    private function jsGenerationAllowed()
    {
        if (!isset($_SESSION['viewed_products'])) {

            return false;
        }
        if ((int) $_SESSION['viewed_products'] - time() < 0) {
            unset($_SESSION['viewed_products']);

            return false;
        }
        if (isset($_COOKIE['viewed_products'])) {

            return false;
        }

        return true;
    }
}
