<?php

class Potoky_AlertAnonymous_Model_Customer extends Mage_Customer_Model_Customer
{
    public static $helpers = [];

    function _construct()
    {
        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $this->_init('anonymouscustomer/anonymous');
        } else {
            parent::_construct();
        }
    }

    public function getName()
    {
        $name = parent::getName();
        if(self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $name = 'Dear Customer';
        }

        return $name;
    }
}