<?php

class Potoky_AlertAnonymous_Model_Customer extends Mage_Customer_Model_Customer
{
    function _construct()
    {
        if (Mage::registry('potoky_alertanonymous')['parent_construct'] === false) {
            $this->_init('anonymouscustomer/anonymous');
        } else {
            parent::_construct();
        }
    }
}