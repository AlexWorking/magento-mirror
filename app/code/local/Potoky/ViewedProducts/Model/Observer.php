<?php

class Potoky_ViewedProducts_Model_Observer
{
    /**
     * Observes the loaded page and ads necessary JS and script if
     * the sessionstorage needs to be clear or reset
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        if (!$_COOKIE['viewed_products'] || $_COOKIE['viewed_products'] === 'updated') {
            return;
        }
        $layout = $observer->getEvent()->getLayout();
        $layout->getBlock('head')->addJs('local/storage.js');
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'sessionstorage_rendering',
            array('template' => 'viewedproducts/process_cookie.phtml',
            ));
        $layout->getBlock('before_body_end')->append($endBlock);
    }
}

