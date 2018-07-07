<?php
/**
 * Created by PhpStorm.
 * User: light
 * Date: 7/7/2018
 * Time: 12:33 PM
 */

class Potoky_AnonymousCustomer_Model_Anonymous extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('anonymouscustomer/anonymous');
    }
}