<?php

class Potoky_AlertAnonymous_Model_Mysql4_Price_Collection extends Mage_ProductAlert_Model_Mysql4_Price_Collection {
    protected function _construct()
    {
        if(Mage::registry('potoky_alertanonymous') === 'anonymouscustomer_create') {
            $this->_init('alertanonymous/price');
        } else {
            parent::_construct();
        }
    }
}