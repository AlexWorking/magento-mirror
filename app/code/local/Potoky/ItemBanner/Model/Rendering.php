<?php

class Potoky_ItemBanner_Model_Rendering extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('itembanner')->__('first banner occupies the position')),
            array('value'=>2, 'label'=>Mage::helper('itembanner')->__('move on to the next position if current is occupied'))
        );
    }
}