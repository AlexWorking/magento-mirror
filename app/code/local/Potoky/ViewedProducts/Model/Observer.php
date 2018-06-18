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
        $layout = $observer->getEvent()->getLayout();
        $test = Mage::getStoreConfig("catalog/js_viewed_products/allow_jsblock");
    }

    /**
     * Observes if any changes were made concerning Catalog section
     * in System Config and if so provides rewrites its timestamp field anew
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function systemConfigWatch(Varien_Event_Observer $observer)
    {
        Mage::getModel('core/config')->saveConfig('catalog/js_viewed_products/timestamp', time());
    }
}

