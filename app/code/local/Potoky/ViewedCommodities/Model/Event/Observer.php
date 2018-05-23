<?php

/**
 * Created by PhpStorm.
 * User: light
 * Date: 5/22/2018
 * Time: 8:27 PM
 */
class Potoky_ViewedCommodities_Model_Event_Observer extends Mage_Reports_Model_Event_Observer
{
    /**
     * Customer login action. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        $_SESSION['customer_log'] = 'In';

        return parent::customerLogin($observer);
    }

    /**
     * Customer logout processing. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        $_SESSION['customer_log'] = 'Out';
    }

}