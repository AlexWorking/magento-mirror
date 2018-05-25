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
     * Observes the loaded page and ads necessary JS and scripts if
     * the localstorage is empty of viewed products.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        if (in_array('catalog_product_view', $layout->getUpdate()->getHandles())) {
            $layout->getBlock('head')->addJs('local/storage.js');
            if (!$layout->getBlock('localstorage_rendering')) {
                Mage::helper('viewedcommodities')->addJsVC($layout, 'gatherer.phtml', 'add');
                Mage::register('vieved_commodity', $this->getProdInfo());
            }
        }
    }

    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedcommodities')
            ->getProductInfo([Mage::registry('current_product')]);
        $sku = array_keys($prodsInfoArr)[0];

        return [
            'sku'         => $sku,
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }
}
