<?php

class Potoky_ViewedProducts_Model_Observer
{
    /**
     * Observes the loaded page and ads necessary JS and scripts if
     * the sessionstorage needs a renewal of viewed product or products
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('viewedproducts/lifetime')->getLifetime() || !$_COOKIE['viewed_products']) {
            return;
        }
        $layout = $observer->getEvent()->getLayout();
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'sessionstorage_rendering',
            array('template' => 'viewedproducts/process_cookie.phtml',
            ));
        $layout->getBlock('before_body_end')->append($endBlock);
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

