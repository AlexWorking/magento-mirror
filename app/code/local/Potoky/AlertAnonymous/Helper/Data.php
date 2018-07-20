<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static $helpers = [];

    public function setHelpers()
    {
        self::$helpers['data'] = $this;
        self::$helpers['allow'] = Mage::helper('alertanonymous/allow');
        self::$helpers['login'] = Mage::helper('alertanonymous/login');
        self::$helpers['registry'] = Mage::helper('alertanonymous/registry');
        self::$helpers['data_1'] = Mage::helper('anonymouscustomer');
        self::$helpers['entity'] = Mage::helper('anonymouscustomer/entity');

        Potoky_AlertAnonymous_AddController::$helpers = self::$helpers;
    }
}