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
            if (!isset($_COOKIE['viewedcommodities'])) {
                if (in_array('catalog_product_view', $layout->getUpdate()->getHandles())) {
                    $layout->unsetBlock('product_data_gatherer');
                } else {
                    $layout->getBlock('head')->addJs('local/storage.js');
                }
                $endBlock = $layout->createBlock(
                    'Mage_Core_Block_Template',
                    'localstorage_rendering',
                    array('template' => 'viewedcommodities/storage_execution.phtml'
                    ));
                $layout->getBlock('before_body_end')->append($endBlock);
            }
    }
}
