<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    protected function _construct()
    {
        $registry = (Mage::registry('potoky_alertanonymous')) ? Mage::registry('potoky_alertanonymous') : null;

        if ($registry === null || $registry['parent_construct'] === true) {
            parent::_construct();
        } else {
            $this->_init('alertanonymous/price');
        }
    }

    public function setCustomerId(int $value)
    {
        if($value == null) {
            $value =  Mage::registry('potoky_alertanonymous')['id'];
        }

        return parent::setCustomerId($value);
    }
}