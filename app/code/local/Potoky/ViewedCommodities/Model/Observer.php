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
        if (true == $_SESSION['customer_log']) {
            unset($_COOKIE['viewed_commodities']);
            setcookie('viewed_commodities', null, time() - 1000, '/');
            unset($_SESSION['customer_log']);
        }
        if (!isset($_COOKIE['viewed_commodities'])) {
            if ($layout->getBlock('customer_logout')) {
                return;
            }
            if (in_array('catalog_product_view', $layout->getUpdate()->getHandles())) {
                $getherer = $layout->getBlock('product_data_gatherer')
                    ->setTemplate('viewedcommodities/storage_execution.phtml');
            } else {
                $layout->getBlock('head')->addJs('local/storage.js');
                $endBlock = $layout->createBlock(
                    'Mage_Core_Block_Template',
                    'localstorage_rendering',
                    array('template' => 'viewedcommodities/storage_execution.phtml'
                    ));
                $layout->getBlock('before_body_end')->append($endBlock);
            }
        }
    }

    /**
     * Observes for the event of customer having been logged in or registered.
     *
     *@return void
     */
    public function customerLogin()
    {
       $_SESSION['customer_log'] = 'In';
    }

    /**
     * Observes for the event of customer having been logged out.
     *
     *@return void
     */
    public function customerLogout()
    {
       $_SESSION['customer_log'] = 'Out';
    }
}
