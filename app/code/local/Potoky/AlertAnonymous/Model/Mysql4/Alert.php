<?php

class Potoky_AlertAnonymous_Model_Mysql4_Alert extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('alertanonymous/ungeristered', 'alert_id');
    }
}