<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static $helpers = [];

    public function setupHelpers()
    {
        self::$helpers['data'] = $this;
        self::$helpers['allow'] = Mage::helper('alertanonymous/allow');
        self::$helpers['login'] = Mage::helper('alertanonymous/login');
        self::$helpers['registry'] = Mage::helper('alertanonymous/registry');
        self::$helpers['data_1'] = Mage::helper('core');
        self::$helpers['data_2'] = Mage::helper('anonymouscustomer');
        self::$helpers['entity'] = Mage::helper('anonymouscustomer/entity');

        Potoky_AlertAnonymous_AddController::$helpers = self::$helpers;
        Potoky_AlertAnonymous_UnsubscribeController::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Email_Template::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Customer::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Email::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Observer::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Model_Price::$helpers = self::$helpers;
        Potoky_AlertAnonymous_Block_Product_View::$helpers = self::$helpers;
    }
}