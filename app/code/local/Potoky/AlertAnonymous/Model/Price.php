<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    public static $lastId;

    protected function _construct()
    {
        if(Mage::registry('potoky_alertanonymous') === 'anonymouscustomer_create') {
            $this->_init('alertanonymous/price');
        } else {
            parent::_construct();
        }
    }

    public function setCustomerId(int $value = null)
    {
        if(Mage::registry('potoky_alertanonymous') === 'anonymouscustomer_create') {
            $value = self::$lastId;
        }

        return parent::setCustomerId($value);
    }
}