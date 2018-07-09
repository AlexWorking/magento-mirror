<?php

class Potoky_AlertAnonymous_Model_Mysql4_Price extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('alertanonymous/price', 'alert_price_id');
    }
}