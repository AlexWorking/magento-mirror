<?php

class Potoky_AlertAnonymous_Model_Price extends Mage_ProductAlert_Model_Price
{
    protected function _construct()
    {
        $this->_init('alertanonymous/alert');
    }
}