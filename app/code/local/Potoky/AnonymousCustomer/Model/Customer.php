<?php

class Potoky_AnonymousCustomer_Model_Customer extends Mage_Customer_Model_Customer
{
    public static $createAnonymous = false;

    function _construct()
    {
        if (self::$createAnonymous) {
            $this->_init('anonymouscustomer/anonymous');
        } else {
            parent::_construct();
        }
    }

    public static function setCreateAnonymous($yesno)
    {
        self::$createAnonymous = $yesno;
    }

    public static function getCreateAnonymous()
    {
        return self::$createAnonymous;
    }
}