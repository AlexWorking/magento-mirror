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
        $this->reactToLog('reset');

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
        $this->reactToLog('clear');

        return parent::customerLogout($observer);
    }

    private function reactToLog($type)
    {
        if (isset($_SESSION['viewed_commodities'])) {
            unset($_SESSION['viewed_commodities']);
        }
        Mage::helper('vievedcommodities')->addJsVC(Mage::app()->getLayout(), $type);

    }

}