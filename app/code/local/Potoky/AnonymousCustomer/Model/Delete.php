<?php

class Potoky_AnonymousCustomer_Model_Delete extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('anonymouscustomer')->__('deleted')),
            array('value'=>2, 'label'=>Mage::helper('anonymouscustomer')->__('created'))
        );
    }

}