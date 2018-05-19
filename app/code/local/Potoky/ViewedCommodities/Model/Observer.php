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
        $blocks = $layout->getAllBlocks();
        foreach ($blocks as $block) {
            if ($block instanceof Mage_Reports_Block_Product_Viewed) {
                $block->setTemplate('viewedcommodities/commodity_viewed.phtml');
                break;
            };
        }
        $layout->getBlock('head')->addJs('local/storage.js');
    }
}
